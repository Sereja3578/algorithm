<?php

namespace backend\modules\algorithm\controllers;

use Yii;
use common\models\AlgorithmParams;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
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

    /**
     * Запускает алгоритм на выполнение
     *
     * @throws \ErrorException
     */
    public function actionRun()
    {
        $params = Yii::$app->request->post();
        $model = new AlgorithmParams();

        if($model->load($params) && $model->validate()) {
            Yii::$app->amqp->send('algorithmExchange', 'run', [
                'model' => $model
            ]);
        }

        Yii::$app->session->setFlash(
            'success',
            Yii::t('flash', 'Алгоритм успешно отправлен на выполнение')
        );

        return $this->redirect(Url::toRoute(['index']));
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