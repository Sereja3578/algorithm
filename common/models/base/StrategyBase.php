<?php

namespace common\models\base;

use Yii;
use common\models\AlgorithmParams;
use common\models\Asset;
use yii\db\Expression;
use common\models\Game;

/**
 * This is the model class for table "strategy".
 *
 * @property integer $id
 * @property integer $algorithm_params_id
 * @property string $timestamp
 * @property integer $iteration_number
 * @property string $money_amount
 * @property integer $game_id
 * @property string $rate_amount
 * @property string $forecast
 * @property integer $result
 * @property integer $best_strategy
 *
 * @property AlgorithmParams $algorithmParams
 * @property Asset $asset
 * @property Game $game
 */
class StrategyBase extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'strategy';
    }

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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'algorithm_params_id' => Yii::t('models', 'Параметры алгоритма'),
            'timestamp' => Yii::t('models', 'Время достижения результата'),
            'iteration_number' => Yii::t('models', 'Номер итерации'),
            'money_amount' => Yii::t('models', 'Сумма денег на момент достижения результата'),
            'game_id' => Yii::t('models', 'Игра'),
            'rate_amount' => Yii::t('models', 'Ставка'),
            'forecast' => Yii::t('models', 'Прогноз'),
            'result' => Yii::t('models', 'Результат'),
            'best_strategy' => Yii::t('models', 'Лучшая стратегия'),
        ];
    }

    /**
     * @return \common\models\query\AlgorithmParamsQuery|\yii\db\ActiveQuery
     */
    public function getAlgorithmParams()
    {
        return $this->hasOne(AlgorithmParams::className(), ['id' => 'algorithm_params_id']);
    }

    /**
     * @return \common\models\query\AssetQuery|\yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(Asset::className(), ['id' => 'asset_id'])
            ->viaTable('algorithm_params via_algorithm_params', ['id' => 'algorithm_params_id']);
    }

    /**
     * @return \common\models\query\GameQuery|\yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::className(), ['id' => 'game_id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\query\StrategyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\StrategyQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function singularRelations()
    {
        return [
            'algorithmParams' => [
                'hasMany' => false,
                'class' => 'common\models\AlgorithmParams',
                'link' => ['id' => 'algorithm_params_id'],
                'direct' => true,
                'viaTable' => false
            ],
            'game' => [
                'hasMany' => false,
                'class' => 'common\models\Game',
                'link' => ['id' => 'game_id'],
                'direct' => true,
                'viaTable' => false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function booleanAttributes()
    {
        return [
            'result',
            'best_strategy'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function datetimeAttributes()
    {
        return ['timestamp'];
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'Strategy');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    public static function titleKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    // public function getTitleText()
    // {
    //     return $this->id;
    // }

    /**
     * @param string|array|Expression $condition
     * @param array $params
     * @param string|array|Expression $orderBy
     * @return array
     */
    public function algorithmParamsIdListItems($condition = null, $params = [], $orderBy = null)
    {
        return AlgorithmParams::findListItems($condition, $params, $orderBy);
    }

    /**
     * @param array $condition
     * @param string|array|Expression $orderBy
     * @return array
     */
    public function algorithmParamsIdFilterListItems(array $condition = [], $orderBy = null)
    {
        return AlgorithmParams::findFilterListItems($condition, $orderBy);
    }

    /**
     * @param string|array|Expression $condition
     * @param array $params
     * @param string|array|Expression $orderBy
     * @return array
     */
    public function gameIdListItems($condition = null, $params = [], $orderBy = null)
    {
        return Game::findListItems($condition, $params, $orderBy);
    }

    /**
     * @param array $condition
     * @param string|array|Expression $orderBy
     * @return array
     */
    public function gameIdFilterListItems(array $condition = [], $orderBy = null)
    {
        return Game::findFilterListItems($condition, $orderBy);
    }
}
