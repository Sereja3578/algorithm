<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 16.02.2018
 * Time: 15:46
 */

namespace backend\components;


class AlgorithmTimer
{
    /**
     * @var int
     */
    private static $currentTime;

    /**
     * @return string
     */
    public static function getCurrentTime() : string
    {
        return date('Y-m-d H-i:s', self::$currentTime);
    }

    /**
     * @return int
     */
    public static function getCurrentTimestamp() : int
    {
        return self::$currentTime;
    }

    /**
     * Set current time
     *
     * @param string $currentTime
     */
    public static function setCurrentTime(string $currentTime)
    {
        self::$currentTime = strtotime($currentTime);
    }

    /**
     * @param int $increment
     */
    public static function incrementCurrentTime(int $increment)
    {
        self::$currentTime += $increment;
    }
}

