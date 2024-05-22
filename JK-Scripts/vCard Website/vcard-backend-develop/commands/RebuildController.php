<?php


namespace app\commands;

use app\scripts\convolutions\RebuildConvolutionsScript;
use app\scripts\invoices\RebuildInvoicesScript;
use Throwable;
use Yii;
use yii\base\Event;
use yii\console\Controller;
use yii\db\Connection;
use yii\helpers\Console;

/**
 * Class RebuildController
 * @package app\commands
 */
class RebuildController extends Controller
{
    /**
     * @param string $since
     * @param bool $commit
     * @throws Throwable
     */
    public function actionConvolutions(string $since, bool $commit = true)
    {
        $commit = filter_var($commit, FILTER_VALIDATE_BOOLEAN);
        if (!$commit) {
            Console::output('Verbose mode! No changes will be applied');
        }

        Yii::$app->db->transaction(function (Connection $connection) use ($since, $commit) {
            $script = new RebuildConvolutionsScript([
                'since' => $since
            ]);

            $script->on(RebuildConvolutionsScript::EVENT_REBUILD, function (Event $event) {
                $data = $event->sender;
                Console::output("Location $data->location, Terminal #$data->terminal, Date $data->date: $data->oldAmount -> $data->newAmount");
            });

            $script->execute();

            $commit ? $connection->transaction->commit() : $connection->transaction->rollBack();
        });
    }

    /**
     * @param string $since
     * @param bool $commit
     * @throws Throwable
     */
    public function actionInvoices(string $since, bool $commit = false)
    {
        $commit = filter_var($commit, FILTER_VALIDATE_BOOLEAN);

        if (!$commit) {
            Console::output('Verbose mode! No changes will be applied');
        }

        Yii::$app->db->transaction(function (Connection $connection) use ($since, $commit) {
            $script = new RebuildInvoicesScript([
                'since' => $since
            ]);

            $script->on(RebuildInvoicesScript::EVENT_REBUILD, function (Event $event) {
                $data = $event->sender;
                Console::output("Invoice #$data->invoiceId, Location $data->location: $data->oldAmount -> $data->newAmount");
            });

            $script->execute();

            $commit ? $connection->transaction->commit() : $connection->transaction->rollBack();
        });
    }
}