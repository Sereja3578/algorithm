<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 19.02.2018
 * Time: 10:41
 */

namespace backend\components;


use common\components\KeyValue;
use common\components\QuoteChunkSearch;
use common\models\AlgorithmParams;
use common\models\Asset;
use yii\base\InvalidParamException;
use Yii;

class QuoteHelper
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
     * @var AlgorithmParams
     */
    public $algorithmParamsModel;

    /**
     * QuoteHelper constructor.
     * @param $algorithmParamsModel
     */
    function __construct($algorithmParamsModel)
    {
        $this->algorithmParamsModel = $algorithmParamsModel;
    }

    /**
     * @return KeyValue[]
     */
    public function getQuotes()
    {
        $assetName = Asset::findOne(['id' => $this->algorithmParamsModel->asset_id])->code;
        $quoteChunkSearch = new QuoteChunkSearch();
        $quoteChunkSearch->dateInterval = $this->algorithmParamsModel->t_start . ' - ' . $this->algorithmParamsModel->t_end;
        $quoteChunkSearch->assetName = $assetName;

        $quotes = $quoteChunkSearch->search()->getModels();

        return $quotes;
    }

    /**
     * @param string|integer $currentDate
     * @return array|null
     */
    public function getCurrentQuote($currentDate)
    {
        if(!is_int($currentDate)) {
            $currentDate = strtotime($currentDate);
        }

        $items = [];
        foreach ($this->getQuotes() as $quote) {
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

    /**
     * @param int $decrement
     * @return int
     */
    public static function getDecrementedTimestamp(int $decrement): int
    {
        return self::$currentTime - $decrement;
    }

    /**
     * @param int $decrement
     * @return string
     */
    public static function getDecrementedTime(int $decrement): string
    {
        return date('Y-m-d H-i:s', self::$currentTime - $decrement);
    }
}