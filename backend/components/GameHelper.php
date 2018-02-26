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

    public function playerSimulation($iterationNumber)
    {
        $this->setIterationNumber($iterationNumber);
        $algorithmParamsModel = $this->algorithmParamsModel;

        if (!$this->checkMoney($this->getCheckMoneyNumberStep())) {
            return false;
        }

//        if($this->isFirstGamePlayed()) {
//            $this->checkStrategy();
//        }

        if (!$this->checkMoney($this->getIncrementedCheckMoneyNumberStep())) {
            return false;
        }

        if ($this->playGameOrNotPlay()) {
            $gameId = $this->chooseGameByChance(unserialize($algorithmParamsModel->games));
            $game = $this->getGame($gameId);

            if ($game) {
                if(!$this->startGame($game)) {
                    return false;
                }
            }

        }

        return true;
    }

    /**
     * Игра начинается с выбора ставки.
     * Затем текущее количество денег уменьшается на размер выбранной ставки.
     * Проверяется оставшееся колличество денег,
     * если оно больше чем money_end, то проверяем
     *
     * @param $game
     * @return bool
     */
    public function startGame($game)
    {
        $algorithmParamsModel = $this->algorithmParamsModel;
        $rate = $this->chooseRate();
        $algorithmParamsModel->amount_start -= $rate;

        /*
        * Имитируем работу игры.
        * Увеличиваем текущее время на число шагов умноженное на время экспирации.
        */
        AlgorithmTimer::incrementCurrentTime($game->number_steps * self::EXPIRATION);

        // @todo реализовать определение результата игры
        // Теперь решаем выиграл игрок или проиграл.

        // @todo получить прогноз.
        $forecast = $this->getForecast(1, $game);

        // После того, как игра сыграна, устанавливаем параметры текущей стратегии
//        $currentStrategyParams = [
//            'algorithm_params_id' => $algorithmParamsModel->id,
//            'timestamp' => AlgorithmTimer::getCurrentTime(),
//            'iteration_number' => $this->iterationNumber,
//            'money_amount' => $algorithmParamsModel->amount_start,
//            'game_id' => $game->id,
//            'rate_amount' => ,
//            'forecast' => ,
//            'result' => ,
//            'best_strategy' => ,
//        ];
//        $this->setCurrentStrategyParams($currentStrategyParams);

        // Проверяем деньги и действуем исходя из результата-проверки
        if (!$this->checkMoney($this->getIncrementedCheckMoneyNumberStep())) {
            return false;
        }

        // А тут проверяем стратегию

        return true;
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
        array_multisort($this->getRatesAsArray(), SORT_DESC, SORT_NUMERIC);
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

    public function getForecast($gameResult, $game): string
    {

        $preparedResultGameSteps = $this->getPreparedResultGameSteps();

        $forecast = [];
        for ($numberStep = 1; $numberStep <= $game->number_steps; $numberStep++) {
           if($numberStep == 1) {
               $forecast[] = $this->getCurrentStepForecast(AlgorithmTimer::getCurrentTime(), $preparedResultGameSteps);
           } else {
               $forecast[] = $this->getCurrentStepForecast(AlgorithmTimer::getDecrementedTime(self::EXPIRATION), $preparedResultGameSteps);
           }
        }

        var_dump($preparedResultGameSteps, AlgorithmTimer::getCurrentTime(), $forecast);
        exit();

        switch ($gameResult){
            case self::GAME_WIN :

        }

        return $this->getExpectedResultGameSteps();
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

        // Так как прогноза точно на текущее время может не быть, выбираем максимально приближенный по времени прогноз
        if (!empty($items)) {
            $minKey = min(array_keys($items));

            return $items[$minKey];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function playGameOrNotPlay()
    {
        $rand = mt_rand(0, 1);

        if ($rand >= $this->algorithmParamsModel->probability_play) {
            return true;
        }

        AlgorithmTimer::incrementCurrentTime(1);
        $this->playGameOrNotPlay();
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
                $deviation = $amountEnd + $algorithmParamsModel->deviation_from_amount_end / 100 * $amountEnd;
                if ($amountStart >= $amountEnd && $amountStart <= $deviation) {
                    return false;
                }
                if ($algorithmParamsModel->k_lucky > 1) {
                    $algorithmParamsModel->k_lucky /= 2;
                }
                break;
            case 2 :
                if ($algorithmParamsModel->amount_start < $algorithmParamsModel->amount_end) {
                    $algorithmParamsModel->k_lucky *= 2;
                }
                if ($algorithmParamsModel->amount_start < min($this->getRatesAsArray())) {
                    return false;
                }
                break;
            case 3 :
                if ($algorithmParamsModel->amount_start > $algorithmParamsModel->amount_end) {
                    $deviation = $amountEnd + $algorithmParamsModel->deviation_from_amount_end / 100 * $amountEnd;
                    if ($amountStart >= $amountEnd && $amountStart <= $deviation) {
                        return false;
                    }
                }
        }

        return true;
    }

    //@todo доработать (вынести сохранение текущей стратегии в массив, а не в базу)
    //@todo а в базу сохранять только по достижении результата.
    public function checkStrategy()
    {
        $algorithmParamsModel = $this->algorithmParamsModel;
        $bestStrategy = $this->getBestStrategiesParams();
        if (!$bestStrategy) {
//            $params = [
//                'algorithm_params_id' => $algorithmParamsModel->id,
//                'timestamp' => AlgorithmTimer::getCurrentTime(),
//                'iteration_number' => $this->iterationNumber,
//                'money_amount' => $algorithmParamsModel->amount_start,
//                'game_id' => $algorithmParamsModel->,
//                'rate_amount' => ,
//                'forecast' => ,
//                'result' => ,
//                'best_strategy' => ,
//            ];
//            $strategy = new Strategy($params);
        } else {

        }
    }

    //@todo доработать
    public function saveStrategy($params)
    {
        $strategy = new Strategy($params);
        $strategy->hardSave();
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

    public function isWin()
    {
        //@todo реализовать определение выиграл игрок или нет и соответствующие поведения
    }

    /**
     * @return array
     */
    public function getGamesAsArray(): array
    {
        return explode(', ', $this->algorithmParamsModel->games);
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
    public function getIncrementedCheckMoneyNumberStep()
    {
        return $this->checkMoneyNumberStep += 1;
    }
}