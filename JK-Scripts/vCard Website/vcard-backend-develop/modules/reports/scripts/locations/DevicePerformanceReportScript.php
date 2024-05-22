<?php


namespace app\modules\reports\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\CabinetType;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\Terminal;
use app\modules\app\components\FilterByTrait;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class DevicePerformanceReportScript
 * @package app\modules\reports\scripts\locations
 */
class DevicePerformanceReportScript extends PagedListScript
{
    use FilterByTrait;
    use ExportQueryTrait;

    /**
     * @var string
     */
    public string $since;

    /**
     * @var string
     */
    public string $until;

    /**
     * @var bool
     */
    public bool $export = false;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var ActiveQuery|null
     */
    private ?ActiveQuery $_query = null;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['since', 'until'], 'required'],
            // [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['cabinetTypeId', ExistValidator::class, 'targetClass' => CabinetType::class, 'targetAttribute' => 'id'],
                ],
                'objectify' => true
            ],
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
        $results = ArrayHelper::typecast($this->_query->all(), [
            'cashIn' => 'float, %0.2f',
            'cashOut' => 'float, %0.2f',
            'netCash' => 'float, %0.2f',
            'dailyCashIn' => 'float, %0.2f',
            'dailyNet' => 'float, %0.2f',
        ]);

        if ($this->export) {
            $headers = [
                'name' => 'Location',
                'city',
                'state',
                'zipCode',
                'county',
                'number' => 'Device ID',
                'cabinet',
                'programName' => 'Game Title',
                'cashIn',
                'cashOut',
                'netCash',
                'dailyCashIn' => 'Daily Cash In Per Device',
                'dailyNet' => 'Daily Net Per Device'
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
            ->andFilterWhere([
                'companyId' => @$this->filters->companyId,
                'id' => @$this->filters->locationId,
                'state' => @$this->filters->state,
                'isLive' => true,
            ])
            ->filter(@$this->filters->query);

        if (@$this->filters->active !== null) {
            $locationQuery->active($this->filters->active);
        }

        $this->filterBy($locationQuery, 'city', @$this->filters->city);
        $this->filterBy($locationQuery, 'state', @$this->filters->state);
        $this->filterBy($locationQuery, 'zipCode', @$this->filters->zipCode);
        $this->filterBy($locationQuery, 'county', @$this->filters->county);

        $this->_query = Terminal::find()
            ->select([
                'name' => 'location.name',
                'city' => 'location.city',
                'state' => 'location.state',
                'zipCode' => 'location.zipCode',
                'county' => 'location.county',
                'terminal.number',
                'cabinet' => 'cabinetType.name',
                'terminal.programName',
                'cashIn' => new Expression('coalesce([[cashIn]], 0)'),
                'cashOut' => new Expression('coalesce([[cashOut]], 0)'),
                'netCash' => new Expression('coalesce([[netCash]], 0)'),
                'dailyCashIn' => new Expression('coalesce([[dailyCashIn]], 0)'),
                'dailyNet' => new Expression('coalesce([[dailyNet]], 0)'),
                'isActive' => 'location.isActive'
            ])
            ->leftJoin([
                'convolution' => Convolution::find()
                    ->select([
                        'convolution.terminal',
                        'convolution.locationId',
                        'cashIn' => 'sum([[moneyIn]])',
                        'cashOut' => 'sum([[moneyOut]])',
                        'netCash' => 'sum([[moneyIn]] - [[moneyOut]])',
                        'dailyCashIn' => 'avg([[moneyIn]])',
                        'dailyNet' => 'avg([[moneyIn]] - [[moneyOut]])',
                    ])
                    ->since($this->since)
                    ->until($this->until)
                    ->groupBy(['locationId', 'terminal'])
            ], '[[convolution.locationId]] = [[terminal.locationId]] and [[convolution.terminal]] = [[terminal.number]]')
            ->leftJoin('cabinetType', '[[cabinetType.id]] = [[terminal.cabinetTypeId]]')
            ->rightJoin([
                'location' => $locationQuery
            ], '[[location.id]] = [[terminal.locationId]]')
            ->leftJoin('route', '[[route.id]] = [[location.routeId]]')
            ->andFilterWhere([
                'cabinetTypeId' => @$this->filters->cabinetTypeId,
            ])
            ->andFilterCompare('programName', @$this->filters->programName, 'ilike')
            ->asArray();

        $this->applySortingToQuery($this->_query, 'name+asc');
    }
}