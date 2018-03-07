<?php

namespace backend\components\grid;

use kartik\grid\BooleanColumn as KartikBooleanColumn;
use Yii;

class BooleanColumn extends KartikBooleanColumn
{

    public $hAlign = 'center';

    public $vAlign = 'middle';

    public $width = '5%';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_null($this->falseLabel) && is_null($this->trueLabel)) {
            list ($this->falseLabel, $this->trueLabel) = Yii::$app->getFormatter()->booleanFormat;
        }
        parent::init();
    }
}
