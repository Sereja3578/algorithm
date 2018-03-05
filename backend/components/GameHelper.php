<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 15.02.2018
 * Time: 15:16
 */

namespace backend\components;


use common\models\AlgorithmParams;
use common\models\Game;
use common\models\Strategy;
use Faker\Provider\DateTime;
use yii\boost\base\InvalidModelException;

class GameHelper
{

    /**
     * @message Экспирация шага
     */
    const EXPIRATION = 5;

    /**
     * @message Игра выиграна
     */
    const GAME_WIN = 1;

    /**
     * @message Игра проиграна
     */
    const GAME_FAILED = 0;

    /**
     * @var AlgorithmParams
     */
    public $algorithmParamsModel;

    /**
     * @var bool
     */
    public $firstGamePlayed = false;
    /**
     * @var int
     */
    public $iterationNumber;

    /**
     * @var array
     */
    public $currentStrategyParams;

    /**
     * @var array
     */
    public $bestStrategyParams;

    /**
     * @var int
     */
    public $checkMoneyNumberStep = 1;

    /**
     * @var array
     */
    public $preparedResultGameSteps;

    /**
     * GameHelper constructor.
     * @param $algorithmParamsModel
     */
    function __construct(AlgorithmParams $algorithmParamsModel)
    {
        $this->algorithmParamsModel = $algorithmParamsModel;
    }

    /**
     * @param $iterationNumber
     * @return bool|Strategy
     */
    public function playerSimulation($iterationNumber)
    {
        var_dump('Algorithm started to simulate user activity and current time is ' . AlgorithmTimer::getCurrentTime());

        var_dump('first game was played: ' . $this->firstGamePlayed);

        var_dump("Current time is: " . AlgorithmTimer::getCurrentTime() . "End time is: " . AlgorithmTimer::getEndTime());

        if(!AlgorithmTimer::checkTime()) {
            return false;
        }

        var_dump(AlgorithmTimer::getCurrentTime() . " < " . AlgorithmTimer::getEndTime());

        $algorithmParamsModel = $this->algorithmParamsModel;
        $this->setIterationNumber($iterationNumber);

        /*
        * Если не прошел проверку, то сохраняем стратегию и выходим из игры,
        * при этом игра считается проигранной.
        */
        if (!$this->checkMoney($this->getCheckMoneyNumberStep())) {
            var_dump('The game: ' . $this->currentStrategyParams['game_id'] . ' was finished with result');

            var_dump($this->currentStrategyParams);

            return $this->getBestStrategy($this->currentStrategyParams);
        }

        var_dump('User has gone ' . $this->getCheckMoneyNumberStep() . ' money check');

        $this->decreaseLucky();

        // Тут сравниваем текущую стратегию после проигрыша или выигрыша с лучшей стратегией
        if($this->firstGamePlayed) {
            if($this->checkStrategy()){
                $strategyParams = $this->getCurrentStrategyParams();
                $this->setBestStrategyParams($strategyParams);

                var_dump('The game: ' . $strategyParams['game_id'] . ' was finished without result');
                var_dump('But we have new best strategy: ', $this->currentStrategyParams);
            };
        }

        if (!$this->checkMoney($this->getNexCheckMoneyNumberStep())) {
            return false;
        }

        var_dump('User has gone ' . $this->getCheckMoneyNumberStep() . ' money check');

        if ($this->playGameOrNotPlay()) {

            var_dump('User decided to play in game');

            $gameId = $this->chooseGameByChance($this->getGamesAsArray());
            $game = $this->getGame($gameId);

            var_dump('User has choosen game: ' . $gameId);

            if ($game) {
                $result = $this->startGame($game);
                if(!$result) {
                    return false;
                } elseif ($result instanceof Strategy) {
                    return $result;
                }
                $this->firstGamePlayed = true;
            }
        }

        // Следующая игра начинается через заданное время (как и везде, время только имитируется)
        AlgorithmTimer::incrementCurrentTime($algorithmParamsModel->t_next_start_game);
        // Сбрасываем номер этапа вроверки денег
        $this->resetCheckMoneyNumberStep();
        /*
         * Функция будет выполняться до тех пор, пока не будет достигнут результат
         * или не кончится время.
         */
        return $this->playerSimulation($iterationNumber);
    }

