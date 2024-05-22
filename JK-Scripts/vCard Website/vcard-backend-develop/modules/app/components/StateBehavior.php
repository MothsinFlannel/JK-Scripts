<?php


namespace app\modules\app\components;


use app\models\ext\Installation;
use app\models\ext\Job;
use app\models\JobQuery;
use vr\core\ArrayHelper;
use yii\base\Behavior;

/**
 * Class StateBehavior
 * @package app\modules\app\components
 */
class StateBehavior extends Behavior
{
    /**
     * @return string[]
     */
    public function events(): array
    {
        return [
            \yii\base\Controller::EVENT_AFTER_ACTION => 'afterAction'
        ];
    }

    /**
     * @param $event
     * @return bool
     */
    public function afterAction($event): bool
    {
        if (is_array($event->result)) {
            $event->result = ArrayHelper::merge($event->result, [
                '_state' => [
                    'installations' => Installation::find()
                        ->andWhere('[[handledAt]] is null')
                        ->cache(Installation::CACHE_DURATION)
                        ->count(),
                    'jobs' => [
                        'totalCount' => $this->getJobQuery()->count(),
                    ]
                ]
            ]);
        }

        return true;
    }

    /**
     * @return JobQuery
     */
    private function getJobQuery(): JobQuery
    {
        return Job::find()
            ->andWhere([
                'state' => [Job::STATUS_IN_PROGRESS, Job::STATUS_PENDING]
            ])
            ->cache(Job::CACHE_DURATION);
    }
}