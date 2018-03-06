<?php

namespace backend\modules\algorithm\models;

use backend\components\interfaces\SearchModelInterface;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AlgorithmParams;

/**
 * AlgorithmParamsSearch represents the model behind the search form about `common\models\AlgorithmParams`.
 */
class AlgorithmParamsSearch extends AlgorithmParams implements SearchModelInterface
{
    public $query;

    public function getGridTitle()
    {
        return Yii::t('models', 'Алгоритмы');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'iterations', 'asset_id', 'deviation_from_amount_end', 't_next_start_game', 'number_rates'], 'integer'],
            [['k_lucky', 'amount_start', 'amount_end', 'rate_coef', 'probability_play'], 'number'],
            [['t_start', 't_end', 'games', 'rates', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getGridColumns()
    {
        return [
            'id',
            'iterations',
            'k_lucky',
            'asset_id',
            'amount_start',
            'amount_end',
            't_start',
            't_end',
            'deviation_from_amount_end',
            't_next_start_game',
            'number_rates',
            'rate_coef',
            'probability_play',
            'created_at',
            'updated_at',
            ['class' => 'yii\grid\ActionColumn']
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
        $this->query = static::find();

        if (!empty($params)) {
            $this->load($params);
        }

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $this->query ,
        ]);

        // grid filtering conditions
        $this->query->andFilterWhere([
            'id' => $this->id,
            'iterations' => $this->iterations,
            'k_lucky' => $this->k_lucky,
            'asset_id' => $this->asset_id,
            'amount_start' => $this->amount_start,
            'amount_end' => $this->amount_end,
            't_start' => $this->t_start,
            't_end' => $this->t_end,
            'deviation_from_amount_end' => $this->deviation_from_amount_end,
            't_next_start_game' => $this->t_next_start_game,
            'number_rates' => $this->number_rates,
            'rate_coef' => $this->rate_coef,
            'probability_play' => $this->probability_play,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $this->query->andFilterWhere(['like', 'games', $this->games])
            ->andFilterWhere(['like', 'rates', $this->rates]);

        return $dataProvider;
    }
}
