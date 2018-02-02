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
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    // set target language to be Russian
    'language' => 'ru',
    // set source language to be Russian
    'sourceLanguage' => 'ru',
];
