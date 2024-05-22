<?php


namespace app\modules\app\scripts\locations;


use app\models\ext\Location;
use app\models\ext\Log;
use app\models\ext\Terminal;
use app\models\Sale;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

const DEFAULT_TIMEZONE = 'America/New_York';

/**
 * Class RevenueReportScript
 * @package app\modules\reports\scripts\locations
 */
class LocationRevenueScript extends PagedListScript
{
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
     * @var ActiveQuery | null
     */
    private ?ActiveQuery $_query;

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
            ['locationId', 'exist', 'targetClass' => Location::class, 'targetAttribute' => 'id'],
            // [['since', 'until'], 'date', 'format' => 'php:Y-m-d H:i'],
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
        $terminals = Terminal::find()
            ->select([
                'terminal.*',
                '[[lastActivityAt]]::timestamp'
            ])
            ->joinWith('cabinetType')
            ->andWhere(['locationId' => $this->locationId])
            ->all();

        $terminals = ArrayHelper::index($terminals, 'number');

        $items = ArrayHelper::getColumn($this->_query->all(), function (array $item) use ($terminals) {
            $terminal = ArrayHelper::getValue($terminals, ArrayHelper::getValue($item, 'deviceId'));

            $sales = Sale::find()
                    ->since($this->since)
                    ->until($this->until)
                    ->andWhere([
                        'locationId' => $this->locationId,
                        'terminal' => ArrayHelper::getValue($terminal, 'number'),
                    ])
                    ->sum('amount') / 100;

            $value = ArrayHelper::getValue($terminal, ['lastActivityAt']);
            if ($value) {
                $value = new DateTime($value);
                $value->setTimezone(new DateTimeZone(DEFAULT_TIMEZONE));
                $value = $value->format('Y-m-d H:i:s');
            }

            return $item + [
                    'sales' => round($sales, 2),
                    'cabinet' => ArrayHelper::getValue($terminal, ['cabinetType', 'name']),
                    'gameName' => ArrayHelper::getValue($terminal, ['programName']),
                    'lastActivityAt' => $value,
                ];
        });

        $results = ArrayHelper::typecast($items, [
            'cashIn' => 'float, %0.2f',
            'cashOut' => 'float, %0.2f',
            'netCash' => 'float, %0.2f',
            'operatorProfit' => 'float, %0.2f',
            'dailyCashIn' => 'float, %0.2f',
            'dailyCashOut' => 'float, %0.2f',
            'dailyNet' => 'float, %0.2f',
            'sales' => 'float, %0.2f',
        ]);


        return [
            'count' => (int)$this->_query->count(),
            'results' => $results,
            'totals' => [
                'cashIn' => round($this->_query->sum('[[cashIn]]') ?? 0, 2),
                'cashOut' => round($this->_query->sum('[[cashOut]]') ?? 0, 2),
                'netCash' => round($this->_query->sum('[[cashIn]] - [[cashOut]]') ?? 0, 2),
                'operatorProfit' => round($this->_query->sum('[[operatorProfit]]') ?? 0, 2),
                'sales' => round(ArrayHelper::sum($items, 'sales') ?? 0, 2),
            ],
            'location' => $this->_location->toArray([], ['operatorEmail', 'route'])
        ];
    }

    /**
     *
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->_location = Location::findOne($this->locationId);

        $this->until = (new DateTime($this->until))
            ->add(DateInterval::createFromDateString('1 min'))
            ->format('Y-m-d H:i');

        $splitPercent = $this->_location->splitPercent / 100.0;

        $logQuery = Log::find()
            ->select([
                'terminal',
                'cashIn' => 'sum([[moneyIn]]) / 100',
                'cashOut' => 'sum([[moneyOut]]) / 100',
                'netCash' => 'sum([[moneyIn]] - [[moneyOut]]) / 100',
                'operatorProfit' => 'sum(:splitPercent* ([[moneyIn]] - [[moneyOut]])) / 100',
                'terminal.programName',
                'date' => '[[createdAt]]::date',
            ])
            ->addParams([
                ':splitPercent' => new Expression("$splitPercent")
            ])
            ->rightJoin([
                'terminal' => Terminal::find()
                    ->select([
                        'number',
                        'programName'
                    ])
                    ->andWhere([
                        'locationId' => $this->locationId,
                    ])
            ], '[[terminal.number]] = [[log.terminal]]')
            ->groupBy(['terminal', 'terminal.programName', '[[createdAt]]::date'])
            ->andWhere([
                'isLive' => $this->_location->isLive,
                'serial' => $this->_location->serial
            ])
            ->andWhere(':since <= [[createdAt]] and [[createdAt]] < :until ', [
                ':since' => $this->since,
                ':until' => $this->until,
            ]);

        $this->_query = ActiveRecord::find()
            ->select([
                'deviceId' => 'terminal',
                'cashIn' => 'sum([[cashIn]])',
                'cashOut' => 'sum([[cashOut]])',
                'netCash' => 'sum([[netCash]])',
                'operatorProfit' => 'sum([[operatorProfit]])',
                'dailyCashIn' => 'avg([[cashIn]])',
                'dailyCashOut' => 'avg([[cashOut]])',
                'dailyNet' => 'avg([[cashIn]] - [[cashOut]])',
                'gameName' => 'programName',
            ])
            ->from([
                'log' => $logQuery
            ])
            ->groupBy(['terminal', 'programName'])
            ->asArray();

        $this->applySortingToQuery($this->_query, 'terminal+asc');
    }
}