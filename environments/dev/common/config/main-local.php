<?php
$config = [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=algorithm',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'on afterOpen' => function (yii\base\Event $event) {
                /* @var $db yii\db\Connection */
                $db = $event->sender;
                $db->createCommand('SET time_zone = :timeZone;', ['timeZone' => date('P')])->execute();
            }
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'pureRedisQuotes' => function () {
            $redis = new \Redis;
            if (!$redis->connect('127.0.0.1', '6379')) {
                throw new \RedisException('Can not connect.');
            }
            $password = '';
            if ($password && !$redis->auth($password)) {
                throw new \RedisException('Can not authenticate.');
            }
            if (!$redis->select(intval('0'))) {
                throw new \RedisException('Can not select database.');
            }
            return $redis;
        },
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'adminlte' => '@vendor/dmstr/yii2-adminlte-asset/gii/templates/crud/simple',
                ]
            ]
        ],
    ];
}

return $config;
