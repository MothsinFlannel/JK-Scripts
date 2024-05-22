<?php


namespace app\modules\app\scripts\payments;


use app\components\Script;
use app\models\ext\Payment;
use app\modules\app\components\InvoiceStatusTrait;
use Throwable;
use vr\core\validators\ExistValidator;

/**
 * Class DeletePaymentScript
 * @package app\modules\app\payments
 */
class DeletePaymentScript extends Script
{
    use InvoiceStatusTrait;

    /**
     * @var int
     */
    public int $id;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Payment::class]
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        try {
            $payment = Payment::findOne($this->id);
            $payment->delete();
            $this->updateInvoiceStatus($payment->invoiceId);
        } catch (Throwable) {
        }
    }
}