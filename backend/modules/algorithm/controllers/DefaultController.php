<?php

namespace backend\modules\algorithm\controllers;

use backend\components\AlgorithmHelper;
use backend\components\Controller;
use backend\components\GameHelper;
use common\components\KeyValueDataProvider;
use Yii;

/**
 * Default controller for the `algorithm` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionRun()
    {

        //@todo сделать инициализацию параметров в AlgorithmHelper
        //$params = Yii::$app->request->post();
        $params = [
            // Итераций главного цикла - одна итерация имитирует действия одного игрока
            'iterations' => 20,
            // Коэффициент удачливости игрока
            'k_lucky' => 1.3,
            // Валютная пара по которй вытаскиваем котировки
            'asset_name' => 'EURUSD',
            // Начальная сумма > t_ends
            'amount_start' => 200,
            // Конечная сумма к которой стремится алгоритм
            'amount_end' => 50,
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
            // Котировки
            'quotes' => [
                ['currency_pair' => 'eurusd', 'amount' => '57', 'time' => '14.02.2018 13:14:29'],
                ['currency_pair' => 'eurusd', 'amount' => '57.37', 'time' => '14.02.2018 13:14:30'],
                ['currency_pair' => 'eurusd', 'amount' => '57.37', 'time' => '14.02.2018 13:14:31'],
                ['currency_pair' => 'eurusd', 'amount' => '57.35', 'time' => '14.02.2018 13:14:32'],
                ['currency_pair' => 'eurusd', 'amount' => '57.37', 'time' => '14.02.2018 13:14:33'],
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
        ];

        $quotes = AlgorithmHelper::getQuotes($params['asset_name'], $params['t_start'], $params['t_end']);
        $expectedResultGames = GameHelper::getExpectedResultGameSteps($quotes);

        var_dump($quotes);
        exit();

        for($i = 0; $i <= $params['iterations']; $i++) {
            GameHelper::startGame($params['games']);
        }

//        /* @var  KeyValueDataProvider*/
//        $quotes = AlgorithmHelper::getQuotes($params['asset_name'], $params['t_start'], $params['t_end']);
//
//        var_dump($quotes);
//        exit();
    }
}
