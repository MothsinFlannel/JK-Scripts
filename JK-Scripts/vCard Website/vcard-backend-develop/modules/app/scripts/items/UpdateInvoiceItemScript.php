<?php


namespace app\modules\app\scripts\items;


use app\models\ext\InvoiceItem;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class UpdateInvoiceItemScript
 * @package app\modules\app\scripts\items
 */
class UpdateInvoiceItemScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var array
     */
    public array $invoiceItem = [];

    /**
     * @var InvoiceItem|null
     */
    private ?InvoiceItem $_entity;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'invoiceItem'], 'required'],
            ['id', ExistValidator::class, 'targetClass' => InvoiceItem::class],
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
            'invoiceItem' => $this->_entity,
        ];
    }

    /**
     *
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = InvoiceItem::findOne($this->id);
        $this->_entity->attributes = ArrayHelper::filter($this->invoiceItem, ['totalIn', 'totalOut', 'title']);
        $this->_entity->revenue = round($this->_entity->totalIn - $this->_entity->totalOut, 2);
        $this->_entity->balance = round($this->_entity->revenue * $this->_entity->invoice->splitPercent / 100, 2);

        if ($this->_entity->type !== InvoiceItem::TYPE_EXTRA) {
            $this->_entity->type = InvoiceItem::TYPE_MANUAL;
        }

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }

        $invoice = $this->_entity->invoice;
        $invoice->amount = round($invoice->getInvoiceItems()->sum('balance'), 2);
        if (!$invoice->save()) {
            throw new ErrorsException($invoice->errors);
        }
    }
}