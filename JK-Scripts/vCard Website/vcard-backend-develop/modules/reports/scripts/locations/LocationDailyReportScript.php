<?php


namespace app\modules\reports\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\TerminalQuery;
use vr\core\ArrayHelper;
use vr\core\Script;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class LocationDailyReportScript
 * @package app\modules\reports\scripts\locations
 */
class LocationDailyReportScript extends Script
{
    use ExportQueryTrait;

    /**
     * @var int|null
     */
    public ?int $locationId = null;

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
     * @var TerminalQuery | null
     */
    private ?TerminalQuery $_query;

    /**
     * @var Location|null
     */
    private ?Location $_location;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['locationId', 'since', 'until'], 'required'],
            // [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
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
        $results = $this->typecast();

        if ($this->export) {
            $this->internalExport($results);
        }

        $group = ArrayHelper::group(ArrayHelper::index($results, null, 'date'), 'date');
        $totals = $this->getTotals();

        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($group, function ($item) {
                $items = ArrayHelper::getValue($item, 'items');
                return $item + [
                        'totals' => [
                            'cashIn' => round(ArrayHelper::sum($items, 'cashIn'), 2),
                            'cashOut' => round(ArrayHelper::sum($items, 'cashOut'), 2),
                            'netCash' => round(ArrayHelper::sum($items, 'netCash'), 2),
                            'dailyCashIn' => round(ArrayHelper::sum($items, 'dailyCashIn'), 2),
                            'dailyNet' => round(ArrayHelper::sum($items, 'dailyNet'), 2)
                        ],
                    ];
            }),
            'totals' => $totals,
            'location' => $this->_location->toArray([], ['operatorEmail', 'route']),
        ];
    }

    /**
     * @return array
     */
    private function typecast(): array
    {
        return ArrayHelper::typecast($this->_query->all(), [
            'cashIn' => 'float, %0.2f',
            'cashOut' => 'float, %0.2f',
            'netCash' => 'float, %0.2f',
            'dailyCashIn' => 'float, %0.2f',
            'dailyNet' => 'float, %0.2f',
        ]);
    }

    /**
     * @param array $results
     * @param bool $send
     * @return mixed
     * @throws RangeNotSatisfiableHttpException
     */
    private function internalExport(array $results, bool $send = true): mixed
    {
        $headers = [
            'date',
            'deviceId',
            'gameTitle',
            'cashIn',
            'cashOut',
            'netCash',
            'dailyCashIn' => 'Daily Cash In Per Device',
            'dailyNet' => 'Daily Net Per Device'
        ];

        return $this->arrayToCsv($results, $headers, false, $send);
    }

    /**
     * @return array
     */
    private function getTotals(): array
    {
        return [
            'cashIn' => round($this->_query->sum('[[cashIn]]'), 2),
            'cashOut' => round($this->_query->sum('[[cashOut]]'), 2),
            'netCash' => round($this->_query->sum('[[cashIn]] - [[cashOut]]'), 2),
            'dailyCashIn' => round($this->_query->sum('[[dailyCashIn]]'), 2),
            'dailyNet' => round($this->_query->sum('[[dailyNet]]'), 2),
        ];
    }

    /**
     * @return mixed
     * @throws RangeNotSatisfiableHttpException
     */
    public function export(): mixed
    {
        $results = $this->typecast();
        return $this->internalExport($results, false);
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_location = Location::findOne($this->locationId);

        $this->_query = $this->_location->getTerminals()
            ->select([
                'date',
                'deviceId' => 'terminal.number',
                'gameTitle' => 'terminal.programName',
                'cashIn',
                'cashOut',
                'netCash' => '[[cashIn]] - [[cashOut]]',
                'dailyCashIn',
                'dailyNet'
            ])
            ->rightJoin([
                'convolution' => Convolution::find()
                    ->select([
                        'date',
                        'terminal',
                        'cashIn' => 'sum([[moneyIn]])',
                        'cashOut' => 'sum([[moneyOut]])',
                        'dailyCashIn' => 'avg([[moneyIn]])',
                        'dailyNet' => 'avg([[moneyIn]] - [[moneyOut]])',
                    ])
                    ->andWhere(['locationId' => $this->locationId])
                    ->since($this->since)
                    ->until($this->until)
                    ->groupBy(['date', 'terminal'])
            ], 'convolution.terminal = terminal.number')
            ->andWhere('terminal.id is not null')
            ->orderBy('date, terminal.number')
            ->asArray();
    }
}