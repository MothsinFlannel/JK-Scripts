<?php

namespace app\jobs\invoices;

use app\models\ext\Job;
use app\models\ext\User;
use app\models\InvoiceQuery;
use app\scripts\invoices\InvoicesListTrait;
use ArrayObject;
use DateTime;
use Exception;
use Throwable;
use vr\core\ErrorsException;
use Yii;
use yii\base\Component;
use yii\db\Connection;
use yii\db\Expression;
use yii\queue\JobInterface;

/**
 *
 */
abstract class BaseExportJob extends Component implements JobInterface
{
    use InvoicesListTrait;

    /**
     *
     */
    const JOB_CATEGORY = 'app/invoices/export';

    /**
     *
     */
    const EXPORT_FOLDER = 'uploads/export';

    /**
     *
     */
    const ROOT_EXPORT_FOLDER = '@webroot/' . self::EXPORT_FOLDER;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var int
     */
    private int $initiatorId;
    /**
     * @var Job
     */
    private Job $job;

    /**
     * @var string
     */
    private ?string $filename = null;

    /**
     * @param int $initiatorId
     * @param array $config
     */
    public function __construct(int $initiatorId, array $config = [])
    {
        $this->initiatorId = $initiatorId;
        parent::__construct($config);
    }

    /**
     * @param $queue
     * @return void
     * @throws ErrorsException
     * @throws Throwable
     */
    public function execute($queue): void
    {
        $this->checkFolder();

        $this->job = new Job([
            'initiatorId' => $this->initiatorId,
            'category' => self::JOB_CATEGORY,
            'title' => $this->getTitle(),
            'state' => Job::STATUS_PENDING,
        ]);

        if (!$this->job->save()) {
            throw new ErrorsException($this->job->errors);
        }

        $filename = $this->getFilename();
        $fullPath = Yii::getAlias(self::ROOT_EXPORT_FOLDER) . '/' . $filename;

        try {
            $query = $this->createQuery(User::findOne($this->initiatorId), $this->filters);
            $this->onExecute($query);

            $this->finishJob($filename);
        } catch (Throwable $throwable) {
            Yii::error($throwable->getMessage());

            $this->job->state = Job::STATE_FAILED;
            $this->job->output = $throwable->getMessage();
            $this->job->endedAt = new Expression('now()');

            if (!$this->job->save() || !$this->job->refresh()) {
                throw new ErrorsException($this->job->errors);
            }

            try {
                unlink($fullPath);
            } catch (Throwable) {
            }
        }
    }

    /**
     * @return void
     */
    private function checkFolder(): void
    {
        $folder = Yii::getAlias(self::ROOT_EXPORT_FOLDER);
        if (!file_exists($folder)) {
            mkdir($folder, 777, true);
        }
    }

    /**
     * @return string
     */
    abstract protected function getTitle(): string;

    /**
     * @return string
     */
    private function getFilename(): string
    {
        return $this->filename = $this->filename ?? (new DateTime())->format('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $this->getOutputType();
    }

    /**
     * @return string
     */
    abstract protected function getOutputType(): string;

    /**
     * @param InvoiceQuery $query
     * @return void
     */
    abstract protected function onExecute(InvoiceQuery $query): void;

    /**
     * @param string $filename
     * @return void
     * @throws ErrorsException
     */
    private function finishJob(string $filename): void
    {
        $this->job->output = trim(self::EXPORT_FOLDER . '/' . $filename, '/');
        $this->job->state = Job::STATE_SUCCEEDED;
        $this->job->endedAt = new Expression('now()');

        if (!$this->job->save() || !$this->job->refresh()) {
            throw new ErrorsException($this->job->errors);
        }
    }

    /**
     * @return string
     */
    protected function getFilePath(): string
    {
        return Yii::getAlias(self::ROOT_EXPORT_FOLDER) . '/' . $this->getFilename();
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getIntervalTitle(): string
    {
        $filters = new ArrayObject($this->filters, ArrayObject::ARRAY_AS_PROPS);
        $since = @$filters->since ? (new DateTime($filters->since))->format('m/d/Y') : ' –';
        $until = @$filters->until ? (new DateTime($filters->until))->format('m/d/Y') : '– ';

        return implode(' ', array_filter([$since, 'THRU', $until]));
    }

    /**
     * @param float $progress
     * @param bool $finished
     * @return void
     * @throws Throwable
     */
    protected function onProgressChanged(float $progress, bool $finished = false): void
    {
        Yii::$app->db->transaction(function (Connection $connection) use ($progress, $finished) {
            $this->job->progress = (int)$progress;
            $this->job->state = Job::STATUS_IN_PROGRESS;

            if (!$this->job->save() || !$this->job->refresh()) {
                throw new ErrorsException($this->job->errors);
            }

            $connection->transaction->commit();
        });
    }
}