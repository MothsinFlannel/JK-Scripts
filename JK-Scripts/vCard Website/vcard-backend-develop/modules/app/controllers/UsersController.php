<?php


namespace app\modules\app\controllers;


use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\users\DeleteUserScript;
use app\modules\app\scripts\users\GetUserScript;
use app\modules\app\scripts\users\UpsertUserScript;
use app\modules\app\scripts\users\UsersListScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class UsersController
 * @package app\modules\app\controllers
 */
class UsersController extends Controller
{
    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionList(): array
    {
        $this->checkInputParams(function () {
            $admin = User::find()->active()->one();

            return [
                'accessToken' => @$admin->accessToken,
                'filters' => [
                    'query' => null
                ],
                'sort' => null,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new UsersListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $admin = User::find()->active()->one();
            $user = User::find()->one();

            return [
                'accessToken' => @$admin->accessToken,
                'id' => @$user->id,
            ];
        });

        return (new GetUserScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $admin = User::find()->active()->one();
            $faker = Factory::create();

            return [
                'accessToken' => @$admin->accessToken,
                'user' => [
                    'email' => $faker->safeEmail,
                    'fullName' => $faker->name,
                    'phone' => $faker->phoneNumber,
                    'role' => User::ROLE_ROUTEMAN,
                    'companyId' => null,
                    'isActive' => true,
                    'password' => 'password',
                    'operatesInStates' => null,
                ]
            ];
        });

        return (new UpsertUserScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $admin = User::find()->active()->one();
            $user = User::find()->one();

            return [
                'accessToken' => @$admin->accessToken,
                'id' => @$user->id,
                'user' => @$user->toArray([
                    'email',
                    'fullName',
                    'phone',
                    'role',
                    'companyId',
                    'isActive',
                    'operatesInStates'
                ])
            ];
        });

        return (new UpsertUserScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $admin = User::find()->active()->one();
            $user = User::find()->one();

            return [
                'accessToken' => @$admin->accessToken,
                'id' => @$user->id,
            ];
        });

        return (new DeleteUserScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}