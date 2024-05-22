<?php


namespace app\modules\app\scripts\invoices;


use app\models\ext\Invoice;
use vr\core\ErrorsException;
use vr\core\Script;
use vr\core\utils\HttpCode;
use vr\core\validators\ExistValidator;

/**
 * Class GetInvoiceScript
 * @package app\modules\app\scripts\invoices
 * @property Invoice $entity
 */
class GetInvoiceScript extends Script
{
    /**
     * @var int|null
     */
    public ?int $id = null;

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
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws ErrorsException
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return (new ViewInvoiceScript(['token' => $this->_entity->token]))->execute()->toArray();
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
        $this->_entity = Invoice::findOne($this->id);
    }
}