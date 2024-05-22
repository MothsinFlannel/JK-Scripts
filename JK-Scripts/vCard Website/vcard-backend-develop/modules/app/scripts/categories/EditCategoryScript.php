<?php


namespace app\modules\app\scripts\categories;


use app\components\Script;
use app\models\Category;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 * Class EditCategoryScript
 * @package app\modules\app\scripts\categories
 */
class EditCategoryScript extends Script
{
    /**
     * @var array
     */
    public array $category = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

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
            ['category', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Category::class],
            [
                'category',
                NestedValidator::class,
                'rules' => [
                    ['name', 'required'],
                ]
            ]
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
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Category::findOne($this->id) ?: new Category();
        $this->_entity->attributes = $this->category;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}