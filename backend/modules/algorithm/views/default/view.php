<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('models', 'Algorithm Params'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="algorithm-params-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('models', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('models', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('models', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'iterations',
            'k_lucky',
            'asset_id',
            'amount_start',
            'amount_end',
            't_start',
            't_end',
            'deviation_from_amount_end',
            'games',
            't_next_start_game',
            'rates',
            'number_rates',
            'rate_coef',
            'probability_play',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
