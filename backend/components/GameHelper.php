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

    const EXPIRATION = 5;

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
        $checkMoneyNumberStep = 1;

        if (!$this->checkMoney($checkMoneyNumberStep)) {
            return false;
        }

//        if($this->isFirstGamePlayed()) {
//            $this->checkStrategy();
//        }

        if (!$this->checkMoney(++$checkMoneyNumberStep)) {
            return false;
        }

        if ($this->playGameOrNotPlay()) {

            $gameId = $this->chooseGameByChance(unserialize($algorithmParamsModel->games));
            $game = $this->getGame($gameId);

            if ($game) {
                $this->startGame($game);
            }

        }

        return true;
    }

    public function startGame($game)
    {
        $algorithmParamsModel = $this->algorithmParamsModel;

        /*
        * Имитируем работу игры.
        * Увеличиваем текущее время на число шагов умноженное на время экспирации.
        */
        for ($i = 0; $i <= $game->number_steps; $i++) {
            AlgorithmTimer::incrementCurrentTime(self::EXPIRATION);
        }

        // Теперь решаем выиграл игрок или проиграл.

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

    public function getGame($gameId)
    {
        return Game::findOne(['id' => $gameId]);
    }

    public function getExpectedResultGameSteps(array $quotes)
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

        return $preparedResultGameSteps;
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
                if ($algorithmParamsModel->amount_start > min($this->getRatesAsArray())) {
                    return false;
                }
                break;
        }

        return true;
    }

    //@todo доработать (вынести сохранение текущей стратегии в массив, а не в базу)
    //@todo а в базу сохранять только по достижении результата.
    public function checkStrategy()
    {
        $algorithmParamsModel = $this->algorithmParamsModel;
        $bestStrategy = $this->getBestStrategy();
        if (!$bestStrategy) {
            $params = [
                'algorithm_params_id' => $algorithmParamsModel->id,
                'timestamp' => AlgorithmTimer::getCurrentTime(),
                'iteration_number' => $this->iterationNumber,
                'money_amount' => $algorithmParamsModel->amount_start,
                'game_id' => $algorithmParamsModel->ga,
                'rate_amount' => ,
                'forecast' => ,
                'result' => ,
                'best_strategy' => ,
            ];
            $strategy = new Strategy($params);
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
     * @return Strategy|null
     */
    public function getBestStrategy()
    {
        return Strategy::getBestStrategy();
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
}