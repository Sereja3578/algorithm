<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\algorithm\models\AlgorithmParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $searchModel->getGridTitle();
$this->params['breadcrumbs'][] = $this->title;

$createButton = Html::a(
    Html::tag('span',
        '',
        ['class' => 'glyphicon glyphicon-plus']) . ' ' . Yii::t('buttons', 'Создать алгоритм'),
    ['create'],
    ['class' => 'btn btn-success btn-sm']
);
?>



<div class="box box-primary">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $searchModel->getGridColumns(),
            'panelBeforeTemplate' => '
                <div class="pull-left">' . $createButton . '</div>
                <div class="pull-left"></div>
                <div class="pull-right">
                   <div class="btn-toolbar kv-grid-toolbar" role="toolbar">{toolbar}</div>
                </div>
                <div class="clearfix"></div>
            ',
        ]); ?>
    </div>
</div>
