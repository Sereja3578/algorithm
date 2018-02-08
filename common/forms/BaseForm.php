<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 07.02.2018
 * Time: 13:12
 */

namespace common\forms;

use common\components\Model;
use Yii;

class BaseForm extends Model implements FormInterface
{
    /**
     * @return string
     */
    public function getFormTitle()
    {
        return Yii::t('models', 'Заголовок формы');
    }
}