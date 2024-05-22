<?php

namespace app\jobs\invoices;

use app\models\InvoiceQuery;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\View;
use ZipArchive;

/**
 *
 */
class HtmlExportJob extends BaseExportJob
{
    /**
     * @return string
     * @throws Exception
     */
    protected function getTitle(): string
    {
        return 'Export as HTML. ' . $this->getIntervalTitle();
    }

    /**
     * @return string
     */
    protected function getOutputType(): string
    {
        return 'zip';
    }

    /**
     * @param InvoiceQuery $query
     * @return void
     * @throws InvalidConfigException
     * @throws Throwable
     */
    protected function onExecute(InvoiceQuery $query): void
    {
        $filename = $this->getFilePath();

        $archive = new ZipArchive();
        $archive->open($filename, ZipArchive::CREATE);

        $total = $query->count();
        foreach ($query->all() as $i => $invoice) {
            $location = strtr($invoice->location->name, [
                '/' => '-',
                '\\' => '-'
            ]);
            $amount = Yii::$app->formatter->asCurrency($invoice->amount ?: 0, 'USD');
            $archive->addFromString("$location, $amount, Invoice #$invoice->id.htm", $this->getContent($invoice));

            $this->onProgressChanged(round(($i + 1) / $total * 100, 1));
        }

        $archive->close();

        $this->onProgressChanged(100, true);
    }

    /**
     * @param $invoice
     * @return string
     */
    protected function getContent($invoice): string
    {
        $view = new View();
        $content = $view->render('//invoices/view', [
            'invoice' => $invoice
        ]);

        $view->params = [
            'title' => 'Invoice #' . $invoice->id,
        ];

        return $view->render('//layouts/main', [
            'content' => $content,
        ]);
    }
}