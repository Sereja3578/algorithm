<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */

$this->title = Yii::t('models', 'Update {modelClass}: ', [
    'modelClass' => 'Algorithm Params',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Algorithm Params'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('models', 'Update');
?>
<div class="algorithm-params-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
