<?php

namespace app\modules\app\scripts\invoices;

use app\models\ext\Invoice;
use app\models\ext\InvoiceItem;
use app\models\InvoiceQuery;
use Exception;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\utils\HttpCode;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;
use Yii;

/**
 *
 */
class UpdateInvoiceScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var array
     */
    public array $invoice;

    /**
     * @var Invoice
     */
    private Invoice $_entity;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['invoice', 'id'], 'required'],
            [
                'id',
                ExistValidator::class,
                'targetClass' => Invoice::class,
                'filter' => function (InvoiceQuery $query) {
                    return $query->andWhere([
                        'status' => Invoice::STATUS_UNPAID
                    ]);
                },
                'statusCode' => HttpCode::NOT_FOUND,
                'message' => Yii::t('app', 'Invoice is not found or wrong status'),
            ],
            [
                'invoice',
                NestedValidator::class,
                'rules' => [
                    ['notes', 'string']
                ]
            ]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return Invoice[]
     * @throws ErrorsException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return (new ViewInvoiceScript(['token' => $this->_entity->token]))->execute()->toArray();
    }

    /**
     * @return void
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->_entity = Invoice::findOne($this->id);
        $this->_entity->scenario = Invoice::SCENARIO_UPDATE;

        // It is safe, see [[Invoice::scenarios]]
        $this->_entity->attributes = $this->invoice;

        InvoiceItem::deleteAll([
            'and',
            ['invoiceId' => $this->_entity->id],
        ]);

        $invoiceItems = ArrayHelper::getValue($this->invoice, 'invoiceItems');

        foreach ($invoiceItems ?: [] as $attributes) {
            $id = ArrayHelper::getValue($attributes, 'id');
            $invoiceItem = InvoiceItem::findOne($id) ?: new InvoiceItem([
                'invoiceId' => $this->_entity->id,
                'type' => InvoiceItem::TYPE_MANUAL,
            ]);

            $invoiceItem->attributes = ArrayHelper::filter($attributes, ['title', 'totalIn', 'totalOut', 'type', 'revenue', 'notes']);

            if ($invoiceItem->totalIn != null && $invoiceItem->totalOut != null) {
                $invoiceItem->revenue = $invoiceItem->totalIn - $invoiceItem->totalOut;
            }

            $invoiceItem->balance = round($invoiceItem->revenue * $this->_entity->splitPercent / 100, 2);

            if (!$invoiceItem->save() || !$invoiceItem->refresh()) {
                throw new ErrorsException($invoiceItem->errors);
            }
        }

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }

        $this->_entity->amount =
            ArrayHelper::getValue($this->invoice, 'balance') ?:
                ArrayHelper::sum($this->_entity->invoiceItems, 'balance') ?: 0;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}