<?php
/**
 * Created by PhpStorm.
 * User: Maksi
 * Date: 2/28/2018
 * Time: 10:13 PM
 */

namespace backend\components;


class WinCoefHelper
{
    /**
     * @var array
     */
    public static $fakeWinCoefs;

    /**
     * @var array
     */
    public static $winCoefs;

    /**
     * @var int
     */
    public static $useFakeCoefs;

    /**
     * @param array $preparedGameResults
     * @return array
     */
    public static function getFakeWinCoefs($preparedGameResults)
    {
        $fakeWinCoefs = [];
        foreach ($preparedGameResults as $time => $result) {
            $fakeWinCoefs[$time] = mt_rand(1,1000) / 100;
        }

        return $fakeWinCoefs;
    }

    /**
     * @return array
     */
    public static function getWinCoefs()
    {
        return self::$winCoefs;
    }

    /**
     * @param $fakeWinCoefs
     */
    public static function setFakeWinCoefs($fakeWinCoefs)
    {
        self::$fakeWinCoefs = $fakeWinCoefs;
    }

    /**
     * @param $winCoefs
     */
    public static function setWinCoefs($winCoefs)
    {
        self::$winCoefs = $winCoefs;
    }

    /**
     * @param $currentDate
     * @param null $winCoefs
     * @return float|null
     */
    public static function getCurrentCoef($currentDate, $winCoefs = null): ?float
    {
        if(!$winCoefs) {
            $winCoefs = self::$useFakeCoefs ? self::$fakeWinCoefs : self::$winCoefs;
        }

        if(!is_int($currentDate)) {
            $currentDate = strtotime($currentDate);
        }

        $items = [];
        foreach ($winCoefs as $time => $coef) {
            $timestamp = strtotime($time);
            if ($timestamp >= $currentDate) {
                $items[$timestamp] =  $coef;
            }
        }

        /*
         * Так как коэффициента точно на текущее время может не быть,
         * выбираем максимально приближенный по времени коэффициент
         *
         */
        if (!empty($items)) {
            $minKey = min(array_keys($items));

            return $items[$minKey];
        }

        return null;
    }
}