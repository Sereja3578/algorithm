<?php

namespace common\components;

use Yii;

trait MessageTrait
{
    /**
     * @return array
     */
    public static function getMessages()
    {
        $messages = [];

        $className = get_called_class();

        $doc = new ConstDoc($className);
        $comments = $doc->getDocComments();
        foreach ($comments as $constName => $comment) {
            $code = constant(static::class . '::' . $constName);
            if (!empty($comment['comment'])) {
                $category = $comment['params']['message'] ?: 'message';
                $messages[$code] = Yii::t($category, $comment['comment']);
            }
        }

        return $messages;
    }

    /**
     * @param $code
     * @return string|null
     */
    public static function getMessage($code)
    {
        $messages = static::getMessages();

        if (!isset($messages[$code])) {
            $model = static::find()->id($code)->one();
            return $model ? $model->getTitleText() : null;
        } else {
            return $messages[$code];
        }
    }

}
