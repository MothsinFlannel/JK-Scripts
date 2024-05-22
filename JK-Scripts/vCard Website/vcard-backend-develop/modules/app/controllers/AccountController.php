<?php


namespace app\modules\app\controllers;


use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\account\GetAccountScript;
use app\modules\app\scripts\account\SignInScript;
use app\modules\app\scripts\account\UpdateAccountScript;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use Yii;

/**
 * Class AccountController
 * @package app\modules\app\controllers
 */
class AccountController extends Controller
{
    /**
     * @var string[]
     */
    public $authExcept = ['sign-in'];

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionSignIn(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->random()->one();

            return [
                'email' => $user->email,
                'password' => 'password',
            ];
        });

        return (new SignInScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
            ];
        });

        return (new GetAccountScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'user' => @$user->toArray(['fullName', 'phone']) + [
                        'password' => null
                    ]
            ];
        });

        return (new UpdateAccountScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}