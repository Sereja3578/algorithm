<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use backend\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateI18N('Редактировать ' . Inflector::camel2words(StringHelper::basename($generator->modelClass)), true) ?> . ': ' . $model-><?= $generator->getNameAttribute() ?>;
$this->params['title'] = <?= $generator->generateI18N(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))), true) ?>;
$this->params['title_desc'] = <?= $generator->generateI18N('Редактировать') ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $model_many_string = $generator->generateI18N(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))), true) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateI18N('Редактирование') ?>;
?>
<div class="box box-primary <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">
    <div class="box-header">
        <div class="box-tools">
            <?= "<?= " ?>Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . ' ' .
            <?= $generator->generateI18N('Просмотр') ?>, ['view', <?= $urlParams ?>], ['class' => 'btn btn-success btn-sm']) ?>
            <?= "<?= " ?>Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-list']) . ' ' .
            <?= $model_many_string ?>, ['index'], ['class' => 'btn btn-default btn-sm']) ?>
        </div>
    </div>
    <div class="box-body" style='margin-top: 10px!important;'>
        <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
        ]) ?>

    </div>
</div>