<?php

namespace common\models;

use common\models\base\StrategyBase;
use yii\db\Expression;

/**
 * Strategy
 * @see \common\models\query\StrategyQuery
 */
class Strategy extends StrategyBase
{
    /**
     * @message Лучшая стратегия
     */
    const BEST_STRATEGY = 1;

    /**
     * @message Не лучшая стратегия
     */
    const NOT_BEST_STRATEGY = 0;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'result',
                'best_strategy'
            ], 'filter', 'filter' => function ($value) {
                return $value ? 1 : 0;
            }, 'skipOnEmpty' => true],
            [[
                'result',
                'best_strategy'
            ], 'boolean'],
            [[
                'algorithm_params_id',
                'iteration_number',
                'game_id'
            ], 'integer', 'min' => 0],
            [[
                'money_amount',
                'rate_amount'
            ], 'number', 'min' => 0],
            [['timestamp'], 'filter', 'filter' => function ($value) {
                return is_int($value) ? date('Y-m-d H:i:s', $value) : $value;
            }],
            [['timestamp'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [[
                'money_amount',
                'rate_amount'
            ], 'match', 'pattern' => '~^\d{1,15}(?:\.\d{1,8})?$~'],
            [['algorithm_params_id', 'iteration_number', 'money_amount', 'game_id', 'rate_amount', 'forecast', 'result', 'best_strategy'], 'required'],
            [['forecast'], 'string', 'max' => 25],
            [['algorithm_params_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlgorithmParams::className(), 'targetAttribute' => ['algorithm_params_id' => 'id']],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['game_id' => 'id']],
            [['timestamp'], 'default', 'value' => new Expression('CURRENT_TIMESTAMP')],
        ];
    }

    /**
     * @return null|Strategy
     */
    public static function getBestStrategy()
    {
        return self::findOne(['best_strategy' => self::BEST_STRATEGY]);
    }

    public function beforeValidate()
    {
        if(parent::beforeValidate()){
            $this->forecast = implode(', ', $this->forecast);
            return true;
        };
        return false;
    }
}
