<?php

use backend\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */

$this->title = Yii::t('algorithm', 'Создать Algorithm Params');
$this->params['title'] = Yii::t('algorithm', 'Algorithm Params');
$this->params['title_desc'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('algorithm', 'Algorithm Params'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary algorithm-params-create">
    <div class="box-header">
        <div class="box-tools">
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-list']) . ' ' .
            Yii::t('algorithm', 'Algorithm Params'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
        </div>
    </div>
    <div class="box-body">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    </div>
</div>