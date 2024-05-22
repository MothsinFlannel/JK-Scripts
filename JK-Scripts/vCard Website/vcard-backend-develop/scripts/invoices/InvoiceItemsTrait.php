<?php


namespace app\scripts\invoices;


use app\models\ext\Convolution;
use app\models\ext\Invoice;
use app\models\ext\InvoiceItem;
use app\models\ext\Terminal;
use DateTime;
use Exception;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use yii\db\ActiveRecord;

/**
 * Trait InvoiceItemsTrait
 * @package app\scripts\invoices
 */
trait InvoiceItemsTrait
{
    /**
     * @param Invoice $invoice
     * @param bool $autoSave
     * @return array
     */
    public function refreshInvoiceItems(Invoice $invoice, bool $autoSave = false): array
    {
        $invoiceItems = $this->generateItems($invoice);

        return ArrayHelper::getColumn($invoiceItems, function (InvoiceItem $invoiceItem) use ($invoice, $autoSave) {

            /** @var InvoiceItem $existing */
            $existing = $invoice->getInvoiceItems()
                ->andWhere([
                    'number' => $invoiceItem->number,
                    'type' => [InvoiceItem::TYPE_AUTOMATIC, InvoiceItem::TYPE_MANUAL]
                ])
                ->one();

            if ($existing && $existing->type != InvoiceItem::TYPE_AUTOMATIC) {
                // The invoice item was changed manually. We cannot change it
                return $existing;
            }

            if (!$existing) {
                $existing = new InvoiceItem([
                    'type' => InvoiceItem::TYPE_AUTOMATIC,
                ]);
            }

            $existing->attributes = $invoiceItem->attributes;
            $existing->invoiceId  = $invoice->id;

            if ($autoSave && !$existing->save()) {
                throw new ErrorsException($existing->errors);
            }

            return $existing;
        });
    }

    /**
     * @param Invoice $invoice
     * @return array
     */
    public function generateItems(Invoice $invoice): array
    {
        $query = Convolution::find()
            ->select([
                'number' => 'coalesce([[groupName]], terminal::varchar)',
                'title' => '(array_agg([[terminal.programName]] order by [[terminal.programName]]))[1]',
                'totalIn' => 'sum([[moneyIn]])',
                'totalOut' => 'sum([[moneyOut]])',
                'revenue' => 'sum([[moneyIn]] - [[moneyOut]])',
                'balance' => 'sum([[percentageProfit]] + [[flatFee]])'
            ])
            ->live()
            ->andWhere(['locationId' => $invoice->locationId])
            ->since($invoice->since)
            ->until($invoice->until)
            ->groupBy('terminal')
            ->leftJoin([
                'terminal' => Terminal::find()
                    ->select([
                        'number',
                        'groupName' => 'coalesce(nullif(trim([[terminal.groupName]]),:empty), number::varchar)',
                        'programName' => '(array_agg([[terminal.programName]] order by [[terminal.programName]]))[1]',
                    ])
                    ->andWhere(['locationId' => $invoice->locationId])
                    ->groupBy(['number', 'groupName'])
                    ->addParams([':empty' => ''])
            ], 'terminal.number = convolution.terminal')
            ->groupBy([
                'coalesce([[groupName]], terminal::varchar)',
            ])
            ->asArray();

        $invoiceItems = ArrayHelper::getColumn($query->asArray()->all(), function (array $attributes) use ($invoice) {
            $invoiceItem = new InvoiceItem();
            $invoiceItem->populateRelation('invoice', $invoice);

            $invoiceItem->attributes = ArrayHelper::typecast($attributes, [
                'totalIn' => 'float, %0.2f',
                'totalOut' => 'float, %0.2f',
                'revenue' => 'float, %0.2f',
                'balance' => 'float, %0.2f',
            ]);


            $lastLogAt = (new DateTime())->format('Y-m-d H:i:s');

            $invoiceItem->lastLogAt = $lastLogAt;
            $invoiceItem->type      = InvoiceItem::TYPE_AUTOMATIC;
            $invoiceItem->notes     = $this->generateNotes($invoiceItem) ?: Invoice::NOTES_UP_TO_DATE;

            return $invoiceItem;
        });

        // This is because of a mix of numbers and group names
        ArrayHelper::multisort($invoiceItems, 'number', SORT_ASC, SORT_NATURAL);

        return $invoiceItems;
    }

    /**
     *
     * @throws Exception
     */
    public function generateNotes(InvoiceItem $item): ?string
    {
        // TODO: MOVE IT OUTSIDE THIS BLOCK
        $missedQuery = $item->invoice->location->getTerminals()
            ->select('number')
            ->leftJoin([
                'future' => \app\models\Convolution::find()
                    ->select('terminal')
                    ->andWhere(['locationId' => $item->invoice->locationId])
                    ->andWhere('date > :until', [':until' => $item->invoice->until])
                    ->live()
            ], '[[future.terminal]] = [[terminal.number]]')
            ->andWhere('future.terminal is null');

        if (!$missedQuery->exists()) {
            return null;
        }

        $convolutionsQuery = Convolution::find()
            ->select([
                'terminal',
                'groupName',
                'lastLogAt' => 'max([[lastLogAt]])',
            ])
            ->live()
            ->leftJoin([
                'terminal' => Terminal::find()
                    ->select([
                        'number',
                        'groupName' => 'coalesce(nullif(trim([[terminal.groupName]]),:empty), number::varchar)'
                    ])
                    ->andWhere(['locationId' => $item->invoice->locationId])
                    ->groupBy(['number', 'groupName'])
                    ->addParams([':empty' => ''])
            ], 'terminal.number = convolution.terminal')
            ->andWhere([
                'terminal' => $missedQuery->column() ?: [],
                'locationId' => $item->invoice->locationId,
            ])
            ->since($item->invoice->since)
            ->until($item->invoice->until)
            ->groupBy([
                '[[groupName]]',
                'terminal'
            ]);

        $minimals = ActiveRecord::find()
            ->select([
                'number' => 'coalesce([[groupName]], terminal::varchar)',
                'min' => 'max([[lastLogAt]])'
            ])
            ->from($convolutionsQuery)
            ->groupBy([
                'coalesce([[groupName]], terminal::varchar)',
            ])
            ->asArray()
            ->all();

        $minimals = ArrayHelper::map($minimals, 'number', 'min');
        $last     = ArrayHelper::getValue($minimals, $item->number);

        return 'Last log at ' . (new DateTime($last))->format('m/d h:i a');
    }
}