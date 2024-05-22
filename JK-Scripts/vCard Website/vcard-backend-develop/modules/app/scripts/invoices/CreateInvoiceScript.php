<?php

namespace app\modules\app\scripts\invoices;

use app\components\Script;
use app\models\ext\Invoice;
use app\models\ext\InvoiceItem;
use app\models\ext\Terminal;
use app\models\LocationQuery;
use app\modules\api\models\Location;
use Exception;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use vr\core\validators\NestedValidator;
use Yii;
use yii\db\Expression;

/**
 *
 */
class CreateInvoiceScript extends Script
{
    /**
     * @var array
     */
    public array $invoice = [];
    /**
     * @var mixed
     */
    private ?Invoice $_entity = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['invoice', 'required'],
            [
                'invoice',
                NestedValidator::class,
                'rules' => [
                    ['locationId', 'required'],
                    [
                        'locationId',
                        'exist',
                        'targetClass' => Location::class,
                        'targetAttribute' => 'id',
                        'filter' => function (LocationQuery $query) {
                            $query->andWhere([
                                'invoicingMode' => Location::INVOICING_MODE_CUSTOM
                            ]);
                        }
                    ],
                ]
            ]
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
            'invoice' => $this->_entity->toArray([], ['invoiceItems'])
        ];
    }

    /**
     * @return void
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $location = Location::findOne(ArrayHelper::getValue($this->invoice, 'locationId'));

        $this->_entity = new Invoice([
            'amount' => 0,
            'status' => Invoice::STATUS_UNPAID,
            'token' => Yii::$app->security->generateRandomString(64),
            'splitPercent' => $location->splitPercent
        ]);

        $this->_entity->attributes = ArrayHelper::filter($this->invoice, ['since', 'until', 'locationId']);

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }

        $expression = new Expression('COALESCE(terminal. "groupName", "number"::VARCHAR)');
        $terminalQuery = Terminal::find()
            ->select([
                'groupName' => $expression,
                'programName' => '(array_agg(terminal. "programName" ORDER BY terminal. "programName")) [1]'
            ])
            ->andWhere([
                'locationId' => $this->_entity->locationId,
            ])
            ->groupBy($expression)
            ->orderBy($expression);

        foreach ($terminalQuery->all() as $terminal) {
            $invoiceItem = new InvoiceItem([
                'number' => $terminal->groupName,
                'title' => $terminal->programName,
                'type' => InvoiceItem::TYPE_MANUAL,
            ]);

            $this->_entity->link('invoiceItems', $invoiceItem);
        }
    }
}