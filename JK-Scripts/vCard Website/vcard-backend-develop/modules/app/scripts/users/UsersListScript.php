<?php


namespace app\modules\app\scripts\users;


use app\models\ext\User;
use app\models\UserQuery;
use vr\core\ArrayHelper;
use vr\core\Inflector;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 * Class UsersListScript
 * @package app\modules\app\users
 */
class UsersListScript extends PagedListScript
{
    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var UserQuery
     */
    private UserQuery $_query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['query', 'trim'],
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
            'results' => ArrayHelper::getColumn($this->_query->all(), function (User $user) {
                return $user->toArray([], ['locationsCount']);
            })
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_query = User::find()
            ->filter(@$this->filters->query)
            ->offset($this->offset)->limit($this->limit);

        if ($this->sort) {
            $sort = lcfirst(urldecode(Inflector::id2camel($this->sort)));
            $this->_query->addOrderBy("$sort");
        }
    }
}