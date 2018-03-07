<?php

use backend\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */

$this->title = Yii::t('algorithm', 'Редактировать Algorithm Params') . ': ' . $model->id;
$this->params['title'] = Yii::t('algorithm', 'Algorithm Params');
$this->params['title_desc'] = Yii::t('backend', 'Редактировать');
$this->params['breadcrumbs'][] = ['label' => Yii::t('algorithm', 'Algorithm Params'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Редактирование');
?>
<div class="box box-primary algorithm-params-update">
    <div class="box-header">
        <div class="box-tools">
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . ' ' .
            Yii::t('backend', 'Просмотр'), ['view', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-list']) . ' ' .
            Yii::t('algorithm', 'Algorithm Params'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
        </div>
    </div>
    <div class="box-body" style='margin-top: 10px!important;'>
        <?= $this->render('_form', [
        'model' => $model,
        ]) ?>

    </div>
</div>