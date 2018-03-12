<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 12.03.2018
 * Time: 12:00
 */

namespace common\components;


use freimaurerei\yii2\amqp\actions\QueueAction as BaseQueueAction;
use freimaurerei\yii2\amqp\AMQP;

class QueueAction extends BaseQueueAction
{

    public $modelClassName = 'common\models\AlgorithmParams';

    /**
     * Routing key must be $className::$actionName
     * Message handler
     * @param \AMQPEnvelope $envelope
     * @param \AMQPQueue    $queue
     * @return bool
     */
    public function handleMessage(\AMQPEnvelope $envelope, \AMQPQueue $queue)
    {
        \Yii::info("Handled message: " . $envelope->getBody());

        $redeliveredCount = $envelope->getHeader('x-redelivered-count') ?: 0;

        $args = $this->bindActionParams(\yii\helpers\Json::decode($envelope->getBody()));

        if ($args === false) {
            $result = false;
        } else {
            $result = $this->callAction(\Yii::createObject($this->modelClassName, $args));
        }

        if ($result) {
            \Yii::info(json_encode([
                'data'   => $envelope->getBody(),
                'route'  => $envelope->getRoutingKey(),
                'status' => AMQP::MESSAGE_STATUS_ACK
            ]), AMQP::$logCategory);
        } else {
            ++$redeliveredCount;
            if ($redeliveredCount > $this->maxRetryCount) {
                \Yii::info("Message could not be processed {$this->maxRetryCount} times. The message was deleted."
                    . json_encode([
                        'data'   => $envelope->getBody(),
                        'route'  => $envelope->getRoutingKey(),
                        'status' => AMQP::MESSAGE_STATUS_ACK
                    ]), AMQP::$logCategory);
            } else {
                if ($this->amqp->delayQueueUsage) {
                    $delayedTime = null;
                    if ($this->retryBoundaryCount <= $redeliveredCount) {
                        $delayedTime = $envelope->getHeader('x-delay');
                    }

                    $delayedTime = $delayedTime ?: ($this->waitingTime * (1 << $redeliveredCount)) * 1000;

                    $this->amqp->getDelayedExchange()->publish(
                        $envelope->getBody(),
                        $queue->getName(),
                        AMQP_NOPARAM,
                        [
                            'headers' => [
                                'x-delay'             => $delayedTime,
                                'delivery_mode'       => 2,
                                'x-redelivered-count' => $redeliveredCount
                            ]
                        ]
                    );
                } else {
                    $this->amqp->send(
                        '',
                        $queue->getName(),
                        $envelope->getBody(),
                        [
                            'x-redelivered-count' => $redeliveredCount
                        ]
                    );
                    \Yii::info(json_encode([
                        'data'   => $envelope->getBody(),
                        'route'  => $envelope->getRoutingKey(),
                        'status' => AMQP::MESSAGE_STATUS_NACK
                    ]), AMQP::$logCategory);
                }
            }
        }
        $queue->ack($envelope->getDeliveryTag());
        return true;
    }

    /**
     * @param $args
     * @return bool
     */
    protected function callAction($args)
    {
        return call_user_func(
            [$this->controller, $this->actionMethod],
            $args
        );
    }
}