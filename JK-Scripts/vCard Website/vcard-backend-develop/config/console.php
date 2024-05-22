<?php

use yii\console\controllers\MigrateController;
use yii\db\Connection;
use yii\queue\file\Queue;
use yii\queue\LogBehavior;
use yii\rbac\DbManager;
use yii\swiftmailer\Mailer;
use yii\web\User;

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'vcard-backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'env', 'queue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@webroot' => '@app/web',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationPath' => [
                '@app/migrations',
                '@yii/rbac/migrations',
            ],
        ],
    ],
    'components' => [
        'env' => require('env.php'),
        'queue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'user' => [
            'class' => User::class,
            'identityClass' => 'app\models\ext\User',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        'db' => [
            'class' => Connection::class,
        ],
        'authManager' => [
            'class' => DbManager::class,
            'itemTable' => '{{%authItem}}',
            'itemChildTable' => '{{%authItemChild}}',
            'assignmentTable' => '{{%authAssignment}}',
            'ruleTable' => '{{%authRule}}',
        ],
        'sendgrid' => [
            'class' => '\app\components\SendGridConnector',
            'apiKey' => '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',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'useFileTransport' => true,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
