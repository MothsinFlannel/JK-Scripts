<?php


namespace app\modules\app\scripts\warehouses;


use app\models\ext\Location;
use app\models\ext\Warehouse;
use app\models\WarehouseQuery;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 * Class WarehousesListScript
 * @package app\modules\app\warehouses
 */
class WarehousesListScript extends PagedListScript
{
    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var WarehouseQuery
     */
    protected WarehouseQuery $_query;

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
                    ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
                    ['offline', 'boolean'],
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
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Warehouse $warehouse) {
                return $warehouse->toArray([], ['terminalsCount']);
            })
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $locationQuery = Location::find()
            ->andFilterWhere([
                'location.id' => @$this->filters->locationId
            ]);

        if (@$this->filters->activeOnly) {
            $locationQuery->active();
        }

        $this->_query = Warehouse::find()
            ->filter(@$this->filters->query)
            ->offset($this->offset)->limit($this->limit);

        $this->applySortingToQuery($this->_query, 'name+asc');
    }
}