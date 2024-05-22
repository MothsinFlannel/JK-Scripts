<?php

namespace app\modules\app\scripts\tasks;

use app\models\ext\User;
use app\models\Task;
use vr\core\ErrorsException;
use vr\core\Script;

class UpsertTaskScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var array
     */
    public array $task = [];

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
            ['task', 'required'],
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
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->entity = Task::findOne($this->id) ?: new Task([
            'authorId' => User::loggedIn()->id,
        ]);
        $this->entity->attributes = $this->task;

        if (!$this->entity->save() || !$this->entity->refresh()) {
            throw new ErrorsException($this->entity->errors);
        }
    }
}