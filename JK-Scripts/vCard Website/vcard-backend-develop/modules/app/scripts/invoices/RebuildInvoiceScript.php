<?php


namespace app\modules\app\scripts\invoices;


use app\components\Script;
use app\models\Convolution;
use app\models\ext\Invoice;
use app\models\InvoiceQuery;
use app\scripts\invoices\InvoiceItemsTrait;
use vr\core\ErrorsException;
use vr\core\utils\HttpCode;
use vr\core\validators\ExistValidator;

/**
 * Class EditInvoiceScript
 * @package app\modules\app\scripts\invoices
 */
class RebuildInvoiceScript extends Script
{
    use InvoiceItemsTrait;

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var float|null
     */
    public ?float $splitPercent = null;

    /**
     * @var Invoice | null
     */
    private ?Invoice $_entity;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'statusCode' => HttpCode::NOT_FOUND, 'targetClass' => Invoice::class],
            [
                'id',
                ExistValidator::class,
                'statusCode' => HttpCode::NOT_FOUND,
                'targetClass' => Invoice::class,
                'filter' => function (InvoiceQuery $query) {
                    return $query->andWhere(['status' => Invoice::STATUS_UNPAID]);
                },
                'message' => 'Only unpaid invoices can be changed'
            ],
            ['splitPercent', 'number'],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'invoice' => $this->_entity->toArray([], ['location', 'payments', 'invoiceItems', 'totals']),
        ];
    }

    /**
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Invoice::findOne($this->id);
        $this->_entity->splitPercent = $this->splitPercent !== null ? $this->splitPercent : $this->_entity->splitPercent;

        $this->rebuildConvolutions();
        $this->refreshInvoiceItems($this->_entity, true);

        $this->_entity->amount = $this->_entity->getInvoiceItems()->sum('balance');

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }

    /**
     * @throws ErrorsException
     */
    private function rebuildConvolutions(): float
    {
        $total = 0;

        $query = Convolution::find()
            ->since($this->_entity->since)
            ->until($this->_entity->until)
            ->andWhere(['locationId' => $this->_entity->locationId]);

        /** @var Convolution $convolution */
        foreach ($query->each() as $convolution) {
            $convolution->percentageProfit = round(($convolution->moneyIn - $convolution->moneyOut) * $this->_entity->splitPercent / 100, 2);
            $convolution->flatFee = $this->_entity->location->flatFee;

            if (!$convolution->save()) {
                throw new ErrorsException($convolution->errors);
            }

            $total += $convolution->percentageProfit + $convolution->flatFee;
        }

        return round($total, 2);
    }
}