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
     * @return bool
     */
    public function beforeValidate()
    {
        if(parent::beforeValidate()){
            $this->games = serialize($this->games);
            $this->rates = implode(', ', $this->rates);
            return true;
        };
        return false;
    }
}