    /**
     * Игра начинается с выбора ставки.
     * Затем текущее количество денег уменьшается на размер выбранной ставки.
     * Проверяется оставшееся колличество денег,
     * если оно больше чем money_end, то проверяем
     *
     * @param $game
     * @return bool|Strategy
     */
    public function startGame($game)
    {

        var_dump('User started to play in the game');

        $algorithmParamsModel = $this->algorithmParamsModel;
        $rate = $this->chooseRate();
        var_dump('User has chosen rate: ' . $rate);
        $algorithmParamsModel->amount_start -= $rate;

        /*
        * Имитируем работу игры.
        * Увеличиваем текущее время на число шагов умноженное на время экспирации.
        */
        AlgorithmTimer::incrementCurrentTime($game->number_steps * self::EXPIRATION);

        if(!AlgorithmTimer::checkTime()) {
            return false;
        }

        var_dump('Here was imitated play activity and current time now is: ' . AlgorithmTimer::getCurrentTime());

        /*
         * Если не прошел проверку, то сохраняем стратегию и выходим из игры,
         * при этом игра считается проигранной.
         */
        if (!$this->checkMoney($this->getNexCheckMoneyNumberStep())) {

            var_dump('The game: ' . $game->id . ' was finished with result');

            return $this->getBestStrategy(null, $rate, $game);
        }

        var_dump('User has gone ' . $this->getCheckMoneyNumberStep() . ' money check');

        if($this->firstGamePlayed) {
            if($this->checkStrategy()){
                $forecast = $this->getForecast(self::GAME_FAILED, $game);
                $strategyParams = [
                    'timestamp' => AlgorithmTimer::getCurrentTimestamp(),
                    'iteration_number' => $this->iterationNumber,
                    'money_amount' => $algorithmParamsModel->amount_start,
                    'game_id' => $game->id,
                    'rate_amount' => $rate,
                    'forecast' => $forecast,
                    'result' => self::GAME_FAILED,
                    'best_strategy' => Strategy::BEST_STRATEGY,
                ];
                $this->setBestStrategyParams($strategyParams);

                var_dump('The game: ' . $game->id . ' was finished without result');
                var_dump('But we have new best strategy: ', $this->bestStrategyParams);

                return true;
            };
        }

        var_dump('User has not gone check strategy, so we just continue to play');

        $gameResult = $this->isWin();

        // Если победил, увеличиваем деньги игрока
        if ($gameResult) {
            $winCoef = WinCoefHelper::getCurrentCoef(AlgorithmTimer::getCurrentTimestamp());
            $algorithmParamsModel->amount_start += $rate * $winCoef;
        }

        $forecast = $this->getForecast($gameResult, $game);
        $currentStrategyParams = [
            'timestamp' => AlgorithmTimer::getCurrentTimestamp(),
            'iteration_number' => $this->iterationNumber,
            'money_amount' => $algorithmParamsModel->amount_start,
            'game_id' => $game->id,
            'rate_amount' => $rate,
            'forecast' => $forecast,
            'result' => $gameResult ? self::GAME_WIN : self::GAME_FAILED,
            'best_strategy' => Strategy::BEST_STRATEGY,
        ];

        if($this->firstGamePlayed) {
            $this->setCurrentStrategyParams($currentStrategyParams);
        } else {
            // При первой игре текущие параметры являются и лучшими
            $this->setCurrentStrategyParams($currentStrategyParams);
            $this->setBestStrategyParams($currentStrategyParams);
        }

        var_dump('User finished to play game: ' . $game->id . ' was finished without result');
        var_dump('But we have new current strategy: ', $this->currentStrategyParams);

        return true;
    }

