<?php


namespace app\modules\app\scripts\statements;


use app\models\ext\Company;
use app\models\ext\Invoice;
use app\models\InvoiceQuery;
use Throwable;
use vr\core\ArrayHelper;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetStatementScript
 * @package app\modules\app\scripts\statements
 */
class GetStatementScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $token = null;

    /**
     * @var InvoiceQuery
     */
    private InvoiceQuery $_query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['token', 'required'],
            ['token', ExistValidator::class, 'targetClass' => Company::class],
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
        $results = ArrayHelper::getColumn($this->_query->all(), function (Invoice $invoice) {
            return $invoice->toArray(['*', 'location.name'], [
                'location',
                'unpaidAmount',
            ]);
        });

        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::group(ArrayHelper::index($results, null, 'status'), 'status')
        ];
    }

    /**
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $company = Company::findOne(['token' => $this->token]);
        $this->_query = Invoice::find()
            ->rightJoin([
                'location' => $company->getLocations()
            ], 'location.id = [[invoice.locationId]]')
            ->andWhere('invoice.id is not null')
            ->orderBy('status, until desc');
    }
}