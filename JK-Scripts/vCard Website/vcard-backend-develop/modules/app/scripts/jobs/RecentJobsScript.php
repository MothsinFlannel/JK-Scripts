<?php

namespace app\modules\app\scripts\jobs;

use app\models\ext\Job;
use app\models\JobQuery;
use app\modules\app\components\RecentJobsTrait;
use vr\core\ArrayHelper;
use vr\core\Script;

/**
 *
 */
class RecentJobsScript extends Script
{
    use RecentJobsTrait;

    /**
     * @var string|null
     */
    public ?string $category = null;

    /**
     * @var JobQuery
     */
    private JobQuery $query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['category', 'required'],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'results' => ArrayHelper::getColumn($this->query->all(), function (Job $job) {
                return $job->toArray([], ['initiator']);
            }),
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {
        $this->query = $this->getRecentJobQuery();
    }
}