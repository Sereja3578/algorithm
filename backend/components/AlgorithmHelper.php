<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 14.02.2018
 * Time: 16:21
 */

namespace backend\components;

use common\components\KeyValue;
use common\components\QuoteChunkSearch;

//@todo перенести в quoteHelper, а в этом класе сделать доступ ко всем входным параметрам.
class AlgorithmHelper
{
    /**
     * @message Котировка повысилась
     */
    const QUOTE_INCREASE = '1';
    /**
     * @message Котировка понизилась
     */
    const QUOTE_DECREASE = '-1';
    /**
     * @message Котировка не изменилась
     */
    const QUOTE_NOT_CHANGED = '0';

    /**
     * @param string $assetName
     * @param $startDate
     * @param $endDate
     * @return KeyValue[]
     */
    public static function getQuotes($assetName, $startDate, $endDate)
    {
        $quoteChunkSearch = new QuoteChunkSearch();
        $quoteChunkSearch->dateInterval = $startDate . ' - ' . $endDate;
        $quoteChunkSearch->assetName = $assetName;

        $quotes = $quoteChunkSearch->search()->getModels();

        return $quotes;
    }

    public static function getCurrentQuote(int $currentDate, array $quotes)
    {
        $items = [];
        foreach ($quotes as $quote) {
            $timestamp = strtotime($quote['timestamp']);
            if ($timestamp >= $currentDate) {
                $items[$timestamp] =  $quote;
            }
        }

        // Так как котировки точно на текущее время может не быть, выбираем максимально приближеную по времени котировку.
        if (!empty($items)) {
            $minKey = min(array_keys($items));

            return $items[$minKey];
        }

        return null;
    }
}