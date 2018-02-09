<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
        'message' => [
            'class' => 'console\controllers\MessageController',
            'sourcePathMap' => [
                '@backend' => [],
                '@common' => [],
                '@console' => [],
                '@frontend' => [],
                '@vendor' => []
            ]
        ],
        'migrate' => [
            'class' => 'console\controllers\MigrateController',
            'templateFile' => '@app/templates/migrations/migration.php',
            'generatorTemplateFiles' => [
                'create_table' => '@app/templates/migrations/createTableMigration.php',
                'drop_table' => '@app/templates/migrations/dropTableMigration.php',
                'add_column' => '@yii/views/addColumnMigration.php',
                'drop_column' => '@yii/views/dropColumnMigration.php',
                'create_junction' => '@app/templates/migrations/createTableMigration.php',
            ]
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
