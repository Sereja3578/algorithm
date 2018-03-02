<?php

namespace common\models;

use common\models\base\AlgorithmParamsBase;
use yii\db\Expression;

/**
 * Algorithm params
 * @see \common\models\query\AlgorithmParamsQuery
 */
class AlgorithmParams extends AlgorithmParamsBase
{
    /**
     * @var array
     */
    public $quotes;

    /**
     * @var array
     */
    public $coefs;

    /**
     * @var array
     */
    public $rates;

    public function rules()
    {
        return [
            [[
                'iterations',
                'asset_id',
                't_next_start_game',
                'number_rates'
            ], 'integer', 'min' => 0],
            [[
                'k_lucky',
                'amount_start',
                'amount_end',
                'deviation_from_amount_end',
                'rate_coef',
                'probability_play'
            ], 'number', 'min' => 0],
            [[
                't_start',
                't_end',
                'created_at',
                'updated_at'
            ], 'filter', 'filter' => function ($value) {
                return is_int($value) ? date('Y-m-d H:i', $value) : $value;
            }],
            [[
                't_start',
                't_end',
                'created_at',
                'updated_at'
            ], 'date', 'format' => 'php:Y-m-d H:i'],
            [[
                'amount_start',
                'amount_end'
            ], 'match', 'pattern' => '~^\d{1,15}(?:\.\d{1,8})?$~'],
            [['asset_id', 'amount_start', 'amount_end', 'games', 'rates'], 'required'],
            [['games', 'rates'], 'string', 'max' => 255],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [[
                't_start',
                't_end',
                'created_at',
                'updated_at'
            ], 'default', 'value' => new Expression('CURRENT_TIMESTAMP')],
            [['iterations'], 'default', 'value' => '200000'],
            [[
                'k_lucky',
                'deviation_from_amount_end',
                'rate_coef'
            ], 'default', 'value' => '1'],
            [['t_next_start_game'], 'default', 'value' => '5'],
            [['number_rates'], 'default', 'value' => '2'],
            [['probability_play', 'use_fake_coefs'], 'default', 'value' => '0'],
            [['quotes', 'rates', 'coefs'], 'safe', 'on' => 'validate'],
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if(parent::beforeValidate()){
            $this->games = serialize($this->games);
            $this->rates = implode(', ', $this->rates);
            return true;
        };
        return false;
    }
}
