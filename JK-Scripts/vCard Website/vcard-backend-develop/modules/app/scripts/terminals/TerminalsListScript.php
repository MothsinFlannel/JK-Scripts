<?php


namespace app\modules\app\scripts\terminals;


use app\models\CabinetType;
use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\MachineType;
use app\models\TerminalQuery;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\db\Expression;

/**
 * Class TerminalsListScript
 * @package app\modules\app\terminals
 */
class TerminalsListScript extends PagedListScript
{
    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var TerminalQuery
     */
    protected TerminalQuery $_query;

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
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Terminal $terminal) {
                return $terminal->toArray([
                    '*',
                    'location.id',
                    'location.name',
                    'location.address',
                    'location.city',
                    'location.state',
                    'location.zipCode',
                    'location.county',
                ], ['cabinetType', 'machineType', 'location', 'padlock', 'doorLock']);
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
            ->select([
                'timezone',
                'locationId' => 'location.id',
                'location.name',
                'location.address',
                'location.city',
                'location.zipCode',
                'location.state',
                'location.county',
                'location.splitPercent',
                'location.maxOfflineHours',
                'location.isLive',
            ])
            ->andFilterWhere([
                'location.id' => @$this->filters->locationId,
                'location.companyId' => @$this->filters->companyId,
            ]);

        if (!@$this->filters->includeTestOnes) {
            $locationQuery->andFilterWhere([
                'location.isLive' => true,
            ]);
        }

        if (@$this->filters->active !== null) {
            $locationQuery->active($this->filters->active);
        }

        $this->_query = Terminal::find()
            ->select([
                'terminal.id',
                'terminal.locationId',
                'terminal.number',
                'lastActivityAt' => new Expression("timezone(location.timezone, [[terminal.lastActivityAt]])"),
                'cabinetAssetNumber' => "coalesce([[cabinetAssetNumber]], '')",
                'boardAssetNumber' => "coalesce([[boardAssetNumber]], '')",
                'licenseNumber' => "coalesce([[terminal.licenseNumber]], '')",
                'splitPercent' => new Expression('coalesce([[terminal.splitPercent]], [[location.splitPercent]])'),
                'flatFee',
                'groupName',
                'terminal.machineTypeId',
                'terminal.cabinetTypeId',
                'padlockId',
                'doorLockId',
                'machineType' => "coalesce([[machineType.name]], '')",
                'cabinetType' => "coalesce([[cabinetType.name]], '')",
                'programName' => "coalesce([[programName]], '')",
                'referenceNumber',
                'placementDate',
                'refillDate',
                'notes'
            ])
            ->rightJoin([
                'location' => $locationQuery
            ], '[[location.locationId]] = [[terminal.locationId]]')
            ->filter(@$this->filters->query)
            ->offline(@$this->filters->offline)
            ->andFilterCompare('terminal.licenseNumber', @$this->filters->licenseNumber, 'ilike')
            ->andFilterCompare('terminal.programName', @$this->filters->programName, 'ilike')
            ->andFilterCompare('terminal.cabinetAssetNumber', @$this->filters->cabinetAssetNumber, 'ilike')
            ->andFilterCompare('terminal.boardAssetNumber', @$this->filters->boardAssetNumber, 'ilike')
            ->andFilterCompare('terminal.referenceNumber', @$this->filters->referenceNumber, 'ilike')
            ->andFilterWhere([
                'terminal.number' => preg_replace('/[^0-9]/', '', @$this->filters->number),
                'terminal.machineTypeId' => @$this->filters->machineTypeId,
                'terminal.cabinetTypeId' => @$this->filters->cabinetTypeId,
                'terminal.id' => @$this->filters->ids,
            ])
            ->leftJoin([
                'cabinetType' => CabinetType::find()
                    ->select([
                        'cabinetTypeId' => 'id',
                        'name'
                    ])
            ], '[[terminal.cabinetTypeId]] = [[cabinetType.cabinetTypeId]]')
            ->leftJoin([
                'machineType' => MachineType::find()
                    ->select([
                        'machineTypeId' => 'id',
                        'name'
                    ])
            ], '[[terminal.machineTypeId]] = [[machineType.machineTypeId]]')
            ->andWhere([
                'warehouseId' => @$this->filters->warehouseId
            ])
            ->andWhere('terminal.id is not null')
            ->offset($this->offset)->limit($this->limit);

        $this->applySortingToQuery($this->_query);
    }
}