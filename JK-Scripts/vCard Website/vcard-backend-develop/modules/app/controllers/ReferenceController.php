<?php


namespace app\modules\app\controllers;


use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\reference\DeleteItemScript;
use app\modules\app\scripts\reference\EditItemScript;
use app\modules\app\scripts\reference\GetItemScript;
use app\modules\app\scripts\reference\ItemsListScript;
use Faker\Factory;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\Inflector;
use vr\core\PagedListScript;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class ReferenceController
 * @package app\modules\app\controllers
 */
abstract class ReferenceController extends Controller
{
    /**
     * @var string
     */
    public string $targetClass;

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
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'sort' => null,
            ];
        });

        return (new ItemsListScript(Yii::$app->request->bodyParams + [
                'targetClass' => $this->targetClass
            ]))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws InvalidConfigException
     * @throws VerboseException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $item = call_user_func([Yii::createObject($this->targetClass), 'find'])->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$item->id,
            ];
        });

        return (new GetItemScript(Yii::$app->request->bodyParams + [
                'targetClass' => $this->targetClass
            ]))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     * @throws InvalidConfigException
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'item' => [
                    'name' => Inflector::titleize($faker->word),
                ]
            ];
        });

        return (new EditItemScript(Yii::$app->request->bodyParams + [
                'targetClass' => $this->targetClass
            ]))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     * @throws InvalidConfigException
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $item = call_user_func([Yii::createObject($this->targetClass), 'find'])->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$item->id,
                'item' => $item->toArray(['name']),
            ];
        });

        return (new EditItemScript(Yii::$app->request->bodyParams + [
                'targetClass' => $this->targetClass
            ]))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $item = call_user_func([Yii::createObject($this->targetClass), 'find'])->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$item->id,
            ];
        });

        return (new DeleteItemScript(Yii::$app->request->bodyParams + [
                'targetClass' => $this->targetClass
            ]))->execute()->toArray();
    }
}