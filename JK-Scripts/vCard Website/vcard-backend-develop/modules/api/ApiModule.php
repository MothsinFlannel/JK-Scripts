<?php

namespace app\modules\api;

use app\modules\api\models\Location;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\web\JsonResponseFormatter;
use yii\web\Request;
use yii\web\Response;
use yii\web\User;

/**
 * api module definition class
 */
class ApiModule extends Module
{

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        // No need to set all up if it is a doc
        if (key_exists('text/html', Yii::$app->request->acceptableContentTypes)) {
            return;
        }

        Yii::$app->set('user', [
            'class' => User::class,
            'identityClass' => Location::class,
            'enableAutoLogin' => true,
        ]);

        /** @noinspection SpellCheckingInspection */
        Yii::$app->set('request', [
            'enableCookieValidation' => true,
            'enableCsrfValidation' => false,

            'cookieValidationKey' => 'ZfXH46dvexkwvWdVP2DB8CyLQ8u3YBn3',

            'class' => Request::class,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ]);

        Yii::$app->set('response', [
            'class' => Response::class,
            'formatters' => [
                Response::FORMAT_JSON => [
                    'class' => JsonResponseFormatter::class,
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ]);
    }
}
