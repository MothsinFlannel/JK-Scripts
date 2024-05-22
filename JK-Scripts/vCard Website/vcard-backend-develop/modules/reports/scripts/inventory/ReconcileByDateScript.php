<?php


namespace app\modules\reports\scripts\inventory;


use app\components\ExportQueryTrait;
use app\models\ext\Convolution;
use app\models\ext\Invoice;
use app\models\ext\Location;
use app\models\ext\Payment;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\db\Expression;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class ReconcileByDateScript
 * @package app\modules\reports\scripts\inventory
 */
class ReconcileByDateScript extends Script
{
    use ExportQueryTrait;

    /**
     *
     */
    const MONDAY = 'Monday';

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
     * @var array
     */
    private array $_results = [];


    /**
     * @var array
     */
    private array $_totals = [];

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
                    ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id']
                ],
                'objectify' => true
            ]
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
        $results = ArrayHelper::typecast($this->_results, [
            'cashIn' => 'float, %0.2f',
            'cashOut' => 'float, %0.2f',
            'net' => 'float, %0.2f',
            'locationSplit' => 'float, %0.2f',
            'operatorSplit' => 'float, %0.2f',
            'amountDue' => 'float, %0.2f',
            'amountPaid' => 'float, %0.2f',
            'balanceDue' => 'float, %0.2f',
        ]);

        if ($this->export) {
            $headers = [
                'date',
                'name',
                'cashIn',
                'cashOut',
                'net',
                'locationSplit',
                'operatorSplit',
                'amountDue',
                'amountPaid',
                'balanceDue',
                'city',
                'state',
                'zipCode',
                'county'
            ];
            $this->arrayToCsv($results, $headers, true);
        }

        return [
            'count' => count($this->_results),
            'results' => $results,
            'totals' => $this->_totals
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        /** @var array $interval */
        foreach ($this->getIntervals() as $interval) {
            $statistics = $this->getStatistics($interval);

            $paidAmounts = $this->getPaidAmounts($interval);
            foreach ($statistics as $each) {
                $locationId = ArrayHelper::getValue($each, 'locationId');
                $location = Location::findOne($locationId);

                $amountPaid = ArrayHelper::getValue($paidAmounts, $locationId);

                $item = [
                        'date' => (new DateTime(ArrayHelper::getValue($interval, 'since')))->format('m/d/Y')
                    ] + $each + [
                        'amountPaid' => $amountPaid,
                        'balanceDue' => round(ArrayHelper::getValue($each, 'amountDue') - $amountPaid, 2),
                        'city' => $location->city,
                        'state' => $location->state,
                        'zipCode' => $location->zipCode,
                        'county' => $location->county,
                        'isActive' => $location->isActive,
                    ];
                $this->_results[] = $item;
                $this->addToTotals($item);
            }

        }
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getIntervals(): array
    {
        $intervals = [];
        $day = DateInterval::createFromDateString('1 day');

        $period = new DatePeriod(
            new DateTime($this->since),
            $day,
            new DateTime($this->until));


        /** @var DateTime $date */
        foreach ($period as $date) {
            if ($date->format('l') == self::MONDAY) {
                $intervals[] = [
                    'since' => $date->format('Y-m-d'),
                    'until' => $date
                        ->add(DateInterval::createFromDateString('6 days'))
                        ->format('Y-m-d'),
                ];
            }
        }

        return $intervals;
    }

    /**
     * @param $interval
     * @return Convolution[]|array
     * @throws Throwable
     */
    private function getStatistics($interval): array
    {
        return Convolution::find()
            ->select([
                'location.name',
                'cashIn' => 'sum([[moneyIn]])',
                'cashOut' => 'sum([[moneyOut]])',
                'net' => 'sum([[moneyIn]] - [[moneyOut]])',
                'locationSplit' => 'sum([[moneyIn]] - [[moneyOut]]) - sum([[convolution.percentageProfit]] + [[convolution.flatFee]])',
                'operatorSplit' => 'sum([[convolution.percentageProfit]] + [[convolution.flatFee]])',
                'amountDue' => 'sum([[convolution.percentageProfit]] + [[convolution.flatFee]])',
                'locationId'
            ])
            ->rightJoin([
                'location' => Location::find()
                    ->andFilterWhere([
                        'location.id' => @$this->filters->locationId,
                        'location.companyId' => @$this->filters->companyId,
                        'location.isLive' => true
                    ])
            ], 'location.id = [[convolution.locationId]]')
            ->since(ArrayHelper::getValue($interval, 'since'))
            ->until(ArrayHelper::getValue($interval, 'until'))
            ->groupBy([
                'locationId',
                'location.name',
            ])
            ->orderBy([
                'location.name' => SORT_ASC,
            ])
            ->asArray()
            ->all();
    }

    /**
     * @param array $interval
     * @return array
     * @throws Throwable
     */
    private function getPaidAmounts(array $interval): array
    {
        $query = Payment::find()
            ->select([
                'locationId',
                'amount' => new Expression('coalesce(sum(payment.amount), 0)')
            ])
            ->rightJoin([
                'invoice' => Invoice::find()
                    ->andWhere([
                        'since' => ArrayHelper::getValue($interval, 'since'),
                        'until' => ArrayHelper::getValue($interval, 'until'),
                    ])
            ], '[[payment.invoiceId]] = invoice.id')
            ->groupBy('locationId')
            ->asArray();

        return ArrayHelper::map($query->all(), 'locationId', 'amount');
    }

    /**
     * @param array $item
     */
    private function addToTotals(array $item): void
    {
        $fields = ['cashIn', 'cashOut', 'net', 'locationSplit', 'operatorSplit', 'amountDue', 'amountPaid', 'balanceDue'];

        foreach ($fields as $field) {
            $this->_totals[$field] = round(@$this->_totals[$field] + $item[$field], 2);
        }
    }
}