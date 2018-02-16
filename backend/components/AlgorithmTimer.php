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
     * @return int
     */
    public static function getCurrentTime() : int
    {
        return self::$currentTime;
    }

    /**
     * Set current time
     *
     * @param int $currentTime
     */
    public static function setCurrentTime(int $currentTime)
    {
        self::$currentTime = $currentTime;
    }

    /**
     * @param int $increment
     */
    public static function incrementCurrentTime(int $increment)
    {
        self::$currentTime += $increment;
    }
}

