<?php


namespace app\modules\app\scripts\reference;


use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Class ItemsListScript
 * @package app\modules\app\scripts\reference
 */
class ItemsListScript extends PagedListScript
{
    /**
     * @var string
     */
    public string $targetClass;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var ActiveQuery
     */
    protected ActiveQuery $_query;

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            ['targetClass', 'required'],
            ['filters', NestedValidator::class, 'rules' => [], 'objectify' => true]
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
            'count' => $this->_query->count(),
            'results' => $this->_query->all()
        ];
    }

    /**
     *
     * @throws InvalidConfigException
     */
    protected function onExecute(): void
    {
        $this->_query = call_user_func([Yii::createObject($this->targetClass), 'find']);
        $this->_query
            ->offset($this->offset)->limit($this->limit);

        $this->applySortingToQuery($this->_query, 'name+asc');
    }
}