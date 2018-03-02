<?php

namespace common\fixtures;

use yii\boost\test\ActiveFixture;

/**
 * Algorithm params fixture
 * @see \common\models\AlgorithmParams
 */
class AlgorithmParams extends ActiveFixture
{

    public $modelClass = 'common\models\AlgorithmParams';

    public $depends = ['common\fixtures\Asset'];

    public $backDepends = ['common\fixtures\Strategy'];

    /*[
        'id' => '',
        'iterations' => '',
        'k_lucky' => '',
        'asset_id' => '',
        'amount_start' => '',
        'amount_end' => '',
        't_start' => '',
        't_end' => '',
        'deviation_from_amount_end' => '',
        'games' => '',
        't_next_start_game' => '',
        'rates' => '',
        'number_rates' => '',
        'rate_coef' => '',
        'probability_play' => '',
        'use_fake_coefs' => '',
        'created_at' => '',
        'updated_at' => ''
    ]*/

    public $dataFile = '@common/tests/data/algorithm_params.php';
}
