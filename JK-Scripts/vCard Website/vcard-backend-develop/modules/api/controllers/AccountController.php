<?php


namespace app\modules\api\controllers;

use app\modules\api\components\Controller;
use app\modules\api\scripts\account\ActivateScript;
use app\modules\api\scripts\account\LoginScript;
use Throwable;
use vr\core\utils\HttpCode;
use Yii;

/**
 * Class AccountController
 * @package app\modules\api\controllers
 */
class AccountController extends Controller
{
    /**
     * @return array
     * @throws Throwable
     */
    public function actionLogin(): array
    {
        return (new LoginScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     *
     * @return array
     * @throws Throwable
     */
    public function actionActivate(): array
    {
        return (new ActivateScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     *
     */
    public function actionLogout(): void
    {
        Yii::$app->user->logout();
        Yii::$app->response->statusCode = HttpCode::NO_CONTENT;
    }
}