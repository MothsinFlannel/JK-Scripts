<?php


namespace app\modules\app\scripts\payments;


use app\components\Script;
use app\models\ext\Payment;
use app\modules\app\components\InvoiceStatusTrait;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;

/**
 * Class EditPaymentScript
 * @package app\modules\app\payments
 */
class EditPaymentScript extends Script
{
    use InvoiceStatusTrait;

    /**
     * @var int
     */
    public int $id;
    /**
     * @var array
     */
    public array $payment = [];
    /**
     * @var Payment|null
     */
    private ?Payment $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            [['id', 'payment'], 'required'],
            ['id', ExistValidator::class, 'targetClass' => Payment::class]
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
            'payment' => $this->_entity->toArray(),
        ];
    }

    /**
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Payment::findOne($this->id);
        $this->_entity->attributes = $this->payment;

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }

        $this->updateInvoiceStatus($this->_entity->invoiceId);
    }
}