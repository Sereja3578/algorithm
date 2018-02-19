<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\algorithm\models\AlgorithmParamsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="algorithm-params-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'iterations') ?>

    <?= $form->field($model, 'k_lucky') ?>

    <?= $form->field($model, 'asset_id') ?>

    <?= $form->field($model, 'amount_start') ?>

    <?php // echo $form->field($model, 'amount_end') ?>

    <?php // echo $form->field($model, 't_start') ?>

    <?php // echo $form->field($model, 't_end') ?>

    <?php // echo $form->field($model, 'deviation_from_amount_end') ?>

    <?php // echo $form->field($model, 'games') ?>

    <?php // echo $form->field($model, 't_next_start_game') ?>

    <?php // echo $form->field($model, 'rates') ?>

    <?php // echo $form->field($model, 'number_rates') ?>

    <?php // echo $form->field($model, 'rate_coef') ?>

    <?php // echo $form->field($model, 'probability_play') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('models', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('models', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
