<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\crud\Generator */
echo "<?php\n";
?>

use backend\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateI18N('Создать ' . Inflector::camel2words(StringHelper::basename($generator->modelClass)), true) ?>;
$this->params['title'] = <?= $generator->generateI18N(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))), true) ?>;
$this->params['title_desc'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => <?= $model_many_string = $generator->generateI18N(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))), true) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
    <div class="box-header">
        <div class="box-tools">
            <?= "<?= " ?>Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-list']) . ' ' .
            <?= $model_many_string ?>, ['index'], ['class' => 'btn btn-default btn-sm']) ?>
        </div>
    </div>
    <div class="box-body">

    <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
    ]) ?>

    </div>
</div>