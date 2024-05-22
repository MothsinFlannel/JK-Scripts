<?php


namespace app\modules\app\controllers;

use app\models\ext\Location;
use app\models\ext\User;
use app\models\Software;
use app\modules\app\components\Controller;
use app\modules\app\scripts\software\DeleteSoftwareScript;
use app\modules\app\scripts\software\EditSoftwareScript;
use app\modules\app\scripts\software\GetSoftwareScript;
use app\modules\app\scripts\software\SoftwareListScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;
use yii\helpers\Inflector;

/**
 * Class SoftwareController
 * @package app\modules\app\controllers
 */
class SoftwareController extends Controller
{
    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionList(): array
    {
        $this->checkInputParams(function () {
            $location = Location::find()->random()->one();
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'locationId' => @$location->id,

                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'sort' => null,
            ];
        });

        return (new SoftwareListScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $software = Software::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$software->id,
            ];
        });

        return (new GetSoftwareScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $location = Location::find()->random()->one();
            $user = User::find()->active()->one();

            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'software' => [
                    'locationId' => @$location->id,
                    'name' => Inflector::titleize($faker->word),
                    'serverNo' => strval($faker->randomNumber(2)),
                    'maxMachineCount' => strval($faker->randomNumber(2)),
                    'isMobileOnly' => $faker->boolean,
                    'split' => 10,
                    'installDate' => $faker->date(),
                    'notes' => $faker->realText,
                    'isFrozen' => false,
                ]
            ];
        });

        return (new EditSoftwareScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $software = Software::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$software->id,
                'software' => @$software->toArray()
            ];
        });

        return (new EditSoftwareScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $software = Software::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$software->id,
            ];
        });

        return (new DeleteSoftwareScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}