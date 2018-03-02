<?php

namespace common\models\base;

use Yii;
use common\models\Asset;
use yii\db\Expression;
use common\models\Strategy;

/**
 * This is the model class for table "algorithm_params".
 *
 * @property integer $id
 * @property integer $iterations
 * @property double $k_lucky
 * @property integer $asset_id
 * @property string $amount_start
 * @property string $amount_end
 * @property string $t_start
 * @property string $t_end
 * @property double $deviation_from_amount_end
 * @property string $games
 * @property integer $t_next_start_game
 * @property string $rates
 * @property integer $number_rates
 * @property double $rate_coef
 * @property double $probability_play
 * @property integer $use_fake_coefs
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Asset $asset
 * @property Strategy[] $strategies
 */
class AlgorithmParamsBase extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'algorithm_params';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['use_fake_coefs'], 'integer'],
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
                return is_int($value) ? date('Y-m-d H:i:s', $value) : $value;
            }],
            [[
                't_start',
                't_end',
                'created_at',
                'updated_at'
            ], 'date', 'format' => 'php:Y-m-d H:i:s'],
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
            [[
                'probability_play',
                'use_fake_coefs'
            ], 'default', 'value' => '0'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'iterations' => Yii::t('models', 'Количество итераций'),
            'k_lucky' => Yii::t('models', 'Коэффициент удачливости игрока'),
            'asset_id' => Yii::t('models', 'Валютная пара'),
            'amount_start' => Yii::t('models', 'Начальная сумма денег'),
            'amount_end' => Yii::t('models', 'Конечная сумма денег'),
            't_start' => Yii::t('models', 'Начальное время'),
            't_end' => Yii::t('models', 'Конечное время'),
            'deviation_from_amount_end' => Yii::t('models', 'Допустимое отклонение текущей суммы от конечной'),
            'games' => Yii::t('models', 'Игры и с указанием шанса'),
            't_next_start_game' => Yii::t('models', 'Время задержки между играми'),
            'rates' => Yii::t('models', 'Ставки через запятую'),
            'number_rates' => Yii::t('models', 'Максимальное число ставок'),
            'rate_coef' => Yii::t('models', 'Коэффициент повышения ставки'),
            'probability_play' => Yii::t('models', 'Вероятность начала игры'),
            'use_fake_coefs' => Yii::t('models', 'Использовать фейковые коэффициенты'),
            'created_at' => Yii::t('models', 'Создано в'),
            'updated_at' => Yii::t('models', 'Обновленов в'),
        ];
    }

    /**
     * @return \common\models\query\AssetQuery|\yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(Asset::className(), ['id' => 'asset_id']);
    }

    /**
     * @return \common\models\query\StrategyQuery|\yii\db\ActiveQuery
     */
    public function getStrategies()
    {
        return $this->hasMany(Strategy::className(), ['algorithm_params_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AlgorithmParamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AlgorithmParamsQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function singularRelations()
    {
        return [
            'asset' => [
                'hasMany' => false,
                'class' => 'common\models\Asset',
                'link' => ['id' => 'asset_id'],
                'direct' => true,
                'viaTable' => false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function pluralRelations()
    {
        return [
            'strategies' => [
                'hasMany' => true,
                'class' => 'common\models\Strategy',
                'link' => ['algorithm_params_id' => 'id'],
                'direct' => false,
                'viaTable' => false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function datetimeAttributes()
    {
        return [
            't_start',
            't_end',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'Параметры алгоритмов');
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
     * @param array $config
     * @return Strategy
     */
    public function newStrategy(array $config = [])
    {
        $model = new Strategy($config);
        $model->algorithm_params_id = $this->id;
        return $model;
    }

    /**
     * @param string|array|Expression $condition
     * @param array $params
     * @param string|array|Expression $orderBy
     * @return array
     */
    public function assetIdListItems($condition = null, $params = [], $orderBy = null)
    {
        return Asset::findListItems($condition, $params, $orderBy);
    }

    /**
     * @param array $condition
     * @param string|array|Expression $orderBy
     * @return array
     */
    public function assetIdFilterListItems(array $condition = [], $orderBy = null)
    {
        return Asset::findFilterListItems($condition, $orderBy);
    }
}
