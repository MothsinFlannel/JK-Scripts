<?php


namespace app\commands;


use app\models\ext\Invoice;
use app\scripts\invoices\InvoiceItemsTrait;
use app\scripts\invoices\MassIssueInvoicesScript;
use Throwable;
use vr\core\ErrorsException;
use Yii;
use yii\console\Controller;
use yii\db\Connection;
use yii\helpers\Console;

/**
 * Class InvoicesController
 * @package app\commands
 */
class InvoicesController extends Controller
{
    use InvoiceItemsTrait;

    /**
     * @param int $weeksEarlier
     * @throws ErrorsException
     * @throws Throwable
     */
    public function actionIssue(int $weeksEarlier = 0)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            (new MassIssueInvoicesScript([
                'weeksEarlier' => $weeksEarlier
            ]))->execute();
        } catch (Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }

        $transaction->commit();
    }

    /**
     * @throws ErrorsException
     * @throws Throwable
     * @deprecated
     */
    public function actionGenerateOrderItems()
    {
        Yii::$app->db->transaction(function (Connection $connection) {
            $invoiceQuery = Invoice::find();
            $count        = $invoiceQuery->count();

            Console::startProgress(0, $count, 'Generating invoice items ');

            foreach ($invoiceQuery->each() as $index => $invoice) {
                $this->refreshInvoiceItems($invoice, true);
                Console::updateProgress($index + 1, $count, 'Generating invoice items ');
            }

            Console::endProgress('Finished');

            $connection->transaction->commit();
        });
    }
}