<?php

namespace app\modules\app\controllers;

use app\models\ext\Job;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\jobs\CancelJobScript;
use app\modules\app\scripts\jobs\JobsListScript;
use app\modules\app\scripts\jobs\RecentJobsScript;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 *
 */
class JobsController extends Controller
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
                    'category' => null,
                    'state' => null,
                ],
                'sort' => 'created-at+desc',
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT
            ];
        });

        return (new JobsListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionRecent(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'category' => null,
            ];
        });

        return (new RecentJobsScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionCancel(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $job = Job::find()
                ->andWhere([
                    'state' => [Job::STATUS_PENDING, Job::STATUS_IN_PROGRESS]
                ])->random()->one();

            return [
                'accessToken' => $user?->accessToken,
                'id' => $job?->id
            ];
        });

        return (new CancelJobScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}