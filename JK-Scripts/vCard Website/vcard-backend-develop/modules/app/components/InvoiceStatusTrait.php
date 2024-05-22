<?php

namespace app\modules\app\components;

use app\models\ext\Invoice;
use vr\core\ErrorsException;

/**
 *
 */
trait InvoiceStatusTrait
{
    /**
     * @param $invoiceId
     * @return void
     * @throws ErrorsException
     */
    protected function updateInvoiceStatus($invoiceId): void
    {
        $invoice = Invoice::findOne($invoiceId);

        $invoice->status = $invoice->unpaidAmount > 0 ? Invoice::STATUS_UNPAID : Invoice::STATUS_PAID;
        if (!$invoice->getPayments()->count()) {
            $invoice->status = Invoice::STATUS_UNPAID;
        }

        if (!$invoice->save(true, ['status'])) {
            throw new ErrorsException($invoice->errors);
        }
    }
}