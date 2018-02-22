<?php

namespace common\fixtures;

use yii\boost\test\ActiveFixture;

/**
 * Strategy fixture
 * @see \common\models\Strategy
 */
class Strategy extends ActiveFixture
{

    public $modelClass = 'common\models\Strategy';

    public $depends = [
        'common\fixtures\AlgorithmParams',
        'common\fixtures\Game'
    ];

    /*[
        'id' => '',
        'algorithm_params_id' => '',
        'timestamp' => '',
        'iteration_number' => '',
        'money_amount' => '',
        'game_id' => '',
        'rate_amount' => '',
        'forecast' => '',
        'result' => '',
        'best_strategy' => ''
    ]*/

    public $dataFile = '@common/tests/data/strategy.php';
}
