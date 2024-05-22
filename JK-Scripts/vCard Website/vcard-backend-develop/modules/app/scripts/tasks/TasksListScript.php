<?php

namespace app\modules\app\scripts\tasks;

use app\models\ext\Board;
use app\models\Task;
use app\models\TaskQuery;
use vr\core\PagedListScript;

/**
 *
 */
class TasksListScript extends PagedListScript
{
    /**
     * @var int|null
     */
    public ?int $boardId = null;
    /**
     * @var TaskQuery
     */
    private TaskQuery $query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['boardId', 'required'],
            ['boardId', 'exist', 'targetClass' => Board::class, 'targetAttribute' => 'id'],
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
            'results' => $this->query->all(),
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {
        $this->query = Task::find()
            ->andWhere([
                'boardId' => $this->boardId
            ])
            ->orderBy('[[summary]] asc');
    }
}