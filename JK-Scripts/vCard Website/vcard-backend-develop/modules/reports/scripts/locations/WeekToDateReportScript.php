<?php


namespace app\modules\reports\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\ext\Convolution;
use app\models\ext\Invoice;
use app\models\ext\Location;
use app\models\ext\Payment;
use app\models\LocationQuery;
use app\modules\app\components\FilterByTrait;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;
use yii\db\Expression;

// TODO: refactor

/**
 * Class WeekToDateReportScript
 * @package app\modules\reports\scripts\locations
 */
class WeekToDateReportScript extends PagedListScript
{
    use FilterByTrait;
    use ExportQueryTrait;

    /**
     * @var string|null
     */
    public ?string $since = null;

    /**
     * @var string|null
     */
    public ?string $until = null;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var bool
     */
    public bool $export = false;
    /**
     * @var LocationQuery
     */
    protected LocationQuery $_query;

    /**
     * @return array
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
                    ['query', 'trim'],
                    ['offline', 'boolean'],
                    ['active', 'boolean'],
                    // [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
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
     * @throws ErrorsException
     * @throws Throwable
     * @throws Throwable
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $locations = $this->_query->all();

        $convolutions = $this->getConvolutions();
        $payments = $this->getPayments();

        $results = ArrayHelper::getColumn($locations, function (Location $location) use ($convolutions, $payments) {
            $stats = ArrayHelper::getValue($convolutions, $location->id);
            $paid = ArrayHelper::getValue($payments, [$location->id, 'paid']);
            $due = ArrayHelper::getValue($payments, [$location->id, 'due']);

            return [
                'name' => $location->name,
                'address' => $location->address,
                'city' => $location->city,
                'state' => $location->state,
                'zipCode' => $location->zipCode,
                'county' => $location->county,
                'totalIn' => round(ArrayHelper::getValue($stats, 'totalIn'), 2),
                'totalOut' => round(ArrayHelper::getValue($stats, 'totalOut'), 2),
                'revenue' => round(ArrayHelper::getValue($stats, 'revenue'), 2),
                'profit' => round(ArrayHelper::getValue($stats, 'profit'), 2),
                'paid' => round($paid, 2),
                'due' => round($due, 2),
                'lastActivityAt' => $location->lastActivityAt,
                'isActive' => $location->isActive,
            ];
        });

        if ($this->export) {
            $headers = ['name', 'address', 'city', 'state', 'zipCode', 'county', 'totalIn', 'totalOut', 'revenue', 'profit', 'paid', 'due', 'lastActivityAt'];
            $this->arrayToCsv($results, $headers, true);
        }

        return [
            'count' => (int)$this->_query->count(),
            'results' => $results,

            'totals' => [
                'moneyIn' => round(ArrayHelper::sum($results, 'totalIn'), 2),
                'moneyOut' => round(ArrayHelper::sum($results, 'totalOut'), 2),
                'revenue' => round(ArrayHelper::sum($results, 'revenue'), 2),
                'profit' => round(ArrayHelper::sum($results, 'profit'), 2),
                'paid' => round(ArrayHelper::sum($results, 'paid'), 2),
                'due' => round(ArrayHelper::sum($results, 'due'), 2),
            ]
        ];
    }

    /**
     * @return array
     * @throws Throwable
     */
    private function getConvolutions(): array
    {
        $convolutions = Convolution::find()
            ->select([
                'convolution.locationId',
                'totalIn' => 'sum([[moneyIn]])',
                'totalOut' => 'sum([[moneyOut]])',
                'revenue' => 'sum([[moneyIn]] - [[moneyOut]])',
                'profit' => 'sum([[convolution.percentageProfit]] + [[convolution.flatFee]])',
            ])
            ->rightJoin([
                'location' => Location::find()
                    ->andFilterWhere([
                        'companyId' => @$this->filters->companyId,
                        'isLive' => true
                    ])
            ], 'location.id = [[convolution.locationId]]')
            ->since($this->since)
            ->until($this->until)
            ->groupBy('convolution.locationId')
            ->asArray()
            ->all();

        return ArrayHelper::index($convolutions, 'locationId');
    }

    /**
     * @return array
     * @throws Throwable
     */
    private function getPayments(): array
    {
        $paid = Payment::find()
            ->select([
                'locationId',
                'paid' => new Expression('sum(payment.amount)'),
                'due' => new Expression('sum(invoice.due)'),
            ])
            ->rightJoin([
                'invoice' => Invoice::find()
                    ->select([
                        'invoice.id',
                        'locationId',
                        'due' => 'amount'
                    ])
                    ->andFilterWhere([
                        'since' => $this->since,
                        'until' => $this->until
                    ])
                    ->rightJoin([
                        'location' => Location::find()
                            ->andFilterWhere([
                                'companyId' => @$this->filters->companyId,
                                'isLive' => true
                            ])
                    ], 'location.id = [[invoice.locationId]]')
            ], '[[payment.invoiceId]] = invoice.id')
            ->groupBy('locationId')
            ->asArray()
            ->all();

        return ArrayHelper::index($paid, 'locationId');
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_query = Location::find()
            ->select([
                'id',
                'name',
                'address',
                'city',
                'state',
                'zipCode',
                'county',
                'lastActivityAt' => new Expression("timezone(timezone, [[location.lastActivityAt]])"),
                'isActive',
            ])
            ->andFilterWhere([
                'companyId' => @$this->filters->companyId,
                'location.id' => @$this->filters->locationId,
                'state' => @$this->filters->state,
                'isLive' => true
            ])
            ->filter(@$this->filters->query)
            ->offline(@$this->filters->offline);

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