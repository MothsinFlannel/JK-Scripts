<?php


namespace app\modules\reports\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\ext\Route;
use app\models\Terminal;
use app\modules\app\components\FilterByTrait;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;
use yii\db\ActiveQuery;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class AllLocationsStandardReport
 * @package app\modules\reports\scripts\locations
 */
class AllLocationsStandardReport extends PagedListScript
{
    use ExportQueryTrait;
    use FilterByTrait;

    /**
     * @var string
     */
    public string $since;

    /**
     * @var string
     */
    public string $until;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var bool
     */
    public bool $export = false;

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
        $results = ArrayHelper::typecast($this->_query->all(), [
            'cashIn' => 'float, %0.2f',
            'cashOut' => 'float, %0.2f',
            'netCash' => 'float, %0.2f',
            'devicesCount' => 'int',
            'dailyCashIn' => 'float, %0.2f',
            'dailyNet' => 'float, %0.2f',
            'splitPercent' => 'float, %0.2f',
        ]);

        if ($this->export) {
            $headers = [
                'name' => 'Location',
                'address',
                'city',
                'state',
                'zipCode',
                'county',
                'cashIn',
                'cashOut',
                'netCash',
                'devicesCount' => '# Devices',
                'dailyCashIn' => 'Daily Cash In Per Device',
                'dailyNet' => 'Daily Net Per Device',
                'splitPercent' => 'Split'
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
        $this->_query = Location::find()
            ->select([
                'name' => 'location.name',
                'address' => 'location.address',
                'city' => 'location.city',
                'state' => 'location.state',
                'zipCode' => 'location.zipCode',
                'county' => 'location.county',
                'cashIn' => 'coalesce([[cashIn]], 0)',
                'cashOut' => 'coalesce([[cashOut]], 0)',
                'netCash' => 'coalesce([[netCash]], 0)',
                'devicesCount' => 'coalesce(terminals.count, 0)',
                'dailyCashIn' => 'coalesce([[dailyCashIn]] / terminals.count, 0)',
                'dailyNet' => 'coalesce([[dailyNet]] / terminals.count, 0)',
                'splitPercent' => '[[location.splitPercent]]',
                'isActive' => 'location.isActive'
            ])
            ->leftJoin([
                'terminals' => Terminal::find()
                    ->select([
                        'count' => 'count(*)',
                        'locationId',
                    ])
                    ->groupBy('locationId')
            ], '[[terminals.locationId]] = [[location.id]]')
            ->leftJoin([
                'route' => Route::find()
            ], '[[route.id]] = [[location.routeId]]')
            ->leftJoin([
                'convolution' => Convolution::find()
                    ->select([
                        'convolution.locationId',
                        'cashIn' => 'sum([[moneyIn]])',
                        'cashOut' => 'sum([[moneyOut]])',
                        'netCash' => 'sum([[moneyIn]] - [[moneyOut]])',
                        'dailyCashIn' => 'avg([[moneyIn]])',
                        'dailyNet' => 'avg([[moneyIn]] - [[moneyOut]])',
                    ])
                    ->since($this->since)
                    ->until($this->until)
                    ->groupBy('locationId')
            ], '[[convolution.locationId]] = location.id')
            ->andFilterWhere([
                'companyId' => @$this->filters->companyId,
                'location.id' => @$this->filters->locationId,
                'state' => @$this->filters->state,
                'isLive' => true,
            ])
            ->filter(@$this->filters->query)
            ->asArray();

        /** @noinspection DuplicatedCode */
        if (@$this->filters->active !== null) {
            $this->_query->active($this->filters->active);
        }

        $this->filterBy($this->_query, 'address', @$this->filters->address);
        $this->filterBy($this->_query, 'city', @$this->filters->city);
        $this->filterBy($this->_query, 'zipCode', @$this->filters->zipCode);
        $this->filterBy($this->_query, 'state', @$this->filters->state);
        $this->filterBy($this->_query, 'county', @$this->filters->county);

        $this->applySortingToQuery($this->_query, 'name+asc');
    }
}