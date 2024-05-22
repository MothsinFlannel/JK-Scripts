<?php


namespace app\modules\app\scripts\clerks;


use app\models\Clerk;
use app\models\ClerkQuery;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 * Class ClerksListScript
 * @package app\modules\app\clerks
 */
class ClerksListScript extends PagedListScript
{
    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @var ClerkQuery
     */
    private ClerkQuery $_query;

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
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Clerk $clerk) {
                return $clerk->toArray([], ['cabinetType']);
            })
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_query = Clerk::find()
            ->joinWith('location')
            ->andWhere(['locationId' => $this->locationId])
            ->andFilterWhere(['like', 'fullName', @$this->filters->query])
            ->orderBy('fullName')
            ->offset($this->offset)->limit($this->limit);
    }
}