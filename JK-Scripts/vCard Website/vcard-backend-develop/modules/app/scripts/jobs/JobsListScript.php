<?php

namespace app\modules\app\scripts\jobs;

use app\models\ext\Job;
use app\models\JobQuery;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 *
 */
class JobsListScript extends PagedListScript
{
    /**
     * @var array|object
     */
    public array|object $filters = [];

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
            ['filters', NestedValidator::class, 'rules' => [], 'objectify' => true],
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
            'count' => $this->query->count(),
            'results' => ArrayHelper::getColumn($this->query->all(), function (Job $job) {
                return $job->toArray([
                    '*',
                    'initiator.fullName',
                    'initiator.email'
                ], ['initiator']);
            }),
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {
        $this->query = Job::find()
            ->andFilterWhere([
                'category' => @$this->filters->category,
                'state' => @$this->filters->state
            ])
            ->offset($this->offset)->limit($this->limit);

        $this->applySortingToQuery($this->query, 'created-at+desc');
    }
}