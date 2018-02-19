<?php

namespace common\models\base;

use Yii;
use common\models\AlgorithmParams;
use yii\db\Expression;

/**
 * This is the model class for table "asset".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AlgorithmParams[] $algorithmParams
 */
class AssetBase extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'asset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'created_at',
                'updated_at'
            ], 'filter', 'filter' => function ($value) {
                return is_int($value) ? date('Y-m-d H:i:s', $value) : $value;
            }],
            [[
                'created_at',
                'updated_at'
            ], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string', 'max' => 25],
            [[
                'created_at',
                'updated_at'
            ], 'default', 'value' => new Expression('CURRENT_TIMESTAMP')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'name' => Yii::t('models', 'Название ассета'),
            'code' => Yii::t('models', 'Код ассета'),
            'created_at' => Yii::t('models', 'Создано в'),
            'updated_at' => Yii::t('models', 'Обновленов в'),
        ];
    }

    /**
     * @return \common\models\query\AlgorithmParamsQuery|\yii\db\ActiveQuery
     */
    public function getAlgorithmParams()
    {
        return $this->hasMany(AlgorithmParams::className(), ['asset_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AssetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AssetQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function pluralRelations()
    {
        return [
            'algorithmParams' => [
                'hasMany' => true,
                'class' => 'common\models\AlgorithmParams',
                'link' => ['asset_id' => 'id'],
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
            'created_at',
            'updated_at'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'Ассеты');
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
     * @return AlgorithmParams
     */
    public function newAlgorithmParam(array $config = [])
    {
        $model = new AlgorithmParams($config);
        $model->asset_id = $this->id;
        return $model;
    }
}
