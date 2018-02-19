<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="algorithm-params-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'iterations')->textInput() ?>

    <?= $form->field($model, 'k_lucky')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'asset_id')->textInput() ?>

    <?= $form->field($model, 'amount_start')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount_end')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 't_start')->textInput() ?>

    <?= $form->field($model, 't_end')->textInput() ?>

    <?= $form->field($model, 'deviation_from_amount_end')->textInput() ?>

    <?= $form->field($model, 'games')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 't_next_start_game')->textInput() ?>

    <?= $form->field($model, 'rates')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number_rates')->textInput() ?>

    <?= $form->field($model, 'rate_coef')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'probability_play')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('models', 'Create') : Yii::t('models', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
