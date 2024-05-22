<?php

namespace app\modules\app\scripts\tasks;

use app\models\Task;
use vr\core\Script;

/**
 *
 */
class GetTaskScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var Task|null
     */
    private ?Task $entity = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', 'exist', 'targetClass' => Task::class],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return Task[]|null[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'task' => $this->entity,
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {
        $this->entity = Task::findOne($this->id);
    }
}