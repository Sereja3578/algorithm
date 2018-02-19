<?php

namespace common\models;

use common\models\base\AlgorithmParamsBase;

/**
 * Algorithm params
 * @see \common\models\query\AlgorithmParamsQuery
 */
class AlgorithmParams extends AlgorithmParamsBase
{
    /**
     * @var array
     */
    public $quotes;
    /**
     * @var array
     */
    public $coefs;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['quotes', 'coefs'], 'safe', 'on' => 'run']
        ]);
    }
}
