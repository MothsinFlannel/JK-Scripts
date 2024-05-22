<?php


namespace app\modules\reports\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\ext\Terminal;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use yii\db\ActiveQuery;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class LocationStandardReportScript
 * @package app\modules\reports\scripts\locations
 */
class LocationStandardReportScript extends PagedListScript
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
        $items = $this->_query->all();

        $results = ArrayHelper::typecast($items, [
            'cashIn' => 'float, %0.2f',
            'cashOut' => 'float, %0.2f',
            'netCash' => 'float, %0.2f',
            'dailyCashIn' => 'float, %0.2f',
            'dailyNet' => 'float, %0.2f',
        ]);

        if ($this->export) {
            $headers = [
                'terminal' => 'Device ID',
                'programName' => 'Game Title',
                'cashIn',
                'cashOut',
                'netCash',
                'dailyCashIn',
                'dailyNet',
            ];
            $this->arrayToCsv($results, $headers);
        }

        return [
            'count' => (int)$this->_query->count(),
            'results' => $results,
            'totals' => [
                'cashIn' => round($this->_query->sum('[[cashIn]]'), 2),
                'cashOut' => round($this->_query->sum('[[cashOut]]'), 2),
                'netCash' => round($this->_query->sum('[[cashIn]] - [[cashOut]]'), 2),
            ],
            'location' => $this->_location->toArray([], ['operatorEmail', 'route'])
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_location = Location::findOne($this->locationId);

        $this->_query = Convolution::find()
            ->select([
                'deviceId' => 'terminal',
                'gameName' => 'terminal.programName',
                'cashIn' => 'sum([[moneyIn]])',
                'cashOut' => 'sum([[moneyOut]])',
                'netCash' => 'sum([[moneyIn]] - [[moneyOut]])',
                'dailyCashIn' => 'avg([[moneyIn]])',
                'dailyNet' => 'avg([[moneyIn]] - [[moneyOut]])',
            ])
            ->leftJoin([
                'terminal' => Terminal::find()
                    ->select([
                        'number',
                        'programName'
                    ])
                    ->andWhere(['locationId' => $this->locationId])
            ], '[[terminal.number]] = [[convolution.terminal]]')
            ->andWhere(['locationId' => $this->locationId])
            ->groupBy(['terminal', 'terminal.programName'])
            ->since($this->since)
            ->until($this->until)
            ->asArray();


        $this->applySortingToQuery($this->_query, 'terminal+asc');
    }
}