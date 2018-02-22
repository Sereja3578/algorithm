<?php

namespace common\fixtures;

use yii\boost\test\ActiveFixture;

/**
 * Game fixture
 * @see \common\models\Game
 */
class Game extends ActiveFixture
{

    public $modelClass = 'common\models\Game';

    public $backDepends = ['common\fixtures\Strategy'];

    /*[
        'id' => '',
        'name' => '',
        'number_steps' => '',
        'created_at' => '',
        'updated_at' => ''
    ]*/

    public $dataFile = '@common/tests/data/game.php';
}
