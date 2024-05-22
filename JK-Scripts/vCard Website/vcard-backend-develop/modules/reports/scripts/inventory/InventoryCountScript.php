<?php


namespace app\modules\reports\scripts\inventory;


use app\components\ExportQueryTrait;
use app\models\ext\Location;
use app\models\ext\Terminal;
use app\modules\app\components\FilterByTrait;
use Throwable;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;
use yii\db\ActiveQuery;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class MachinesInventoryScript
 * @package app\modules\reports\scripts\inventory
 */
class InventoryCountScript extends PagedListScript
{
    use FilterByTrait;
    use ExportQueryTrait;

    /**
     * @var array | object
     */
    public array|object $filters;

    /**
     * @var bool
     */
    public bool $export = false;

    /**
     * @var ActiveQuery
     */
    private ActiveQuery $_query;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            ['filters', NestedValidator::class, 'rules' => [], 'objectify' => true]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws RangeNotSatisfiableHttpException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $results = $this->_query->all();

        if ($this->export) {
            $headers = ['tradeName', 'effectiveDate', 'address', 'city', 'state', 'zipCode', 'county', 'machinesCount'];
            $this->arrayToCsv($results, $headers, true);
        }

        return [
            'count' => (int)$this->_query->count(),
            'results' => $results,
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_query = Location::find()
            ->select([
                'tradeName' => 'location.name',
                'effectiveDate' => '[[location.createdAt]]::date',
                'address' => 'location.address',
                'city' => 'location.city',
                'state' => 'location.state',
                'zipCode' => 'location.zipCode',
                'county' => 'location.county',
                'machinesCount' => 'coalesce(terminals.count, 0)',
                'isActive' => 'location.isActive',
            ])
            ->andFilterWhere([
                'location.isLive' => true,
                'location.companyId' => @$this->filters->companyId,
                'state' => @$this->filters->state
            ])
            ->filter(@$this->filters->query)
            ->asArray();

        $terminalQuery = Terminal::find()
            ->joinWith('location')
            ->select([
                'locationId',
                'count' => 'count(*)'
            ])
            ->andFilterWhere([
                'machineTypeId' => @$this->filters->machineTypeId,
                'location.companyId' => @$this->filters->companyId,
            ])
            ->groupBy('locationId');

        if (@$this->filters->machineTypeId) {
            $this->_query->rightJoin([
                'terminals' => $terminalQuery
            ], '[[terminals.locationId]] = location.id');
        } else {
            $this->_query->leftJoin([
                'terminals' => $terminalQuery
            ], '[[terminals.locationId]] = location.id');
        }

        /** @noinspection DuplicatedCode */
        if (@$this->filters->active !== null) {
            $this->_query->active($this->filters->active);
        }

        $this->filterBy($this->_query, 'address', @$this->filters->address);
        $this->filterBy($this->_query, 'city', @$this->filters->city);
        $this->filterBy($this->_query, 'zipCode', @$this->filters->zipCode);
        $this->filterBy($this->_query, 'state', @$this->filters->state);
        $this->filterBy($this->_query, 'county', @$this->filters->county);

        $this->applySortingToQuery($this->_query, 'tradeName+asc');
    }
}