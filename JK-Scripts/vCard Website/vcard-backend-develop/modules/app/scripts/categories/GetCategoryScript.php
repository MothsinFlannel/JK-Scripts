<?php


namespace app\modules\app\scripts\categories;


use app\models\Category;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetCategoryScript
 * @package app\modules\app\scripts\categories
 */
class GetCategoryScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var Category|null
     */
    private ?Category $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Category::class]
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
            'category' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Category::findOne($this->id);
    }
}