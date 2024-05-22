<?php


namespace app\scripts\invoices;


use app\components\Script;
use app\models\ext\Convolution;
use app\models\ext\Invoice;
use app\models\ext\Location;
use DateInterval;
use DateTime;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use Yii;
use yii\base\Exception;

/**
 * Class IssueInvoiceScript
 * @package app\scripts\invoices
 * @property-read Invoice $entity
 */
class IssueInvoiceScript extends Script
{
    use InvoiceItemsTrait;

    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @var string|null
     */
    public ?string $since = null;

    /**
     * @var string|null
     */
    public ?string $until = null;

    /**
     * @var Invoice|null
     */
    private ?Invoice $_entity;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['locationId', 'required'],
            ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
            //            [['since', 'until'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'invoice' => $this->_entity?->toArray([], ['location']),
        ];
    }

    /**
     * @return Invoice|null
     */
    public function getEntity(): ?Invoice
    {
        return $this->_entity;
    }

    /**
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute()
    {
        $location = Location::findOne($this->locationId);
        $amount = round($this->calculateAmount() ?? 0, 2);

        $saturday = new DateTime('last Sunday');
        if (!$this->since) {
            $this->since = $saturday->format('Y-m-d');
        }

        if (!$this->until) {
            $this->until = $saturday->sub(DateInterval::createFromDateString('-6 days'))->format('Y-m-d');
        }

        $this->_entity = new Invoice();
        $this->_entity->locationId = $this->locationId;
        $this->_entity->since = $this->since;
        $this->_entity->until = $this->until;
        $this->_entity->status = Invoice::STATUS_UNPAID;
        $this->_entity->token = Yii::$app->security->generateRandomString(64);
        $this->_entity->amount = $amount;
        $this->_entity->splitPercent = $location->splitPercent;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }

        $this->refreshInvoiceItems($this->_entity, true);
    }

    /**
     * @return float|null
     */
    private function calculateAmount(): ?float
    {
        return Convolution::find()
            ->live()
            ->andWhere(['locationId' => $this->locationId])
            ->since($this->since)
            ->until($this->until)
            ->sum('[[percentageProfit]] + [[flatFee]]');
    }
}