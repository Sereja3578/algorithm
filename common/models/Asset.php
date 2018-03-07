<?php

namespace common\models;

use common\models\base\AssetBase;

/**
 * Asset
 * @see \common\models\query\AssetQuery
 */
class Asset extends AssetBase
{
    /**
     * EUR/USD
     * @message const
     */
    const EURUSD = 1;

    /**
     * EUR/RUB
     * @message const
     */
    const EURRUB = 2;

    /**
     * BTC/USD
     * @message const
     */
    const BTCUSD = 3;

    /**
     * USD/RUB
     * @message const
     */
    const USDRUB = 4;
}
