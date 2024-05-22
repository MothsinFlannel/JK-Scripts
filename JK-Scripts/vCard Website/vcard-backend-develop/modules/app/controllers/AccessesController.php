<?php


namespace app\modules\app\controllers;


use app\models\Access;
use app\models\ext\Route;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\accesses\GrantAccessScript;
use app\modules\app\scripts\accesses\RevokeAccessScript;
use Throwable;
use vr\api\components\VerboseException;
use Yii;

/**
 * Class AccessesController
 * @package app\modules\app\controllers
 */
class AccessesController extends Controller
{
    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionGrant(): array
    {
        $this->checkInputParams(function () {
            $admin = User::find()->active()->admin()->random()->one();

            $user = User::find()->active()->routeman()->random()->one();
            $route = Route::find()->random()->one();

            return [
                'accessToken' => @$admin->accessToken,
                'access' => [
                    'userId' => @$user->id,
                    'referenceId' => @$route->id,
                    'referenceType' => $route ? $route::tableName() : null,
                ]
            ];
        });

        return (new GrantAccessScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionRevoke(): array
    {
        $this->checkInputParams(function () {
            $access = Access::find()->random()->one();

            return [
                'accessToken' => @$access->user->accessToken,
                'id' => @$access->id,
            ];
        });

        return (new RevokeAccessScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}