<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\algorithm\models\AlgorithmParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $searchModel->getGridTitle();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="algorithm-params-index">
    <p class="pull-right">
        <?= Html::a(Yii::t('models', 'Создать новый алгоритм'), ['run'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $searchModel->getGridColumns(),
    ]); ?>
</div>
