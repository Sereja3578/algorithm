<?php

namespace common\components;

use Yii;
use Exception;
use yii\db\BaseActiveRecord;

class KeyValue extends BaseActiveRecord
{
    public static function getDb()
    {
        throw new Exception('Method "getDb" should be overridden in the child class.');
    }

    public static function primaryKey($asArray = false)
    {
        $matches = explode('\\', get_called_class());
        $key = strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst(end($matches))));
        return $asArray ? [$key] : $key;
    }

    public function insert($runValidation = true, $attributes = null)
    {
        throw new Exception('Method not implemented.');
    }

    /**
     * @return KeyValueQuery|object
     */
    public static function find()
    {
        return Yii::createObject(KeyValueQuery::className(), [get_called_class()]);
    }
}
