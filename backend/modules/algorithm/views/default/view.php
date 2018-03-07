<?php

use backend\helpers\Html;
use backend\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\modules\algorithm\models\base\AlgorithmParamsSearchBase */

$this->title = $model->getListTitle();
$this->params['title'] = $model->getGridTitle();
$this->params['title_desc'] = Yii::t('backend', 'Просмотр');
$this->params['breadcrumbs'][] = ['label' => $model->getGridTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;

$updateButton = Html::updateButton($model);
$createButton = Html::createButton();
$indexButton = Html::indexButton($model->getGridTitle());
?>
<div class="box box-primary algorithm-params-view">
    <div class="box-body" style="margin-top: 10px">
        <p align="right">
            <?= join(' ', [$updateButton, $createButton, $indexButton]) ?>
        </p>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => $model->getListColumns(),
            'disableAttributes' => $model->getDisableAttributes(),
        ])?>
    </div>
</div>