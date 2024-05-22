<?php


namespace app\modules\app\controllers;


use app\models\Clerk;
use app\models\ext\Location;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\clerks\ClerksListScript;
use app\modules\app\scripts\clerks\DeleteClerkScript;
use app\modules\app\scripts\clerks\EditClerkScript;
use app\modules\app\scripts\clerks\GetClerkScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class ClerksController
 * @package app\modules\app\controllers
 */
class ClerksController extends Controller
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
            $clerk = Clerk::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,
                ],
                'locationId' => $clerk->locationId,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new ClerksListScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $clerk = Clerk::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$clerk->id,
            ];
        });

        return (new GetClerkScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $location = Location::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'clerk' => [
                    'locationId' => $location->id,
                    'fullName' => $faker->name,
                    'pin' => (string)$faker->randomNumber(4, true),
                    'isManager' => $faker->boolean
                ]
            ];
        });

        return (new EditClerkScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $clerk = Clerk::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$clerk->id,
                'clerk' => @$clerk->toArray([
                    'locationId',
                    'fullName',
                    'pin',
                    'isManager',
                ])
            ];
        });

        return (new EditClerkScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $clerk = Clerk::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$clerk->id,
            ];
        });

        return (new DeleteClerkScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}