<?php

namespace common\models;

use common\models\base\StrategyBase;

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
     * @return null|Strategy
     */
    public static function getBestStrategy()
    {
        return self::findOne(['best_strategy' => self::BEST_STRATEGY]);
    }
}
