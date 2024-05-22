<?php

namespace app\modules\app\scripts\tasks;

use app\models\Task;
use Throwable;
use vr\core\Script;
use yii\db\StaleObjectException;

/**
 *
 */
class DeleteTaskScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

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
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Task::findOne($this->id)->delete();
    }
}