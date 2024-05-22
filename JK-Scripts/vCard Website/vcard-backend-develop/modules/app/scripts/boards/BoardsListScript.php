<?php

namespace app\modules\app\scripts\boards;

use app\models\Board;
use app\models\BoardQuery;
use vr\core\PagedListScript;

/**
 *
 */
class BoardsListScript extends PagedListScript
{
    /**
     * @var BoardQuery
     */
    private BoardQuery $query;

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
        $this->query = Board::find()
            ->orderBy('[[title]] asc');
    }
}