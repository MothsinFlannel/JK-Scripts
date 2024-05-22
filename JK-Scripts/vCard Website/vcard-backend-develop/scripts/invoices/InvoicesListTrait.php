<?php

namespace app\scripts\invoices;

use app\models\ext\Invoice;
use app\models\ext\Location;
use app\models\ext\Payment;
use app\models\ext\User;
use app\models\InvoiceQuery;
use ArrayObject;
use Throwable;
use yii\db\Expression;

/**
 *
 */
trait InvoicesListTrait
{
    /**
     * @param object $filters
     * @return InvoiceQuery
     * @throws Throwable
     */
    public function createQuery(User $user, object|array $filters): InvoiceQuery
    {
        $filters = new ArrayObject($filters, ArrayObject::ARRAY_AS_PROPS);
        
        return Invoice::find()
            ->select([
                '*',
                'location' => 'location.name',
                'due' => new Expression('invoice.amount - coalesce(payment.paid, 0)')
            ])
            ->andFilterWhere([
                'since' => @$filters->since,
                'until' => @$filters->until,
                'id' => @$filters->ids,
            ])
            ->leftJoin([
                'payment' => Payment::find()->select([
                    'paid' => 'sum(amount)',
                    'invoiceId',
                ])->groupBy('[[invoiceId]]')
            ], '[[payment.invoiceId]] = [[invoice.id]]')
            ->rightJoin([
                'location' => Location::find()->select([
                    'locationId' => 'id',
                    'name'
                ])->andFilterWhere([
                    'companyId' => $user->companyId,
                    'routeId' => @$filters->routeId
                ])->filter(@$filters->query)
            ], '[[location.locationId]] = [[invoice.locationId]]')
            ->andFilterWhere([
                'invoice.locationId' => @$filters->locationId,
                'status' => @$filters->status,
            ])->andWhere('invoice.id is not null');
    }
}