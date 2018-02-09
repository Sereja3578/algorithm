<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\forms\ResetPasswordForm */

$this->title = Yii::t('auth', 'Восстановить пароль');

$fieldOptions = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => '{input}<span class="glyphicon glyphicon-envelope form-control-feedback"></span>'
];

?>
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Admin</b>Algorithm</a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg"><?= $this->title ?></p>
        <?php
        $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableClientValidation' => false
        ]);
        ?>

        <?= $form->field($model, 'email', $fieldOptions)->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <div class="row">
            <div class="col-xs-12">
                <a href="<?= Url::toRoute(['login']) ?>" style="line-height: 34px">
                    &larr; <?= Yii::t('auth', 'Назад') ?>
                </a>
                <?= Html::submitButton(Yii::t('buttons', 'Отправить'), [
                    'class' => 'btn btn-primary btn-flat pull-right',
                    'name' => 'login-button'
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