    /**
     * @param array $currentStrategyParams
     * @param float $rate
     * @param Game $game
     * @return Strategy
     */
    public function getBestStrategy($currentStrategyParams = null, $rate = null, $game = null) {

        if($currentStrategyParams) {
            var_dump('Strategy was got by 1 variant and strategy is: ', $currentStrategyParams);
            return new Strategy($currentStrategyParams);
        } else {
            $algorithmParamsModel = $this->algorithmParamsModel;
            $forecast = $this->getForecast(self::GAME_FAILED, $game);

            $strategyParams = [
                'timestamp' => AlgorithmTimer::getCurrentTimestamp(),
                'iteration_number' => $this->iterationNumber,
                'money_amount' => $algorithmParamsModel->amount_start,
                'game_id' => $game->id,
                'rate_amount' => $rate,
                'forecast' => $forecast,
                'result' => self::GAME_FAILED,
                'best_strategy' => Strategy::BEST_STRATEGY,
            ];
            var_dump('Strategy was got by 2 variant');
            return new Strategy($strategyParams);
        }
    }

    /**
     * @return array
     */
    public function getBestStrategyParams(): array
    {
        return $this->bestStrategyParams;
    }

    /**
     * @param array $bestStrategyParams
     */
    public function setBestStrategyParams(array $bestStrategyParams): void
    {
        $this->bestStrategyParams = $bestStrategyParams;
    }

    public function checkStrategy()
    {
        $algorithmParamsModel = $this->algorithmParamsModel;
        $currentMoney = $algorithmParamsModel->amount_start;
        $endMoney = $algorithmParamsModel->amount_end;

        /*
         * Для приближения сверху, стратегия будет верна, если текущее число станет меньше чем предыдущее.
         * Таким образом получится, что если конечная сумма должна быть 70, текущее число днеге
         * после ставки стало 72, а до ставки было 73, то мы приблизились ближе к цели,
         * и эта стратегия лучше. Но так как 70 - 72 больше чем 70 - 73,
         * то нужно сравнивать по модулю.
         */
        $currentApproximation = ($endMoney - $currentMoney > 0) ? $endMoney - $currentMoney : ($endMoney - $currentMoney) * -1 ;
        $previewApproximation = ($endMoney - $this->bestStrategyParams['money_amount'] > 0) ?
            $endMoney - $this->bestStrategyParams['money_amount'] :
            ($endMoney - $this->bestStrategyParams['money_amount']) * -1 ;

        var_dump('current approximation: ' . $currentApproximation);
        var_dump('preview approximation: ' . $previewApproximation);

        if ($currentApproximation < $previewApproximation) {
            var_dump('The check of strategy was completed');
            return true;
        }
        var_dump('The check of strategy was not completed');
        return false;
    }

    /**
     * @param Strategy $strategy
     * @throws InvalidModelException
     */
    public function saveStrategy(Strategy $strategy)
    {
        var_dump('Save best strategy: ', $strategy);
        $strategy->hardSave();
    }

    /**
     * @param array $games
     * @return int|null|string
     */
    public function chooseGameByChance(array $games)
    {
        $rand = mt_rand(1, 100);

        $segment = 0;
        foreach ($games as $gameId => $chance) {
            $segment += $chance;
            if ($rand <= $segment) {
                return $gameId;
            }
        }

        return null;
    }

    /**
     * Выбираем ставку по принципу:
     * Выбираем максимальные по значению ставки.
     * Максимум number_rates ставок. Проверяем,
     * соответствует ли хотябы одна из ставок критерию -
     * текущее количество денег больше rate_coef * эту ставку.
     * Выбираем первую подходящую по критерию ставку,
     * если такой ставки нет, выбираем из всех ставок минимальную.
     *
     * @return float|int
     */
    public function chooseRate()
    {
        $algorithmParamsModel = $this->algorithmParamsModel;
        $rates = $this->getRatesAsArray();
        array_multisort($rates, SORT_DESC, SORT_NUMERIC);
        $maxRates = array_slice($rates,0, $algorithmParamsModel->number_rates);

        foreach ($maxRates as $maxRate) {
            if ($algorithmParamsModel->amount_start > $algorithmParamsModel->rate_coef * $maxRate) {
                return $maxRate;
            }
        }

        return min($this->getRatesAsArray());
    }

