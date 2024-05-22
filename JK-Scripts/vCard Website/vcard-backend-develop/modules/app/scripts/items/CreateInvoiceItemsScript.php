<?php


namespace app\modules\app\scripts\items;


use app\models\ext\Invoice;
use app\models\ext\InvoiceItem;
use Exception;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 * Class CreateInvoiceItemScript
 * @package app\modules\app\scripts\items
 */
class CreateInvoiceItemsScript extends Script
{
    /**
     * @var array
     */
    public array $invoiceItems = [];

    /**
     * @var InvoiceItem[]
     */
    private array $_entities = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['invoiceItems', 'required'],
            [
                'invoiceItems',
                'each',
                'rule' => [
                    NestedValidator::class,
                    'rules' => [
                        [['invoiceId', 'balance', 'title'], 'required'],
                        ['invoiceId', ExistValidator::class, 'targetClass' => Invoice::class, 'targetAttribute' => 'id']
                    ]

                ]
            ]
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
            'invoiceItems' => $this->_entities
        ];
    }

    /**
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        foreach ($this->invoiceItems as $invoiceItem) {
            $item = new InvoiceItem();
            $item->attributes = ArrayHelper::filter($invoiceItem, ['invoiceId', 'balance', 'title']);
            $item->type = InvoiceItem::TYPE_EXTRA;
            if (!$item->save() || !$item->refresh()) {
                throw new ErrorsException($item->errors);
            }

            $this->_entities[] = $item;
        }

        $invoice = Invoice::findOne(ArrayHelper::getValue($this->_entities, [0, 'invoiceId']));
        $invoice->amount = round($invoice->getInvoiceItems()->sum('balance'), 2);

        if (!$invoice->save()) {
            throw new ErrorsException($invoice->errors);
        }
    }
}