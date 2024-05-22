<?php


namespace app\modules\app\controllers;

use app\models\Board;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\boards\BoardsListScript;
use app\modules\app\scripts\boards\DeleteBoardScript;
use app\modules\app\scripts\boards\GetBoardScript;
use app\modules\app\scripts\boards\UpsertBoardScript;
use Faker\Factory;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\Inflector;
use vr\core\PagedListScript;
use Yii;

/**
 * Class BoardsController
 * @package app\modules\app\controllers
 */
class BoardsController extends Controller
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
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'sort' => null,
            ];
        });

        return (new BoardsListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $board = Board::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$board->id,
            ];
        });

        return (new GetBoardScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'board' => [
                    'title' => Inflector::titleize($faker->word),
                    'columns' => UpsertBoardScript::DEFAULT_COLUMNS,
                    'isActive' => true,
                ]
            ];
        });

        return (new UpsertBoardScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $board = Board::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$board->id,
                'board' => $board->toArray(),
            ];
        });

        return (new UpsertBoardScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $board = Board::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$board->id,
            ];
        });

        return (new DeleteBoardScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}