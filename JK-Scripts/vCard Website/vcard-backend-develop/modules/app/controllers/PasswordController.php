<?php

namespace app\modules\app\controllers;

use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\components\ExpiredPasswordFilter;
use app\modules\app\scripts\password\ChangePasswordScript;
use app\modules\app\scripts\password\ForgotPasswordScript;
use app\modules\app\scripts\password\RecoverPasswordScript;
use app\modules\app\scripts\password\ResetPasswordScript;
use Throwable;
use vr\api\components\VerboseException;
use Yii;

/**
 *
 */
class PasswordController extends Controller
{
    /**
     * @var string[]
     */
    public $authExcept = ['forgot', 'recover'];

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'expiredPassword' => [
                'class' => ExpiredPasswordFilter::class,
                'except' => ['change']
            ],
        ]);
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionForgot(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->random()->one();

            return [
                'email' => @$user->email,
            ];
        });

        return (new ForgotPasswordScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionRecover(): array
    {
        $this->checkInputParams(function () {
            return [
                'recoveryToken' => null,
                'password' => null,
            ];
        });

        return (new RecoverPasswordScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionReset(): array
    {
        $this->checkInputParams(function () {
            $admin = User::find()->active()->one();
            $user = User::find()->random()->one();

            return [
                'accessToken' => @$admin->accessToken,
                'userId' => @$user->id,
                'password' => null,
            ];
        });

        return (new ResetPasswordScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionChange(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->andWhere('[[passwordExpiresAt]] <= now()')->random()->one();

            return [
                'accessToken' => $user?->accessToken,
                'oldPassword' => 'password',
                'newPassword' => 'password',
            ];
        });

        return (new ChangePasswordScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}