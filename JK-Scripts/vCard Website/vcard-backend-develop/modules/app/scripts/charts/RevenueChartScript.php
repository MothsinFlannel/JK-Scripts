<?php


namespace app\modules\app\scripts\charts;


use app\models\ext\Convolution;
use app\models\ext\Location;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use yii\db\ActiveQuery;

/**
 * Class RevenueChartScript
 * @package app\modules\app\scripts\charts
 */
class RevenueChartScript extends Script
{
    /**
     * @var array | object
     */
    public array|object $filters = [];

    /**
     * @var string|null
     */
    public ?string $since;

    /**
     * @var string|null
     */
    public ?string $until;

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
            [['since', 'until'], 'required'],
            // [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
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
     * @throws Exception
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $queried = ArrayHelper::typecast($this->_query->all(), [
            'y' => 'float,%f'
        ]);

        $queried = ArrayHelper::merge($this->createPeriod(), ArrayHelper::map($queried, 'x', 'y'));

        return [
            'results' => ArrayHelper::group($queried, 'x', 'y'),
            'operatorProfit' => $this->getOperatorProfit()
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function createPeriod(): array
    {
        $day = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod(new DateTime($this->since),
            $day,
            (new DateTime($this->until))->add($day)
        );

        /** @noinspection PhpParamsInspection */
        $period = ArrayHelper::getColumn($period, function (DateTime $dateTime) {
            return [
                'x' => $dateTime->format('Y-m-d'),
                'y' => 0.0
            ];
        });
        return ArrayHelper::map($period, 'x', 'y');
    }

    /**
     * @return float
     */
    private function getOperatorProfit(): float
    {
        $profit = Convolution::find()
            ->since($this->since)
            ->until($this->until)
            ->andFilterWhere(['locationId' => @$this->filters->locationId])
            ->sum('[[percentageProfit]] + [[flatFee]]');

        return round($profit, 2);
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_query = Convolution::find()
            ->select([
                'x' => 'date',
                'y' => 'sum([[moneyIn]]) - sum([[moneyOut]])'
            ])
            ->rightJoin([
                'location' => Location::find()
                    ->andFilterWhere([
                        'location.id' => @$this->filters->locationId,
                        'location.companyId' => @$this->filters->companyId
                    ])
            ], '[[location.id]] = [[convolution.locationId]]')
            ->since($this->since)->until($this->until)
            ->groupBy('date')
            ->orderBy('date')
            ->asArray();
    }
}