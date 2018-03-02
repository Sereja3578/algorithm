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
     * @var int
     */
    private static $endTime;

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

    /**
     * @return string
     */
    public static function getEndTime(): string
    {
        return  date('Y-m-d H-i:s', self::$endTime);
    }

    /**
     * @return int
     */
    public static function getEndTimestamp(): int
    {
        return  self::$endTime;
    }

    /**
     * @param string $endTime
     */
    public static function setEndTime(string $endTime): void
    {
        self::$endTime = strtotime($endTime);
    }

    /**
     * @return bool
     */
    public static function checkTime()
    {
        if (self::$currentTime < self::$endTime) {
            return true;
        }
        return false;
    }
}

