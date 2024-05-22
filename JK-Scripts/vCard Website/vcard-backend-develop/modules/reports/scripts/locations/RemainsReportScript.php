<?php

namespace app\modules\reports\scripts\locations;

use app\components\ExportQueryTrait;
use app\models\ext\Invoice;
use app\models\ext\InvoiceItem;
use app\modules\api\models\Location;
use app\modules\app\components\FilterByTrait;
use app\scripts\invoices\InvoiceItemsTrait;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\Script;
use vr\core\validators\NestedValidator;
use yii\web\RangeNotSatisfiableHttpException;

/**
 *
 */
class RemainsReportScript extends Script
{
    use FilterByTrait;
    use ExportQueryTrait;
    use InvoiceItemsTrait;

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
    private array $_data = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['since', 'until'], 'required'],
            // [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
            ['export', 'boolean'],
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['query', 'trim'],
                    ['active', 'boolean'],
                ],
                'objectify' => true,
            ]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array[]
     * @throws RangeNotSatisfiableHttpException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        if ($this->export) {
            $this->arrayToCsv($this->_data, [
                'name' => 'Location',
                'address',
                'city',
                'state',
                'zipCode',
                'county',
                'number' => 'Terminal #',
                'title' => 'Program Name',
                'revenueRemain',
                'balanceRemain',
            ], true);
        }

        return [
            'count' => count($this->_data),
            'results' => $this->_data
        ];
    }

    /**
     * @return void
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $query = Location::find()
            ->filter(@$this->filters->query)
            ->andFilterWhere([
                'city' => @$this->filters->city,
                'county' => @$this->filters->county,
                'zipCode' => @$this->filters->zipCode,
                'state' => @$this->filters->state,
                'companyId' => @$this->filters->companyId,
                'isActive' => @$this->filters->active,
                'isLive' => true,
            ])
            ->live();

        /** @var Location $each */
        foreach ($query->each() as $each) {
            $location = [
                'locationId' => $each->id,
                'name' => $each->name,
                'address' => $each->address,
                'city' => $each->city,
                'state' => $each->state,
                'county' => $each->county,
                'zipCode' => $each->zipCode,
                'isActive' => $each->isActive,
            ];

            $invoice = new Invoice([
                'locationId' => $each->id,
                'since' => $this->since,
                'until' => $this->until,
            ]);

            $invoiceItems = $this->generateItems($invoice);

            $existingInvoice = Invoice::findOne([
                'locationId' => $each->id,
                'since' => $this->since,
                'until' => $this->until,
            ]);

            $existingInvoiceItems = ArrayHelper::index($existingInvoice->invoiceItems ?? [], 'number');

            /** @var InvoiceItem $invoiceItem */
            foreach ($invoiceItems as $invoiceItem) {
                $balanceRemain = $invoiceItem->balance - ArrayHelper::getValue($existingInvoiceItems, [$invoiceItem->number, 'balance']);
                $revenueRemain = $invoiceItem->revenue - ArrayHelper::getValue($existingInvoiceItems, [$invoiceItem->number, 'revenue']);

                if ($revenueRemain || $balanceRemain) {
                    $this->_data[] = array_merge($location, [
                        'number' => $invoiceItem->number,
                        'title' => $invoiceItem->title,
                        'revenueRemain' => round($revenueRemain, 2),
                        'balanceRemain' => round($balanceRemain, 2),
                    ]);
                }
            }
        }
    }
}