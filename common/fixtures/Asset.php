<?php

namespace common\fixtures;

use yii\boost\test\ActiveFixture;

/**
 * Asset fixture
 * @see \common\models\Asset
 */
class Asset extends ActiveFixture
{

    public $modelClass = 'common\models\Asset';

    public $backDepends = ['common\fixtures\AlgorithmParams'];

    /*[
        'id' => '',
        'name' => '',
        'code' => '',
        'created_at' => '',
        'updated_at' => ''
    ]*/

    public $dataFile = '@common/tests/data/asset.php';
}
