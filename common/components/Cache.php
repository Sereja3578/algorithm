<?php

namespace common\components;

use Yii;
use dcb9\redis\Cache as RedisCache;

class Cache extends RedisCache
{

    /**
     * @inheritdoc
     */
    protected function flushValues()
    {
        $redis = Yii::$app->pureRedis;
        $keys = $redis->keys($this->keyPrefix.'*');
        return (is_array($keys) && count($keys)) ? $redis->del($keys) : true;
    }
}