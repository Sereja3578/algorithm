<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'urlManager' => [
            'class' => 'codemix\localeurls\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:\w+>/<controller:\w+>/<action:(\w|-)+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<module>/<controller>/<action>',
            ],
            'languages' => ['en', 'ru'],
            'keepUppercaseLanguageCode' => true,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'ru',
                    'basePath' => '@common/messages',
                    'forceTranslation' => true,
                    'on missingTranslation' => ['common\components\TranslationEventHandler', 'missing'],
                ],
            ]
        ],
        'cache' => [
            'class' => 'common\components\Cache',
            'keyPrefix' => 'cache:',
            'redis' => 'redis'
        ],
        'authManager' => [
            'class' => 'backend\components\DbManager',
            'cache' => 'cache'
        ],
        'amqp' => [
            'class' => 'freimaurerei\yii2\amqp\AMQP',
            'config' => [
                'exchanges' => [
                    'algorithmExchange' => [
                        'config' => [
                            'flags' => \AMQP_DURABLE,
                            'type'  => \AMQP_EX_TYPE_DIRECT,
                        ]
                    ],
                ],
                'queues' => [
                    'console\controllers\AlgorithmQueueController::actionRun' => [
                        'binds' => [
                            'algorithmExchange' => [
                                'run',
                            ],
                        ],
                        'config' => [
                            'flags' => \AMQP_DURABLE,
                        ]
                    ],
                ],
            ],
        ],
    ],
    // set target language to be Russian
    'language' => 'ru',
    // set source language to be Russian
    'sourceLanguage' => 'ru',
];
