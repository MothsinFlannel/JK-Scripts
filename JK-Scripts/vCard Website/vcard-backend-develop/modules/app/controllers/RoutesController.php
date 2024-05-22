<?php


namespace app\modules\app\controllers;


use app\models\ext\Route;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\routes\DeleteRouteScript;
use app\modules\app\scripts\routes\EditRouteScript;
use app\modules\app\scripts\routes\GetRouteScript;
use app\modules\app\scripts\routes\RoutesListScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class RoutesController
 * @package app\modules\app\controllers
 */
class RoutesController extends Controller
{
    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionList(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,
                ],
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'sort' => null,
            ];
        });

        return (new RoutesListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $route = Route::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$route->id
            ];
        });

        return (new GetRouteScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'route' => [
                    'name' => 'Route #' . $faker->randomNumber(5, true),
                ],
            ];
        });

        return (new EditRouteScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $route = Route::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$route->id,
                'route' => $route?->toArray(['name'])
            ];
        });

        return (new EditRouteScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $route = Route::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$route->id,
            ];
        });

        return (new DeleteRouteScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}