<?php

use vr\api\components\ErrorHandler;
use yii\db\Connection;
use yii\queue\file\Queue;
use yii\queue\LogBehavior;
use yii\rbac\DbManager;
use yii\swiftmailer\Mailer;

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'vcard-backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'env', 'queue'],
    'aliases' => [
        '@bower' => '@vendor/yidas/yii2-bower-asset/bower',
        '@npm' => '@vendor/npm-asset',
    ],
    'name' => 'vCard Backend',
    'defaultRoute' => 'app/doc/index',
    'language' => 'en-US',

    'components' => [
        'env' => require('env.php'),
        'queue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
        ],
        'request' => [
            'cookieValidationKey' => 'BHy9KVxkB4kb5yMYuwZ8tDFjcyLd9XSW',
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\ext\User',
            'enableAutoLogin' => true,
        ],
        'sendgrid' => [
            'class' => '\app\components\SendGridConnector',
            'apiKey' => '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',
        ],
        'security' => [
            'passwordHashCost' => 10,
        ],
        'errorHandler' => [
            'class' => ErrorHandler::class,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST v1/login' => 'api/account/login',
                'POST v1/activate' => 'api/account/activate',
                'POST v1/logout' => 'api/account/logout',

                'GET v1/options' => 'api/location/options',
                'PUT v1/terminal' => 'api/location/terminal',
                'PUT v1/sale' => 'api/location/sale',
                'PUT v1/redemption' => 'api/location/redemption',
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:200',
                    ],
                    'maxFileSize' => 102400, // 100MB
                ]
            ],
        ],
        'mailer' => [
            'class' => Mailer::class,
            'useFileTransport' => true,
        ],
        'db' => [
            'class' => Connection::class,
            'queryCacheDuration' => 60,
            'enableQueryCache' => true,
            'enableSchemaCache' => !YII_DEBUG,
            'queryCache' => 'cache',
            'schemaCache' => 'cache',
            'enableProfiling' => false,
        ],
        'authManager' => [
            'class' => DbManager::class,
            'itemTable' => '{{%authItem}}',
            'itemChildTable' => '{{%authItemChild}}',
            'assignmentTable' => '{{%authAssignment}}',
            'ruleTable' => '{{%authRule}}',
        ],
        'formatter' => [
            'dateFormat' => 'MM/dd/yyyy',
            'currencyCode' => 'USD',
        ],
    ],
    'params' => $params,

    'modules' => [
        'app' => [
            'class' => 'app\modules\app\AppModule',
        ],
        'reports' => [
            'class' => 'app\modules\reports\ReportsModule',
        ],
        'render' => [
            'class' => 'app\modules\render\RenderModule',
        ],
        'api' => [
            'class' => 'app\modules\api\ApiModule',
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', 'localhost'],
    ];
}

return $config;
