<?php


namespace app\modules\reports\scripts\locations;


use app\components\ExportQueryTrait;
use app\models\Category;
use app\models\CategoryQuery;
use app\models\ext\Location;
use app\models\Redemption;
use app\models\RedemptionItem;
use vr\core\ArrayHelper;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class RedemptionsReportScript
 * @package app\modules\reports\scripts\locations
 */
class RedemptionsReportScript extends Script
{
    use ExportQueryTrait;

    /**
     * @var int | null
     */
    public ?int $locationId;

    /**
     * @var string|null
     */
    public ?string $since;

    /**
     * @var string|null
     */
    public ?string $until;

    /**
     * @var bool
     */
    public bool $export = false;

    /**
     * @var CategoryQuery | null
     */
    private ?CategoryQuery $_query;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            [['locationId', 'since', 'until'], 'required'],
            ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
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
            'amount' => 'float,%f',
        ]);

        if ($this->export) {
            $headers = ['category', 'amount'];
            $this->arrayToCsv($results, $headers);
        }

        return [
            'results' => $results,
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_query = Category::find()
            ->select([
                'category.name',
                'amount' => 'sum(redemption.amount)',
            ])
            ->leftJoin([
                'redemption' => RedemptionItem::find()
                    ->select([
                        'amount' => 'sum([[amount]])',
                        'category'
                    ])
                    ->rightJoin([
                        'redemption' => Redemption::find()
                            ->andWhere(['locationId' => $this->locationId])
                            ->since($this->since)
                            ->until($this->until)
                    ], '[[redemptionItem.redemptionId]] = redemption.id')
                    ->groupBy('category')
            ], 'redemption.category = category.name')
            ->groupBy('category.name')
            ->orderBy('category.name')
            ->asArray();
    }
}