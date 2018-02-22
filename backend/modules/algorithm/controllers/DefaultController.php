<?php

namespace backend\modules\algorithm\controllers;

use backend\components\AlgorithmTimer;
use backend\components\Controller;
use backend\components\GameHelper;
use backend\components\QuoteHelper;
use backend\modules\algorithm\models\AlgorithmParamsSearch;
use common\models\AlgorithmParams;
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

        //@todo сделать инициализацию параметров в AlgorithmHelper
        //$params = Yii::$app->request->post();
        $params = [
            'AlgorithmParams' => [
                // Итераций главного цикла - одна итерация имитирует действия одного игрока
                'iterations' => 20,
                // Коэффициент удачливости игрока
                'k_lucky' => 1.3,
                // Валютная пара по которй вытаскиваем котировки
                'asset_id' => 1,
                // Начальная сумма > t_ends
                'amount_start' => 50,
                // Конечная сумма к которой стремится алгоритм
                'amount_end' => 200,
                // Начальное время работы алгоритма < now()
                't_start' => '2018-01-24 11:00:00',
                // По истечению данного времени заканчиваем работу алгоритма < now()
                't_end' => '2018-01-24 11:00:30',
                // Минимальный допустимый процент отклонения текущей суммы от конечной
                'deviation_from_amount_end' => 1,
                // Игры, где ключи id игры, а значения шанс шанс выбора игры
                'games' => [
                    '1' => 30,
                    '2' => 30,
                    '3' => 20,
                    '4' => 15,
                    '5' => 5,
                ],
                // Коэффициенты игры, где ключи id игры
                'coefs' => ['1' => 3.5, '2' => 3],
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
            ]
        ];

        $model = new AlgorithmParams();
        if($model->load($params) && $model->validate()) {
            // Создаем хелперы с параметрами алгоритма
            AlgorithmTimer::setCurrentTime($model->t_start);
            $quoteHelper = new QuoteHelper($model);
            $gameHelper = new GameHelper($model);

            // Получаем котировки
//            $quotes = $quoteHelper->getQuotes();
            //$expectedResultGameSteps = GameHelper::getExpectedResultGameSteps($quotes);

//            var_dump($quotes);
//            exit();

            for($iterationNumber = 0; $iterationNumber <= $model->iterations; $iterationNumber++) {
                $gameHelper->playerSimulation($iterationNumber);
            }
        }

//        /* @var  KeyValueDataProvider*/
//        $quotes = AlgorithmHelper::getQuotes($params['asset_name'], $params['t_start'], $params['t_end']);
//
//        var_dump($quotes);
//        exit();
    }
}
