<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 12.03.2018
 * Time: 11:59
 */

namespace common\components;

use freimaurerei\yii2\amqp\controllers\QueueListener as BaseQueueListener;

class QueueListener extends BaseQueueListener
{
    /**
     * @var string
     */
    public $actionClass = 'common\components\QueueAction';
}