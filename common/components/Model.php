<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 08.02.2018
 * Time: 10:58
 */

namespace common\components;

use yii\base\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * @return array
     */
    public function getAttributesNames()
    {
        return array_keys($this->getAttributes());
    }
}