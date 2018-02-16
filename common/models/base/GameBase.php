<?php

namespace common\models\base;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "game".
 *
 * @property integer $id
 * @property string $name
 * @property integer $number_steps
 * @property string $created_at
 * @property string $updated_at
 */
class GameBase extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number_steps'], 'integer'],
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
            [['name', 'number_steps'], 'required'],
            [['name'], 'string', 'max' => 255],
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
            'name' => Yii::t('models', 'Название игры'),
            'number_steps' => Yii::t('models', 'Число шагов'),
            'created_at' => Yii::t('models', 'Created At'),
            'updated_at' => Yii::t('models', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\GameQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\GameQuery(get_called_class());
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
        return Yii::t('models', 'Game');
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
}
