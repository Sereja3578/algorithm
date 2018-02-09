<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 09.02.2018
 * Time: 18:13
 */

namespace backend\components;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\Controller as BaseController;

class Controller extends BaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => ['created_at'],
                'updatedAtAttribute' => ['updated_at'],
                'value' => new Expression('NOW()'),
            ]
        ]);
    }
}