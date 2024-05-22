<?php


namespace app\modules\reports\scripts\inventory;


use app\components\ExportQueryTrait;
use app\models\CabinetType;
use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\MachineType;
use app\modules\app\components\FilterByTrait;
use Throwable;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class InventoryDetailsScript
 * @package app\modules\reports\scripts\inventory
 */
class InventoryDetailsScript extends PagedListScript
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
            $headers = [
                'locationName' => 'Location',
                'city',
                'state',
                'zipCode',
                'county',
                'programName',
                'machineId',
                'cabinetAssetNumber',
                'boardAssetNumber',
                'terminalId',
                'licenseNumber',
                'machineType',
                'cabinetType',
                'status',
                'cost',
                'acquisitionDate'
            ];
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
        $locationQuery = Location::find()
            ->select([
                'id',
                'name',
                'city',
                'state',
                'zipCode',
                'county',
                'isActive',
            ])
            ->filter(@$this->filters->query)
            ->andFilterWhere([
                'location.id' => @$this->filters->locationId,
                'location.companyId' => @$this->filters->companyId,
                'location.isLive' => true,
            ]);

        if (@$this->filters->active !== null) {
            $locationQuery->active($this->filters->active);
        }

        $this->_query = Terminal::find()
            ->select([
                'locationName' => 'location.name',
                'city' => 'location.city',
                'state' => 'location.state',
                'zipCode' => 'location.zipCode',
                'county' => 'location.county',
                'programName' => "coalesce([[terminal.programName]], '')",
                'machineId' => 'terminal.number',
                'cabinetAssetNumber' => "coalesce([[terminal.cabinetAssetNumber]], '')",
                'boardAssetNumber' => "coalesce([[terminal.boardAssetNumber]], '')",
                'terminalId' => 'terminal.id',
                'licenseNumber' => 'terminal.licenseNumber',
                'machineType' => "coalesce([[machineType.name]], '')",
                'cabinetType' => "coalesce([[cabinetType.name]], '')",
                'status' => new Expression(':active::text', [':active' => 'Active']),
                'cost' => new Expression('0'),
                'acquisitionDate' => new Expression('null'),
                'isActive' => 'location.isActive',
            ])
            ->leftJoin([
                'cabinetType' => CabinetType::find()
            ], '[[cabinetType.id]] = [[terminal.cabinetTypeId]]')
            ->leftJoin([
                'machineType' => MachineType::find()
            ], '[[machineType.id]] = [[terminal.machineTypeId]]')
            ->rightJoin([
                'location' => $locationQuery
            ], '[[terminal.locationId]] = location.id')
            ->andFilterWhere([
                'machineTypeId' => @$this->filters->machineTypeId,
                'cabinetTypeId' => @$this->filters->cabinetTypeId
            ])
            ->asArray();

        $this->filterBy($this->_query, 'licenseNumber', @$this->filters->licenseNumber);
        $this->filterBy($this->_query, 'boardAssetNumber', @$this->filters->boardAssetNumber);
        $this->filterBy($this->_query, 'cabinetAssetNumber', @$this->filters->cabinetAssetNumber);

        $this->filterBy($this->_query, 'zipCode', @$this->filters->zipCode);
        $this->filterBy($this->_query, 'county', @$this->filters->county);
        $this->filterBy($this->_query, 'city', @$this->filters->city);
        $this->filterBy($this->_query, 'state', @$this->filters->state);
        $this->filterBy($this->_query, 'programName', @$this->filters->programName);

        $this->applySortingToQuery($this->_query, 'locationName+asc');
    }
}