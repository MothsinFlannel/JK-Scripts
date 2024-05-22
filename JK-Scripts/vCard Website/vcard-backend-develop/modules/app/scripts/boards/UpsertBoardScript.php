<?php

namespace app\modules\app\scripts\boards;

use app\models\ext\Board;
use Exception;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\Script;
use yii\db\ArrayExpression;

/**
 *
 */
class UpsertBoardScript extends Script
{
    public const DEFAULT_COLUMNS = ['To do', 'In progress', 'Done'];

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var array
     */
    public array $board = [];

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
            ['board', 'required'],
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
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->entity = Board::findOne($this->id) ?: new Board([
            'columns' => self::DEFAULT_COLUMNS,
        ]);

        $this->entity->attributes = $this->board;
        $this->entity->columns = new ArrayExpression(ArrayHelper::getValue($this->board, 'columns') ?? []);

        // Save with no validation
        if (!$this->entity->save(false) || !$this->entity->refresh()) {
            throw new ErrorsException($this->entity->errors);
        }
    }
}