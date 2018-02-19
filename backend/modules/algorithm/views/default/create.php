<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */

$this->title = Yii::t('models', 'Create Algorithm Params');
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Algorithm Params'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="algorithm-params-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
