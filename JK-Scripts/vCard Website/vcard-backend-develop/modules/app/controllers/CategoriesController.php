<?php


namespace app\modules\app\controllers;


use app\models\Category;
use app\models\ext\Location;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\categories\CategoriesListScript;
use app\modules\app\scripts\categories\DeleteCategoryScript;
use app\modules\app\scripts\categories\EditCategoryScript;
use app\modules\app\scripts\categories\ExportCategoriesScript;
use app\modules\app\scripts\categories\GetCategoryScript;
use Faker\Factory;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\Inflector;
use vr\core\PagedListScript;
use Yii;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class CategoriesController
 * @package app\modules\app\controllers
 */
class CategoriesController extends Controller
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
            $category = Category::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null
                ],
                'locationId' => @$category->locationId,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new CategoriesListScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $category = Category::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$category->id,
            ];
        });

        return (new GetCategoryScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
                'category' => [
                    'locationId' => $location->id,
                    'name' => Inflector::titleize($faker->word),
                ]
            ];
        });

        return (new EditCategoryScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $category = Category::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$category->id,
                'category' => @$category->toArray([
                    'name',
                ])
            ];
        });

        return (new EditCategoryScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $category = Category::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$category->id,
            ];
        });

        return (new DeleteCategoryScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionExport(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $category = Category::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null
                ],
                'locationId' => @$category->locationId
            ];
        });

        return (new ExportCategoriesScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}