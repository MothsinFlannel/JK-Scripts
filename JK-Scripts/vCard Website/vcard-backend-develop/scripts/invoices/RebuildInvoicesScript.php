<?php


namespace app\scripts\invoices;


use app\components\Script;
use app\models\ext\Invoice;
use app\models\ext\Location;
use Throwable;
use vr\core\ArrayObject;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use yii\base\Event;

/**
 * Class RebuildInvoicesScript
 * @package app\scripts\convolutions
 */
class RebuildInvoicesScript extends Script
{
    /**
     *
     */
    const EVENT_REBUILD = 'rebuild';

    /**
     *
     */
    const THRESHOLD = 0.001;

    /**
     * @var string | null
     */
    public ?string $since = null;

    /**
     * @var string | null
     */
    public ?string $until = null;

    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['since', 'required'],
            //            [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
            ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @throws ErrorsException
     * @throws Throwable
     */
    protected function onExecute()
    {
        $query = Invoice::find()
            ->with('invoiceItems')
            ->andWhere('since >= :since', [':since' => $this->since])
            ->andFilterCompare('until', $this->until, '<=')
            ->andWhere([
                'status' => Invoice::STATUS_UNPAID,
            ])
            ->andWhere('invoice.id is not null');

        /** @var Invoice $invoice */
        foreach ($query->each() as $invoice) {
            $invoice->refreshInvoiceItems($invoice, true);

            $amount = round($invoice->getInvoiceItems()->sum('balance'), 2);

            if (abs($invoice->amount - $amount) > self::THRESHOLD) {
                $this->triggerInvoiceRebuild($invoice, $amount);

                $invoice->amount = $amount;
                if (!$invoice->save(false, ['amount'])) {
                    throw new ErrorsException($invoice->errors);
                }
            }
        }
    }

    /**
     * @param Invoice $invoice
     * @param float|null $amount
     */
    protected function triggerInvoiceRebuild(Invoice $invoice, ?float $amount): void
    {
        $this->trigger(self::EVENT_REBUILD, new Event([
            'sender' => new ArrayObject([
                'location' => $invoice->location->name,
                'invoiceId' => $invoice->id,
                'oldAmount' => $invoice->amount,
                'newAmount' => $amount,
            ])
        ]));
    }
}