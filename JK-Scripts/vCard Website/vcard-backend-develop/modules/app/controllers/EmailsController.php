<?php


namespace app\modules\app\controllers;


use app\models\ext\Location;
use app\models\ext\User;
use app\models\NotificationEmail;
use app\modules\app\components\Controller;
use app\modules\app\scripts\emails\DeleteEmailScript;
use app\modules\app\scripts\emails\EditEmailScript;
use app\modules\app\scripts\emails\EmailsListScript;
use app\modules\app\scripts\emails\GetEmailScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

class EmailsController extends Controller
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
            $email = NotificationEmail::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null
                ],
                'locationId' => @$email->locationId,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new EmailsListScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $email = NotificationEmail::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$email->id,
            ];
        });

        return (new GetEmailScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
                'email' => [
                    'locationId' => $location->id,
                    'email' => $faker->safeEmail,
                ]
            ];
        });

        return (new EditEmailScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $email = NotificationEmail::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$email->id,
                'email' => @$email->toArray([
                    'email',
                ])
            ];
        });

        return (new EditEmailScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $email = NotificationEmail::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$email->id,
            ];
        });

        return (new DeleteEmailScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}