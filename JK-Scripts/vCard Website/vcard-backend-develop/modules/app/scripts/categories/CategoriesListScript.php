<?php


namespace app\modules\app\scripts\categories;


use app\models\Category;
use app\models\CategoryQuery;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 * Class CategoriesListScript
 * @package app\modules\app\scripts\categories
 */
class CategoriesListScript extends PagedListScript
{
    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var CategoryQuery
     */
    protected CategoryQuery $_query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['locationId', 'required'],
            [
                'filters',
                NestedValidator::class,
                'rules' => [

                ],
                'objectify' => true,
            ]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array|string[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Category $category) {
                return $category->toArray();
            })
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_query = Category::find()
            ->andWhere(['locationId' => $this->locationId])
            ->orderBy('category')
            ->offset($this->offset)->limit($this->limit);
    }
}