    public function getGame($gameId)
    {
        return Game::findOne(['id' => $gameId]);
    }

    public function setPreparedResultGameSteps(array $quotes)
    {
        $preparedResultGameSteps = [];
        foreach ($quotes as $key => $quote) {
            // Стартовая котировка
            if ($key == 0) {
                continue;
            }

            if ($quotes[$key - 1]['ask'] > $quote['ask']) {
                $preparedResultGameSteps[$quote['timestamp']] = QuoteHelper::QUOTE_DECREASE;
            } elseif ($quotes[$key - 1]['ask'] < $quote['ask']) {
                $preparedResultGameSteps[$quote['timestamp']] = QuoteHelper::QUOTE_INCREASE;
            } elseif ($quotes[$key - 1]['ask'] == $quote['ask']) {
                $preparedResultGameSteps[$quote['timestamp']] = QuoteHelper::QUOTE_NOT_CHANGED;
            }
        }

        $this->preparedResultGameSteps = $preparedResultGameSteps;
    }

    public function getPreparedResultGameSteps(): array
    {
        return $this->preparedResultGameSteps;
    }

    /**
     * Чтобы получить заранее определенные
     *
     * @param int $gameResult
     * @param Game $game
     * @return array
     */
    public function getForecast($gameResult, $game): array
    {
        $preparedResultGameSteps = $this->getPreparedResultGameSteps();

        $forecast = [];
        $approximateQuoteTime = '';
        for ($numberStep = 1; $numberStep <= $game->number_steps; $numberStep++) {
           if($numberStep == 1) {
               $currentStepForecast = $this->getCurrentStepForecast(AlgorithmTimer::getCurrentTimestamp(), $preparedResultGameSteps);
               $approximateQuoteTime = current(array_keys($currentStepForecast));
               $forecast[] = $currentStepForecast[$approximateQuoteTime];
           } else {
               $previewStepTime = $approximateQuoteTime - self::EXPIRATION;
               $currentStepForecast = $this->getCurrentStepForecast($previewStepTime, $preparedResultGameSteps);
               $approximateQuoteTime = current(array_keys($currentStepForecast));
               $forecast[] = $currentStepForecast[$approximateQuoteTime];
           }
        }

        switch ($gameResult){
            case self::GAME_WIN :
                return array_reverse($forecast);
            case self::GAME_FAILED :
                shuffle($forecast);
                return $forecast;
        }
    }

