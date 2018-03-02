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

        /*
         * Устанавливаем дату начала получения котировок на 1 минуту меньше,
         * чтобы при первой игре можно было получить котировки за дату
         * меньше начальной даты игры. Например игрок начал играть в 10 часов 15 минут и 15 секунд,
         * а начало работы алгоритма и соответственно получения котировок было 10 часов
         * 15 минут и 10 секунд. В игре было 3 шага с экспирацией по 5 секунд.
         * При получении прогноза на каждый шаг, мы берем его по времени, сначала
         * за время окончания игры, потом за время полученной котировки, минус экспирация.
         * Получается что при игре в три шага, с тем условем, что игра началась в
         * 10 часов 15 минут и 15 секунд, то первую котировку мы получим за
         * 10 часов 15 минут и 15 секунд, вторую за 10 часов 15 минут и 10 секунд,
         * а третью 10 часов 15 минут и 5 секунд, но изначально бы мы получили
         * набор котировок только с 10 часов 15 минут и 10 секунд (время начала алгоритма)
         * и этой котировки бы не было в данных. Соответственно нужно быть котировки хотябы
         * на 1 минуту раньше времени начала алгоритма. Причем этот показатель может увеличиваться,
         * при большей экспирации шага.
         */
        $dateTime = new \DateTime($this->algorithmParamsModel->t_start);
        $dateInterval = new \DateInterval('PT1M');
        $startTime = $dateTime->sub($dateInterval)->format('Y-m-d H:i');

        $quoteChunkSearch->dateInterval = $startTime . ' - ' . $this->algorithmParamsModel->t_end;
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

        /*
         * Так как котировки точно на текущее время может не быть,
         * выбираем максимально приближеную по времени котировку.
         */
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