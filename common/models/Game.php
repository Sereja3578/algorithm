<?php

namespace common\models;

use common\models\base\GameBase;

/**
 * Game
 * @see \common\models\query\GameQuery
 */
class Game extends GameBase
{
    /**
     * @message Игра в один шаг
     */
    const ONE_STEPS_GAME = 1;

    /**
     * @message Игра в два шага
     */
    const TWO_STEPS_GAME = 2;

    /**
     * @message Игра в три шага
     */
    const THREE_STEPS_GAME = 3;

    /**
     * @message Игра в четыре шага
     */
    const FOUR_STEPS_GAME = 4;

    /**
     * @message Игра в пять шагов
     */
    const FIVE_STEPS_GAME = 5;
}
