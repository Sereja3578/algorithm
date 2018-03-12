<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name' => Yii::t('main', 'Алгоритм'),
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'user' => [
            'class' => 'backend\modules\user\Module'
        ],
        'algorithm' => [
            'class' => 'backend\modules\algorithm\Module',
        ],
        'gridview' => [
            'class' => 'backend\modules\gridview\Module',
        ],
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',

            // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                kartik\datecontrol\Module::FORMAT_DATE => 'dd-MM-yyyy',
                kartik\datecontrol\Module::FORMAT_TIME => 'HH:mm:ss a',
                kartik\datecontrol\Module::FORMAT_DATETIME => 'dd-MM-yyyy HH:mm:ss a',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                kartik\datecontrol\Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
                kartik\datecontrol\Module::FORMAT_TIME => 'php:H:i:s',
                kartik\datecontrol\Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            // set your display timezone
            'displayTimezone' => 'Europe/Minsk',

            // set your timezone for date saved to db
            'saveTimezone' => 'UTC',

            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

            // use ajax conversion for processing dates from display format to save format.
            'ajaxConversion' => true,

            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                kartik\datecontrol\Module::FORMAT_DATE => ['type' => 2, 'pluginOptions' => ['autoclose' => true]], // example
                kartik\datecontrol\Module::FORMAT_DATETIME => ['type' => 2, 'pluginOptions' => ['autoclose' => true]], // setup if needed
                kartik\datecontrol\Module::FORMAT_TIME => ['type' => 2, 'pluginOptions' => ['autoclose' => true]], // setup if needed
            ],

            // custom widget settings that will be used to render the date input instead of kartik\widgets,
            // this will be used when autoWidget is set to false at module or widget level.
            'widgetSettings' => [
                kartik\datecontrol\Module::FORMAT_DATE => [
                    'class' => 'yii\jui\DatePicker', // example
                    'options' => [
                        'dateFormat' => 'php:d/M/Y',
                        'options' => ['class' => 'form-control'],
                    ]
                ]
            ]
            // other settings
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl' => ['auth/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-green',
                ],
            ],
        ],
    ],
    'as AccessBehavior' => [
        'class' => 'backend\components\rbac\AccessBehavior',
        'rules' => [
            'auth' => [
                [
                    'actions' => ['login', 'request-password-reset', 'reset-password'],
                    'allow' => true,
                ],
                [
                    'actions' => ['logout'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
            'site' => [
                [
                    'actions' => ['error'],
                    'allow' => true,
                ],
                [
                    'actions' => ['index'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
