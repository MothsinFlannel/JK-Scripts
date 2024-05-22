<?php


namespace app\modules\app\scripts\invoices;


use app\models\ext\Invoice;
use app\models\ext\Location;
use app\models\ext\Payment;
use app\models\ext\User;
use app\models\InvoiceQuery;
use app\modules\app\components\RecentJobsTrait;
use app\scripts\invoices\InvoicesListTrait;
use DateTime;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 * Class InvoicesListScript
 * @package app\modules\app\scripts\invoices
 */
class InvoicesListScript extends PagedListScript
{
    use InvoicesListTrait;
    use RecentJobsTrait;

    /**
     * @var bool
     */
    public bool $extraFields = true;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var InvoiceQuery
     */
    protected InvoiceQuery $_query;

    /**
     * @var array
     */
    private array $_invoicing = [];

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [
                'filters',
                NestedValidator::class,
                'rules' => [
                    ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
                    // TODO: turn on when the issue is fixed in Yii. Most likely it will happen in 2.0.48
                    // [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
                ],
                'objectify' => true
            ],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws Throwable
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $payments = Payment::find()
            ->select([
                'paid' => 'sum(payment.amount)',
                'payment.invoiceId',
            ])
            ->rightJoin([
                'invoice' => $this->_query
            ], '[[invoice.id]] = [[payment.invoiceId]]')
            ->groupBy('[[payment.invoiceId]]')
            ->asArray()
            ->all();

        $payments = ArrayHelper::map($payments, 'invoiceId', 'paid');

        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Invoice $invoice) use ($payments) {
                $expand = ['location'];

                if ($this->extraFields) {
                    $expand = array_merge($expand, [
                        'location',
                        'invoiceItems',
                        'invoiceItems.notes',
                        'totals',
                        'payments'
                    ]);
                }

                return $invoice->toArray([], $expand) + [
                        'due' => round($invoice->amount - ArrayHelper::getValue($payments, $invoice->id, 0), 2)
                    ];
            }),
            'invoicing' => $this->_invoicing,
            '_state' => [
                'jobs' => [
                    'context' => [
                        'category' => 'app/invoices/export',
                        'items' => $this->getRecentJobQuery()->limit(1)->all(),
                        'count' => $this->getRecentJobQuery()->count(),
                    ],
                ]
            ]
        ];
    }

    /**
     *
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $this->_query = $this
            ->createQuery(User::loggedIn(), $this->filters)
            ->offset($this->offset)->limit($this->limit);

        $this->applySortingToQuery($this->_query, 'since+desc');

        if (@$this->filters->locationId) {
            $location = Location::findOne($this->filters->locationId);

            if ($location->invoicingMode == Location::INVOICING_MODE_CUSTOM) {
                $since = new DateTime($location->getInvoices()->max('until') ?: $location->createdAt);
                $until = (new DateTime('yesterday'));

                $this->_invoicing = [
                    'since' => $since->format('Y-m-d'),
                    'until' => $until->format('Y-m-d'),
                ];
            }
        }
    }
}