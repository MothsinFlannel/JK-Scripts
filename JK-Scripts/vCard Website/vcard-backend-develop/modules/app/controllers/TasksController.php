<?php


namespace app\modules\app\controllers;

use app\models\ext\Board;
use app\models\ext\User;
use app\models\Task;
use app\modules\app\components\Controller;
use app\modules\app\scripts\reference\DeleteItemScript;
use app\modules\app\scripts\tasks\GetTaskScript;
use app\modules\app\scripts\tasks\TasksListScript;
use app\modules\app\scripts\tasks\UpsertTaskScript;
use Faker\Factory;
use vr\api\components\VerboseException;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use Yii;

/**
 * Class TasksController
 * @package app\modules\app\controllers
 */
class TasksController extends Controller
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
            $task = Task::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'boardId' => $task->boardId,
            ];
        });

        return (new TasksListScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $task = Task::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$task->id,
            ];
        });

        return (new GetTaskScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $board = Board::find()->random()->one();
            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'task' => [
                    'boardId' => $board->id,
                    'column' => ArrayHelper::getValue($board->columns, 0),
                    'summary' => $faker->sentence,
                    'description' => $faker->realText,
                ]
            ];
        });

        return (new UpsertTaskScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $task = Task::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$task->id,
                'task' => $task->toArray(),
            ];
        });

        return (new UpsertTaskScript(Yii::$app->request->bodyParams))->execute()->toArray();
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
            $task = Task::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$task->id,
            ];
        });

        return (new DeleteItemScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}