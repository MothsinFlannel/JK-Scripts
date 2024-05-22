<?php

namespace app\modules\app\components;

use app\models\ext\Job;
use app\models\JobQuery;

/**
 *
 */
trait RecentJobsTrait
{
    /**
     * @return JobQuery
     */
    protected function getRecentJobQuery(): JobQuery
    {
        return Job::find()
            ->andWhere([
                'or',
                [
                    'state' => [Job::STATUS_IN_PROGRESS, Job::STATUS_PENDING],
                    'category' => 'app/invoices/export'
                ],
                'now() < [[endedAt]] + :interval'
            ], [
                ':interval' => '5 minutes'
            ])
            ->cache(Job::CACHE_DURATION)
            ->orderBy('[[endedAt]] desc, [[createdAt]] desc')
            ->limit(1);
    }
}