<?php


namespace app\modules\app\scripts\invoices;

use app\jobs\invoices\ExcelExportJob;
use app\jobs\invoices\HtmlExportJob;
use app\models\ext\User;
use Throwable;
use vr\core\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\queue\Queue;

/**
 * Class ExportInvoicesScript
 * @package app\modules\app\scripts\invoices
 */
class ExportInvoicesScript extends InvoicesListScript
{
    /**
     *
     */
    const DEFAULT_FORMAT = self::FORMAT_HTML;

    /**
     *
     */
    const FORMAT_HTML = 'html';

    /**
     *
     */
    const FORMAT_XLSX = 'xlsx';

    /**
     * @var string|null
     */
    public ?string $format = self::DEFAULT_FORMAT;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['format', 'required'],
            ['format', 'in', 'range' => [self::FORMAT_HTML, self::FORMAT_XLSX]],
        ]);
    }

    /**
     * @throws Throwable
     * @throws InvalidConfigException
     */
    protected function onExecute(): void
    {
        parent::onExecute();

        $class = ArrayHelper::getValue([
            self::FORMAT_HTML => HtmlExportJob::class,
            self::FORMAT_XLSX => ExcelExportJob::class,
        ], $this->format);

        /** @var Queue $queue */
        $queue = Yii::$app->get('queue');

        $queue->push(Yii::createObject($class, [
            User::loggedIn()->id,
            [
                'filters' => $this->filters
            ]
        ]));
    }
}