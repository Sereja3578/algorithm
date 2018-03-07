<?php

use backend\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Asset;
use common\models\Game;

/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => 'run',
    'id' => 'app',
    'enableAjaxValidation'   => false,
    'enableClientValidation' => true,
    'validateOnBlur'         => false,
    'validateOnType'         => false,
    'validateOnChange'       => true,
    'validateOnSubmit'       => true,
]); ?>

<div class="algorithm-params-form">

    <?= $form->field($model, 'iterations')->widget(\kartik\widgets\TouchSpin::className(), [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 1,
            'max' => 9999999999,
        ]
    ]); ?>

    <?= $form->field($model, 'k_lucky')->widget(\kartik\widgets\TouchSpin::className(), [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 0,
            'step' => 0.1,
            'decimals' => 1,
        ]
    ]); ?>

    <?= $form->field($model, 'asset_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => Asset::findForFilter(), 
        'options' => ['placeholder' => Yii::t('algorithm', 'Выберите валютную пару')],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'amount_start')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount_end')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 't_start')->widget(\kartik\widgets\DateTimePicker::className(), [
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $form->field($model, 't_end')->widget(\kartik\widgets\DateTimePicker::className(), [
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $form->field($model, 'deviation_from_amount_end')->widget(\kartik\widgets\TouchSpin::className(), [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 0,
            'step' => 0.1,
            'decimals' => 1,
        ]
    ]); ?>

    <?= $form->field($model, 'games')->widget(\kartik\widgets\Select2::className(), [
            'data' => \common\models\Game::findForFilter(),
            'options' => ['placeholder' => Yii::t('algorithm', 'Выберите игры')],
            'pluginOptions' => [
                'multiple' => true
            ]
    ]) ?>

    <?= $this->render('games-chance', [
        'model' => $model,
        'form' => $form
    ]);?>

    <?= $form->field($model, 't_next_start_game')->widget(\kartik\widgets\TouchSpin::className(), [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 0,
            'max' => 3600,
        ]
    ]); ?>

    <?= $form->field($model, 'rates')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number_rates')->widget(\kartik\widgets\TouchSpin::className(), [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 1,
            'max' => 10,
        ]
    ]); ?>

    <?= $form->field($model, 'rate_coef')->widget(\kartik\widgets\TouchSpin::className(), [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 0.1,
            'step' => 0.1,
            'decimals' => 1,
        ]
    ]); ?>

    <?= $form->field($model, 'probability_play')->widget(\kartik\widgets\TouchSpin::className(), [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 0,
            'step' => 0.1,
            'decimals' => 1,
        ]
    ]); ?>

    <?= $form->field($model, 'use_fake_coefs')->checkbox(); ?>

</div>

<div class="box-footer">
    <?= Html::submitButton(Yii::t('buttons', 'Начать выполнение алгоритма'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
