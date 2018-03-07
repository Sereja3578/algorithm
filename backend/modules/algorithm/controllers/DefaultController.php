<?php

namespace backend\modules\algorithm\controllers;

use backend\components\AlgorithmTimer;
use backend\components\GameHelper;
use backend\components\QuoteHelper;
use backend\components\WinCoefHelper;
use common\models\Strategy;
use Yii;
use common\models\AlgorithmParams;
use backend\modules\algorithm\models\AlgorithmParamsSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\ActiveRecord;

/**
 * DefaultController implements the CRUD actions for AlgorithmParams model.
 *
 * @method actionExportConfirm()
 * @method actionExport($partExport, $params = null)
 */
class DefaultController extends Controller
{
    protected $searchClass = 'backend\modules\algorithm\models\AlgorithmParamsSearch';

    /**
     * Lists all AlgorithmParams models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var ActiveRecord $searchModel */
        $searchModel = $this->getSearchModel();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(),
        ]);
    }

    /**
     * Displays a single AlgorithmParams model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => \backend\modules\algorithm\models\base\AlgorithmParamsSearchBase::find()->pk($id)->one(),
        ]);
    }

    /**
     * Creates a new AlgorithmParams model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AlgorithmParams();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->validate(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AlgorithmParams model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (($model = AlgorithmParams::find()
                        ->pk($id)
                        ->one()) === null) {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AlgorithmParams model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionRun()
    {
        $params = Yii::$app->request->post();

//        $params = [
//            'AlgorithmParams' => [
//                // Итераций главного цикла - одна итерация имитирует действия одного игрока
//                'iterations' => 20,
//                // Коэффициент удачливости игрока
//                'k_lucky' => 1.2,
//                // Валютная пара по которй вытаскиваем котировки
//                'asset_id' => 1,
//                // Начальная сумма > t_ends
//                'amount_start' => 50,
//                // Конечная сумма к которой стремится алгоритм
//                'amount_end' => 200,
//                // Начальное время работы алгоритма < now()
//                't_start' => '2018-01-24 10:37',
//                // По истечению данного времени заканчиваем работу алгоритма < now()
//                't_end' => '2018-01-24 20:48',
//                // Минимальный допустимый процент отклонения текущей суммы от конечной
//                'deviation_from_amount_end' => 20,
//                // Игры, где ключи id игры, а значения шанс шанс выбора игры
//                'games' => [
//                    '1' => 15,
//                    '2' => 5,
//                    '3' => 20,
//                    '4' => 30,
//                    '5' => 30,
//                ],
//                // Время до следующей игры
//                't_next_start_game' => 5,
//                // Ставки
//                'rates' => [0.1, 0.5, 1, 5, 10],
//                // Число ставок
//                'number_rates' => 2,
//                // Повышение ставки
//                'rate_coef' => 1.2,
//                // Вероятность ставки
//                'probability_play' => 0.7,
//                'use_fake_coefs' => 1,
//            ]
//        ];



        $model = new AlgorithmParams();
        if($model->load($params) && $model->validate()) {

            var_dump($params);
            exit();

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

    /**
     * Finds the AlgorithmParams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AlgorithmParams the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlgorithmParams::find()
                ->innerJoinWith([
                    'asset asset'
                ])
                ->pk($id)
                ->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('errors', 'Запрашиваемая страница не найдена.'));
        }
    }
}