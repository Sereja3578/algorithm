<?php

namespace backend\modules\algorithm\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\algorithm\models\base\AlgorithmParamsSearchBase;
use yii\helpers\ArrayHelper;

/**
 * AlgorithmParamsSearch represents the model behind the search form about `common\models\AlgorithmParams`.
 */
class AlgorithmParamsSearch extends AlgorithmParamsSearchBase
{
    /**
     * @return []
     */
    public function getGridColumns()
    {
        return [
            'iterations' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'iterations'
            ],
            'k_lucky' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'k_lucky',
                'format' => ['decimal', 4]
            ],
            'asset_id' => [
                'class' => 'backend\components\grid\AssetIdColumn',
                'customFilters' => $this->getFilter('asset_id'),
            ],
            'amount_start' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'amount_start'
            ],
            'amount_end' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'amount_end'
            ],
            'deviation_from_amount_end' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'deviation_from_amount_end',
                'format' => ['decimal', 4]
            ],
            'games' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'games'
            ],
            't_next_start_game' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 't_next_start_game'
            ],
            'rates' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'rates'
            ],
            'number_rates' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'number_rates'
            ],
            'rate_coef' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'rate_coef',
                'format' => ['decimal', 4]
            ],
            'probability_play' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'probability_play',
                'format' => ['decimal', 4]
            ],
            'use_fake_coefs' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'use_fake_coefs'
            ],
            't_start' => [
                'class' => 'backend\components\grid\DatetimeRangeColumn',
                'attribute' => 't_start',
            ],
            't_end' => [
                'class' => 'backend\components\grid\DatetimeRangeColumn',
                'attribute' => 't_end',
            ],
            'action' => [
                'class' => 'backend\components\grid\ActionColumn',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params = null)
    {
        if (!empty($params)) {
            $this->load($params);
        }

        $this->query = AlgorithmParamsSearchBase::find();
                
        
        $this->initDateFilters();
        $this->initDatetimeFilters();

        $this->query->andFilterWhere([
            $this->query->a('id') => $this->id,
            $this->query->a('iterations') => $this->iterations,
            $this->query->a('k_lucky') => $this->k_lucky,
            $this->query->a('asset_id') => $this->asset_id,
            $this->query->a('amount_start') => $this->amount_start,
            $this->query->a('amount_end') => $this->amount_end,
            $this->query->a('deviation_from_amount_end') => $this->deviation_from_amount_end,
            $this->query->a('t_next_start_game') => $this->t_next_start_game,
            $this->query->a('number_rates') => $this->number_rates,
            $this->query->a('rate_coef') => $this->rate_coef,
            $this->query->a('probability_play') => $this->probability_play,
            $this->query->a('use_fake_coefs') => $this->use_fake_coefs,
        ]);

        $this->query->andFilterWhere(['like', $this->query->a('games'), $this->games])
            ->andFilterWhere(['like', $this->query->a('rates'), $this->rates]);

        $this->query->joinWith([
			'asset asset'
		]);
        
        return $this->getDataProvider();
    }
}