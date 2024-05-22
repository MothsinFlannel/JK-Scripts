<?php


namespace app\modules\app\scripts\dashboard;


use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\LocationQuery;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\Script;
use vr\core\validators\NestedValidator;

/**
 * Class DashboardIndexScript
 * @package app\modules\app\scripts\dashboard
 */
class DashboardIndexScript extends Script
{
    /** @var array|object */
    public array|object $filters = [];

    /**
     * @var LocationQuery
     */
    private LocationQuery $_offlineQuery;

    public function rules(): array
    {
        return [
            [
                'filters',
                NestedValidator::class,
                'rules' => [],
                'objectify' => true
            ]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array|array[]
     * @throws Throwable
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        try {
            return [
                'revenue' => [
                    'lastWeek' => $this->getSeries('last week'),
                    'thisWeek' => $this->getSeries('this week'),
                    'byState' => $this->getRevenueByState(),
                ],
                'offlineLocations' => (int)$this->_offlineQuery->count()
            ];
        } catch (Throwable) {

        }

        return [];
    }

    /**
     * @param $week
     * @return array
     * @throws Throwable
     */
    protected function getSeries($week): array
    {
        $interval = $this->getInterval($week);

        /** @noinspection PhpParamsInspection */
        $dates = ArrayHelper::getColumn($interval, function (DateTime $date) {
            return $date->format('Y-m-d');
        });
        $convolutions = Convolution::find()
            ->select([
                'date',
                'amount' => 'sum([[moneyIn]] - [[moneyOut]])',
            ])
            ->innerJoin([
                'location' => Location::find()
                    ->andFilterWhere([
                        'companyId' => @$this->filters->companyId
                    ])
            ], '[[locationId]] = location.id')
            ->andWhere([
                'date' => $dates
            ])
            ->groupBy('date')
            ->asArray()
            ->all();

        $convolutions = ArrayHelper::map($convolutions, 'date', 'amount');

        /** @noinspection PhpParamsInspection */
        return ArrayHelper::getColumn($interval, function (DateTime $date) use ($convolutions) {
            return round(ArrayHelper::getValue($convolutions, $date->format('Y-m-d')), 2);
        });
    }

    /**
     * @param $whatWeek
     * @return DatePeriod
     * @throws Exception
     */
    protected function getInterval($whatWeek): DatePeriod
    {
        $oneDay = DateInterval::createFromDateString('1 day');

        $start = (new DateTime($whatWeek))->sub($oneDay);
        $end = min(
            (new DateTime($whatWeek))->add(DateInterval::createFromDateString('6 days')),
            (new DateTime())->add($oneDay));

        return new DatePeriod($start, $oneDay, $end);
    }

    /**
     * @return array
     * @throws Throwable
     */
    protected function getRevenueByState(): array
    {
        $interval = $this->getInterval('this week');

        $states = Location::find()
            ->andFilterWhere([
                'companyId' => @$this->filters->companyId
            ])
            ->select('state')
            ->distinct()
            ->orderBy('state')
            ->column() ?: [];

        /** @noinspection PhpParamsInspection */
        $convolutions = Convolution::find()
            ->select([
                'amount' => 'sum([[moneyIn]]) - sum([[moneyOut]])',
                'state'
            ])
            ->innerJoin([
                'location' => Location::find()
                    ->andFilterWhere([
                        'companyId' => @$this->filters->companyId
                    ])
            ], '[[locationId]] = location.id')
            ->andWhere([
                'date' => ArrayHelper::getColumn($interval, function (DateTime $date) {
                    return $date->format('Y-m-d');
                })
            ])
            ->groupBy('location.state')
            ->orderBy('location.state')
            ->asArray()
            ->all();

        $convolutions = ArrayHelper::map($convolutions, 'state', 'amount');

        $revenues = ArrayHelper::getColumn($states, function (string $state) use ($convolutions) {
            return [
                'state' => $state,
                'amount' => ArrayHelper::getValue($convolutions, $state),
            ];
        });

        return ArrayHelper::typecast($revenues, [
            'amount' => 'float,%f'
        ]);
    }

    protected function onExecute(): void
    {
        try {
            $this->_offlineQuery = Location::find()
                ->andFilterWhere([
                    'companyId' => @$this->filters->companyId
                ])
                ->active()
                ->offline()
                ->orderBy(['lastActivityAt' => SORT_DESC]);
        } catch (Throwable) {
        }
    }
}