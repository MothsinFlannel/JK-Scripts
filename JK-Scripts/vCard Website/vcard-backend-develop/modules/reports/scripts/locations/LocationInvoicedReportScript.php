<?php


namespace app\modules\reports\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\TerminalQuery;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class LocationInvoicedReportScript
 * @package app\modules\reports\scripts\locations
 */
class LocationInvoicedReportScript extends PagedListScript
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
        $results = ArrayHelper::typecast($this->_query->all(), [
            'cashIn' => 'float, %0.2f',
            'cashOut' => 'float, %0.2f',
            'netCash' => 'float, %0.2f'
        ]);

        if ($this->export) {
            $headers = [
                'terminal' => 'Device ID',
                'programName' => 'Game Title',
                'cashIn',
                'cashOut',
                'netCash'
            ];
            $this->arrayToCsv($results, $headers);
        }

        return [
            'count' => (int)$this->_query->count(),
            'results' => $results,
            'location' => $this->_location->toArray([], ['operatorEmail', 'route'])
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_location = Location::findOne($this->locationId);

        $this->_query = $this->_location->getTerminals()
            ->select([
                'deviceId' => 'terminal.number',
                'gameName' => 'terminal.programName',
                'cashIn',
                'cashOut',
                'netCash' => '[[cashIn]] - [[cashOut]]'
            ])
            ->leftJoin('cabinetType', '[[cabinetType.id]] = [[terminal.cabinetTypeId]]')
            ->rightJoin([
                'convolution' => Convolution::find()
                    ->select([
                        'terminal',
                        'cashIn' => 'sum([[moneyIn]])',
                        'cashOut' => 'sum([[moneyOut]])',
                    ])
                    ->andWhere(['locationId' => $this->locationId])
                    ->since($this->since)
                    ->until($this->until)
                    ->groupBy('terminal')
            ], 'convolution.terminal = terminal.number')
            ->andWhere('terminal.id is not null')
            ->asArray();

        $this->applySortingToQuery($this->_query, 'number+asc');
    }
}