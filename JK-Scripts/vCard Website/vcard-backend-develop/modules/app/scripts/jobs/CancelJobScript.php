<?php

namespace app\modules\app\scripts\jobs;

use app\components\Script;
use app\models\ext\Job;
use app\models\JobQuery;
use vr\core\ErrorsException;

/**
 *
 */
class CancelJobScript extends Script
{
    public ?int $id = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            [
                'id',
                'exist',
                'targetClass' => Job::class,
                'filter' => function (JobQuery $query) {
                    $query->andWhere([
                        'state' => [Job::STATUS_PENDING, Job::STATUS_IN_PROGRESS]
                    ]);
                }
            ]
        ];
    }

    /**
     * @return void
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $job = Job::findOne($this->id);
        $job->state = Job::STATE_CANCELED;

        if (!$job->save()) {
            throw new ErrorsException($job->errors);
        }
    }
}