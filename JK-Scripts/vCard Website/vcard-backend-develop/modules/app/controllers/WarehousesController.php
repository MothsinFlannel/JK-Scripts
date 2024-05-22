<?php


namespace app\modules\app\controllers;


use app\models\ext\User;
use app\models\ext\Warehouse;
use app\modules\app\components\Controller;
use app\modules\app\scripts\warehouses\DeleteWarehouseScript;
use app\modules\app\scripts\warehouses\EditWarehouseScript;
use app\modules\app\scripts\warehouses\GetWarehouseScript;
use app\modules\app\scripts\warehouses\WarehousesListScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\Inflector;
use vr\core\PagedListScript;
use Yii;

/**
 * Class WarehousesController
 * @package app\modules\app\controllers
 */
class WarehousesController extends Controller
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

        return (new WarehousesListScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $warehouse = Warehouse::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$warehouse->id,
            ];
        });

        return (new GetWarehouseScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'warehouse' => [
                    'name' => Inflector::titleize($faker->sentence),
                ]
            ];
        });

        return (new EditWarehouseScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $warehouse = Warehouse::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$warehouse->id,
                'warehouse' => @$warehouse->toArray()
            ];
        });

        return (new EditWarehouseScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $warehouse = Warehouse::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$warehouse->id,
            ];
        });

        return (new DeleteWarehouseScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}