<?php


namespace app\modules\app\scripts\invoices;


use app\components\Script;
use app\models\ext\Invoice;
use app\models\InvoiceQuery;
use app\models\Payment;
use app\modules\app\components\InvoiceStatusTrait;
use DateTime;
use vr\core\ErrorsException;
use vr\core\utils\HttpCode;
use vr\core\validators\ExistValidator;
use Yii;

/**
 * Class PayInvoiceScript
 * @package app\modules\app\scripts\invoices
 */
class PayInvoiceScript extends Script
{
    use InvoiceStatusTrait;

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var float|null
     */
    public ?float $amount = null;

    /**
     * @var string|null
     */
    public ?string $notes = null;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
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
                'message' => Yii::t('app', 'Invoice is not found or already paid'),
            ],
            ['id', 'checkAmount'],
            ['amount', 'number', 'min' => 0.01],
            ['notes', 'trim']
        ];
    }

    /**
     * @return void
     */
    public function checkAmount(): void
    {
        $invoice = Invoice::findOne($this->id);
        if ($invoice->unpaidAmount < $this->amount) {
            $this->addError('amount', Yii::t('app', 'Amount cannot be greater than {0}', [$invoice->unpaidAmount]));
        }
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [];
    }

    /**
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $payment = new Payment([
            'invoiceId' => $this->id,
            'amount' => $this->amount,
            'paidOn' => (new DateTime('today'))->format('Y-m-d'),
            'notes' => $this->notes
        ]);

        if (!$payment->save() || !$payment->refresh()) {
            throw new ErrorsException($payment->errors);
        }

        $this->updateInvoiceStatus($payment->invoiceId);
    }
}