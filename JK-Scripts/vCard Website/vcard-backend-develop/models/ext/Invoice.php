<?php


namespace app\models\ext;

use app\components\ReformatTimestampBehavior;
use app\models\InvoiceItemQuery;
use app\models\InvoiceQuery;
use app\models\PaymentQuery;
use app\scripts\invoices\InvoiceItemsTrait;
use Throwable;
use Yii;

/**
 * Class Invoice
 * @package app\models\ext
 * @property-read float $unpaidAmount
 * @property-read float $totals
 * @property-read Payment[] $payments
 */
class Invoice extends \app\models\Invoice
{
    use InvoiceItemsTrait;

    /**
     *
     */
    const STATUS_UNPAID = 'unpaid';
    /**
     *
     */
    const STATUS_PAID = 'paid';

    /**
     *
     */
    const NOTES_UP_TO_DATE = 'Up to date';
    /**
     *
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * @var array
     */
    private array $_totals = [];

    /**
     * {@inheritdoc}
     * @return InvoiceQuery the active query used by this AR class.
     * @throws Throwable
     */
    public static function find(): InvoiceQuery
    {
        $query = new InvoiceQuery(get_called_class());

        if (!Yii::$app->user->isGuest && !User::loggedIn()->isLive) {
            $query->andWhere('1 = 0');
        }

        return $query;
    }

    /**
     * @return array|string[]
     */
    public function extraFields(): array
    {
        return [
            'location',
            'payments',
            'unpaidAmount',
            'totals',
            'invoiceItems'
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function scenarios(): array
    {
        return parent::scenarios() + [
                self::SCENARIO_UPDATE => ['notes', 'since', 'until', 'amount'],
            ];
    }

    /**
     * @return array
     * @deprecated
     */
    public function getTotals(): array
    {
        $this->_totals = $this->_totals ?: [
            'totalIn' => round($this->getInvoiceItems()->sum('[[totalIn]]') ?: 0, 2),
            'totalOut' => round($this->getInvoiceItems()->sum('[[totalOut]]') ?: 0, 2),
            'revenue' => round($this->getInvoiceItems()->sum('revenue') ?: 0, 2),
            'balance' => round($this->getInvoiceItems()->sum('balance') ?: 0, 2),
            'paid' => round($this->getPayments()->sum('amount') ?: 0, 2),
            'due' => $this->unpaidAmount,
            'weeklyRevenueTotal' => 0,
            'previousBalance' => 0,
            'revenueToSplit' => 0,
            'dueToLocation' => 0,
            'dueToCompany' => 0,
        ];

        return $this->_totals;
    }

    /**
     * @return InvoiceItemQuery
     */
    public function getInvoiceItems(): InvoiceItemQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasMany(InvoiceItem::class, ['invoiceId' => 'id'])->orderBy('id');
    }

    /**
     * @return PaymentQuery
     */
    public function getPayments(): PaymentQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasMany(Payment::class, ['invoiceId' => 'id']);
    }

    /**
     * @return float
     */
    public function getUnpaidAmount(): float
    {
        return round($this->amount - (float)$this->getPayments()->sum('amount'), 2);
    }
}