    /**
     * @param string|integer $currentDate
     * @param array $preparedResultGameSteps
     * @return array|null
     */
    public function getCurrentStepForecast($currentDate, $preparedResultGameSteps)
    {
        if(!is_int($currentDate)) {
            $currentDate = strtotime($currentDate);
        }

        $items = [];
        foreach ($preparedResultGameSteps as $time => $stepResult) {
            $timestamp = strtotime($time);
            if ($timestamp >= $currentDate) {
                $items[$timestamp] =  $stepResult;
            }
        }

        /*
         * Так как прогноза точно на текущее время может не быть,
         * выбираем максимально приближенный по времени прогноз
         *
         */
        if (!empty($items)) {
            $minKey = min(array_keys($items));

            return [$minKey => $items[$minKey]];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function playGameOrNotPlay()
    {
        $rand = mt_rand(0, 10) / 10;

        if ($rand >= $this->algorithmParamsModel->probability_play) {
            return true;
        }

        AlgorithmTimer::incrementCurrentTime(1);
        return $this->playGameOrNotPlay();
    }

    /**
     * @param int $numberStepOfCheck
     * @return bool
     */
    public function checkMoney(int $numberStepOfCheck)
    {
        $algorithmParamsModel = $this->algorithmParamsModel;
        $amountStart = $algorithmParamsModel->amount_start;
        $amountEnd = $algorithmParamsModel->amount_end;

        switch ($numberStepOfCheck) {
            case 1 :
                if ($algorithmParamsModel->amount_start > $algorithmParamsModel->amount_end) {
                    $deviation = $amountEnd + $algorithmParamsModel->deviation_from_amount_end / 100 * $amountEnd;
                    if ($amountStart >= $amountEnd && $amountStart <= $deviation) {
                        return false;
                    }
                }
                return true;
            case 2 :
                if ($algorithmParamsModel->amount_start < min($this->getRatesAsArray())) {
                    return false;
                } elseif ($algorithmParamsModel->amount_start < $algorithmParamsModel->amount_end) {
                    $this->increaseLucky();
                }
                return true;
            case 3 :
                if ($algorithmParamsModel->amount_start > $algorithmParamsModel->amount_end) {
                    $deviation = $amountEnd + $algorithmParamsModel->deviation_from_amount_end / 100 * $amountEnd;
                    if ($amountStart >= $amountEnd && $amountStart <= $deviation) {
                        return false;
                    }
                }
                return true;
        }

        return true;
    }

    /**
     * Увеличивает коэффициент удачливости игрока вдвое
     */
    public function increaseLucky(){
        var_dump('k_lucky before increase: ' . $this->algorithmParamsModel->k_lucky);
        $this->algorithmParamsModel->k_lucky *= 2;
        var_dump('k_lucky after increase: ' . $this->algorithmParamsModel->k_lucky);
    }

    /**
     * Уменьшает коэффициент удачливости игрока вдвое
     */
    public function decreaseLucky(){
        if ($this->algorithmParamsModel->k_lucky > 1) {
            var_dump('k_lucky before decrease: ' . $this->algorithmParamsModel->k_lucky);
            $this->algorithmParamsModel->k_lucky /= 2;
            var_dump('k_lucky after derease: ' . $this->algorithmParamsModel->k_lucky);
        }
    }

    /**
     * @return bool
     */
    public function isFirstGamePlayed(): bool
    {
        return $this->firstGamePlayed;
    }

    /**
     * @param bool $firstGamePlayed
     */
    public function setFirstGamePlayed(bool $firstGamePlayed)
    {
        $this->firstGamePlayed = $firstGamePlayed;
    }

    /**
     * @return bool
     */
    public function isWin(): bool
    {
        $winCoef = WinCoefHelper::getCurrentCoef(AlgorithmTimer::getCurrentTimestamp());
        $rand = mt_rand(0, 10) / 10;

        var_dump('winCoef: ' . $winCoef, 'rand: ' . $rand, 'k_lucky: ' . $this->algorithmParamsModel->k_lucky);

        if ($rand > 0 && $rand <= 1 / $winCoef * $this->algorithmParamsModel->k_lucky) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getGamesAsArray(): array
    {
        return unserialize($this->algorithmParamsModel->games);
    }

    /**
     * @return array
     */
    public function getRatesAsArray(): array
    {
        return explode(', ', $this->algorithmParamsModel->rates);
    }

    /**
     * @param int $iterationNumber
     */
    public function setIterationNumber(int $iterationNumber)
    {
        $this->iterationNumber = $iterationNumber;
    }

    /**
     * @return array
     */
    public function getCurrentStrategyParams(): array
    {
        return $this->currentStrategyParams;
    }

    /**
     * @param array $currentStrategyParams
     */
    public function setCurrentStrategyParams(array $currentStrategyParams)
    {
        $this->currentStrategyParams = $currentStrategyParams;
    }

    /**
     * @return int
     */
    public function getCheckMoneyNumberStep(): int
    {
        return $this->checkMoneyNumberStep;
    }

    /**
     * Увеличивает номер этапа проверки денег на 1
     */
    public function getNexCheckMoneyNumberStep()
    {
        return $this->checkMoneyNumberStep += 1;
    }

    /**
     * Сбрасывает номер этапа проверки денег
     */
    public function resetCheckMoneyNumberStep() {
        $this->checkMoneyNumberStep = 1;
    }
}