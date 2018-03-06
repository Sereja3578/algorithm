<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use backend\helpers\Html;
use backend\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->baseSearchModelClass, '\\') ?> */

$this->title = $model->getListTitle();
$this->params['title'] = $model->getGridTitle();
$this->params['title_desc'] = <?= $generator->generateI18N('Просмотр') ?>;
$this->params['breadcrumbs'][] = ['label' => $model->getGridTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model-><?= $generator->getNameAttribute() ?>;

$updateButton = Html::updateButton($model);
$createButton = Html::createButton();
$indexButton = Html::indexButton($model->getGridTitle());
?>
<div class="box box-primary <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <div class="box-body" style="margin-top: 10px">
        <p align="right">
            <?="<?=" ?> join(' ', [$updateButton, $createButton, $indexButton]) ?>
        </p>
        <?="<?= "?>DetailView::widget([
            'model' => $model,
            'attributes' => $model->getListColumns(),
            'disableAttributes' => $model->getDisableAttributes(),
        ])?>
    </div>
</div>