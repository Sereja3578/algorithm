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
     * Игра в один шаг
     * @message const
     */
    const ONE_STEPS_GAME = 1;

    /**
     * Игра в два шага
     * @message const
     */
    const TWO_STEPS_GAME = 2;

    /**
     * Игра в три шага
     * @message const
     */
    const THREE_STEPS_GAME = 3;

    /**
     * Игра в четыре шага
     * @message const
     */
    const FOUR_STEPS_GAME = 4;

    /**
     * Игра в пять шагов
     * @message const
     */
    const FIVE_STEPS_GAME = 5;
}
