<?php

namespace app\modules\app\scripts\boards;

use app\models\Board;
use Throwable;
use vr\core\Script;
use yii\db\StaleObjectException;

/**
 *
 */
class DeleteBoardScript extends Script
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
            ['id', 'exist', 'targetClass' => Board::class],
        ];
    }

    /**
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Board::findOne($this->id)->delete();
    }
}