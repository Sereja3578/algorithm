<?php

namespace common\components;

use yii\i18n\MissingTranslationEvent;
use Yii;

class TranslationEventHandler
{

    public static function missing(MissingTranslationEvent $event)
    {
        if (YII_ENV_PROD) {
            Yii::error("@MISSING: {$event->category}.{$event->message} FOR LANGUAGE {$event->language} @");
        }
    }
}
