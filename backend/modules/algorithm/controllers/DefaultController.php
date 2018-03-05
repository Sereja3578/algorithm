<?php

namespace backend\modules\algorithm\controllers;

use backend\components\AlgorithmTimer;
use backend\components\Controller;
use backend\components\GameHelper;
use backend\components\QuoteHelper;
use backend\components\WinCoefHelper;
use backend\modules\algorithm\models\AlgorithmParamsSearch;
use common\models\AlgorithmParams;
use common\models\Strategy;
use Yii;

/**
 * Default controller for the `algorithm` module
 */
class DefaultController extends Controller
{
    public $searchModel = 'backend\modules\algorithm\models\AlgorithmParamsSearch';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        /**
         * @var AlgorithmParamsSearch $searchModel
         */
        $searchModel = $this->getSearchModel();
        $dataProvider = $searchModel->search();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRun()
    {

        //$params = Yii::$app->request->post();
        $params = [
            'AlgorithmParams' => [
                // Итераций главного цикла - одна итерация имитирует действия одного игрока
                'iterations' => 20,
                // Коэффициент удачливости игрока
                'k_lucky' => 1,
                // Валютная пара по которй вытаскиваем котировки
                'asset_id' => 1,
                // Начальная сумма > t_ends
                'amount_start' => 50,
                // Конечная сумма к которой стремится алгоритм
                'amount_end' => 200,
                // Начальное время работы алгоритма < now()
                't_start' => '2018-01-24 10:37',
                // По истечению данного времени заканчиваем работу алгоритма < now()
                't_end' => '2018-01-24 20:48',
                // Минимальный допустимый процент отклонения текущей суммы от конечной
                'deviation_from_amount_end' => 20,
                // Игры, где ключи id игры, а значения шанс шанс выбора игры
                'games' => [
                    '1' => 15,
                    '2' => 5,
                    '3' => 20,
                    '4' => 30,
                    '5' => 30,
                ],
                // Время до следующей игры
                't_next_start_game' => 5,
                // Ставки
                'rates' => [0.1, 0.5, 1, 5, 10],
                // Число ставок
                'number_rates' => 2,
                // Повышение ставки
                'rate_coef' => 1.2,
                // Вероятность ставки
                'probability_play' => 0.7,
                'use_fake_coefs' => 1,
            ]
        ];

        $model = new AlgorithmParams();
        if($model->load($params) && $model->validate()) {

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

//            var_dump($quotes);
//            exit();

            for($iterationNumber = 1; $iterationNumber <= $model->iterations; $iterationNumber++) {
                if(AlgorithmTimer::checkTime()) {
                    var_dump('Iteration number: ' . $iterationNumber);

                    $result = $gameHelper->playerSimulation($iterationNumber);
                    if (!$result) {
                        continue;
                    } elseif ($result instanceof Strategy) {
                        $model->hardSave(false);
                        $result->algorithm_params_id = $model->id;
                        $gameHelper->saveStrategy($result);
                        break;
                    }
                }
            }
        }

//        /* @var  KeyValueDataProvider*/
//        $quotes = AlgorithmHelper::getQuotes($params['asset_name'], $params['t_start'], $params['t_end']);
//
//        var_dump($quotes);
//        exit();
    }
}
