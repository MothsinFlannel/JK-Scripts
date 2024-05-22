<?php

use yii\db\Connection;

$params = require __DIR__ . '/params.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'vcard-backend-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'pgsql:host=localhost;dbname=vcard',
            'username' => 'postgres',
            'password' => 'password',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\ext\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => $params,
];
