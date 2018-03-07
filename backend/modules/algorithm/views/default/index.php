<?php

use yii\helpers\Html;
use backend\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\algorithm\models\AlgorithmParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $permissionPrefix */

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
<div class="box box-primary algorithm-params-index">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'disableColumns' => $searchModel->getDisableColumns(),
            'columns' => $searchModel->getGridColumns(),
            'toolbar' => $searchModel->getGridToolbar(),
            'panelBeforeTemplate' => '
                <div class="pull-left">' . $createButton . '</div>
                <div class="pull-left"></div>
                <div class="pull-right">
                   <div class="btn-toolbar kv-grid-toolbar" role="toolbar">{toolbar}</div>
                </div>
                <div class="clearfix"></div>
            ',
        ]) ?>
    </div>
</div>