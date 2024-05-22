<?php


namespace app\modules\app\scripts\invoices;


use app\models\ext\Invoice;
use vr\core\Script;
use vr\core\utils\HttpCode;
use vr\core\validators\ExistValidator;

/**
 * Class ViewInvoiceScript
 * @package app\modules\app\scripts\invoices
 * @property Invoice $entity
 */
class ViewInvoiceScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $token = null;

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
            ['token', 'required'],
            ['token', ExistValidator::class, 'statusCode' => HttpCode::NOT_FOUND, 'targetClass' => Invoice::class],
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
            'invoice' => $this->_entity->toArray([], [
                'location',
                'payments',
                'invoiceItems',
                'totals',
                'invoiceItems.notes'
            ])
        ];
    }

    /**
     * @return Invoice|null
     */
    public function getEntity(): ?Invoice
    {
        return $this->_entity;
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Invoice::findOne(['token' => $this->token]);
    }
}