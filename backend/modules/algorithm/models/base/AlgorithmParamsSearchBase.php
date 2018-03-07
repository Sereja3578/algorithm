<?php

namespace backend\modules\algorithm\models\base;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AlgorithmParams;
use yii\helpers\ArrayHelper;
use backend\helpers\Html;
use backend\components\SearchInterface;
use backend\components\SearchTrait;

/**
 * AlgorithmParamsSearchBase represents the model behind the search form about `common\models\AlgorithmParams`.
 */
class AlgorithmParamsSearchBase extends AlgorithmParams implements SearchInterface

{
    use SearchTrait {
        modifyQuery as protected modifyQueryDefault;
        getSort as protected getSortDefault;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                [['id', 'iterations', 'asset_id', 't_next_start_game', 'number_rates', 'use_fake_coefs'], 'integer'],
                [['k_lucky', 'amount_start', 'amount_end', 'deviation_from_amount_end', 'rate_coef', 'probability_play'], 'number'],
                [['t_start', 't_end', 'created_at', 'updated_at'], 'filter', 'filter' => 'trim'],
                [['t_start', 't_end', 'created_at', 'updated_at'], 'date', 'format' => 'dd/MM/YYYY - dd/MM/YYYY', 'message' => Yii::t('backend', 'Некорректный диапазон дат')],
                [['games', 'rates'], 'safe'],
            ]
        );
    }
    /**
     * @return string
     */
    public function getGridTitle()
    {
        return Yii::t('algorithm', 'Параметры алгоритмов');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [

            ]
        );
    }

    /**
     * @return string
     */
    public function getListTitle()
    {
        return Yii::t('algorithm', 'Параметры алгоритмов') . ': ' . $this->id;
    }

    /**
     * @return array
     */
    public function getListColumns()
    {
        return [
            'id',
            'iterations',
            [
                'attribute' => 'k_lucky',
                'format' => ['decimal', 4],
            ],
            [
                'attribute' => 'asset_id',
                'value' => function ($form, $widget) {
                    return ($model = $widget->model->asset) ? $model->getDocName() : null;
                },
                'format' => 'raw',
            ],
            'amount_start',
            'amount_end',
            't_start',
            't_end',
            [
                'attribute' => 'deviation_from_amount_end',
                'format' => ['decimal', 4],
            ],
            'games',
            't_next_start_game',
            'rates',
            'number_rates',
            [
                'attribute' => 'rate_coef',
                'format' => ['decimal', 4],
            ],
            [
                'attribute' => 'probability_play',
                'format' => ['decimal', 4],
            ],
            'use_fake_coefs',
            'created_at',
            'updated_at',
            
        ];
    }
}