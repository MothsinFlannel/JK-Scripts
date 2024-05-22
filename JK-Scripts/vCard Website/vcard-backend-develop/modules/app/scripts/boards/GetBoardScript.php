<?php

namespace app\modules\app\scripts\boards;

use app\models\Board;
use vr\core\Script;

/**
 *
 */
class GetBoardScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var Board|null
     */
    private ?Board $entity = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', 'exist', 'targetClass' => Board::class],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return Board[]|null[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'board' => $this->entity,
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {
        $this->entity = Board::findOne($this->id);
    }
}