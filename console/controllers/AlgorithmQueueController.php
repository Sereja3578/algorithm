<?php

namespace console\controllers;

use Yii;
use backend\components\AlgorithmTimer;
use backend\components\GameHelper;
use backend\components\QuoteHelper;
use backend\components\WinCoefHelper;
use common\models\Strategy;
use common\models\AlgorithmParams;
use common\components\QueueListener;
use Exception;

/**
 * Class PlatprocQueueController
 * @package console\controllers
 */
class AlgorithmQueueController extends QueueListener
{
    /**
     * @param AlgorithmParams $model
     * @return bool
     */
    public function actionRun($model)
    {

        print_r($model);

        try {
            // Устанавливаем начальное и конечное время
            AlgorithmTimer::setCurrentTime($model->t_start);
            AlgorithmTimer::setEndTime($model->t_end);
            // Создаем хелперы с параметрами алгоритма
            $cloneAlgorithmParams = clone $model;
            $quoteHelper = new QuoteHelper($cloneAlgorithmParams);
            $gameHelper = new GameHelper($cloneAlgorithmParams);

            $quotes = $quoteHelper->getQuotes();

            // Формируем массив с подготовленными результатами игры на каждую секунду
            $gameHelper->setPreparedResultGameSteps($quotes);

            WinCoefHelper::$useFakeCoefs = $model->use_fake_coefs;
            if ($model->use_fake_coefs) {
                // Устанавливаем фейковые коэффициенты
                $fakeWinCoefs = WinCoefHelper::getFakeWinCoefs($gameHelper->getPreparedResultGameSteps());
                WinCoefHelper::setFakeWinCoefs($fakeWinCoefs);
            } else {
                // Устанавливаем реальные коэффициенты
                $winCoefs = WinCoefHelper::getWinCoefs();
                WinCoefHelper::setWinCoefs($winCoefs);
            }

            for($iterationNumber = 1; $iterationNumber <= $model->iterations; $iterationNumber++) {
                if(AlgorithmTimer::checkTime()) {
                    var_dump('Iteration number: ' . $iterationNumber);

                    $result = $gameHelper->playerSimulation($iterationNumber);
                    if (!$result) {
                        $gameHelper->resetAllParams(clone $model);
                        continue;
                    } elseif ($result instanceof Strategy) {
                        $model->hardSave(false);
                        $result->algorithm_params_id = $model->id;
                        $gameHelper->saveStrategy($result);
                        return true;
                    }
                }
            }
        } catch (Exception $e) {
            var_dump($e->getMessage() . $e->getFile() . $e->getLine());
            return false;
        }
    }

}