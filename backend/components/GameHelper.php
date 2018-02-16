<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 15.02.2018
 * Time: 15:16
 */

namespace backend\components;


use common\models\Game;

class GameHelper
{

    const EXPIRATION = 5;

    /**
     * @var array
     */
    private static $params;

    /**
     * @return array
     */
    public static function getParams(): array
    {
        return self::$params;
    }

    /**
     * @param array $params
     */
    public static function setParams(array $params)
    {
        self::$params = $params;
    }

    public static function startGame(array $games)
    {
        $gameId = self::chooseGameByChance($games);
        $game = self::getGame($gameId);

        if ($game) {
            for ($i = 0; $i <= $game->number_steps; $i++) {

            }
        }

        var_dump($game);
        exit();
    }

    public static function chooseGameByChance(array $games)
    {
        $rand = mt_rand(1, 100);

        $segment = 0;
        foreach ($games as $gameId => $chance) {
            $segment += $chance;
            if ($rand <= $segment) {
                return $gameId;
            }
        }

        return null;
    }

    public static function getGame($gameId)
    {
        return Game::findOne(['id' => $gameId]);
    }

    public static function getExpectedResultGameSteps(array $quotes)
    {
        $preparedResultGameSteps = [];
        foreach ($quotes as $key => $quote) {
            // Стартовая котировка
            if ($key == 0) {
                continue;
            }

            if ($quotes[$key - 1]['ask'] > $quote['ask']) {
                $preparedResultGameSteps[$quote['timestamp']] = AlgorithmHelper::QUOTE_DECREASE;
            } elseif ($quotes[$key - 1]['ask'] < $quote['ask']) {
                $preparedResultGameSteps[$quote['timestamp']] = AlgorithmHelper::QUOTE_INCREASE;
            } elseif ($quotes[$key - 1]['ask'] == $quote['ask']) {
                $preparedResultGameSteps[$quote['timestamp']] = AlgorithmHelper::QUOTE_NOT_CHANGED;
            }
        }

        return $preparedResultGameSteps;
    }

    public function playGameOrNotPlay()
    {

    }
}