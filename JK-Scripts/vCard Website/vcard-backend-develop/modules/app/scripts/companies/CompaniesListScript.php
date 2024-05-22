<?php


namespace app\modules\app\scripts\companies;


use app\models\CompanyQuery;
use app\models\ext\Company;
use Exception;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 * Class CompaniesListScript
 * @package app\modules\app\companies
 */
class CompaniesListScript extends PagedListScript
{
    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @var CompanyQuery
     */
    private CompanyQuery $_query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'filters',
                NestedValidator::class,
                'rules' => [

                ],
                'objectify' => true,
            ]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array|string[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (Company $company) {
                return $company->toArray();
            })
        ];
    }

    /**
     *
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->_query = Company::find()
            ->filter(@$this->filters->query)
            ->orderBy($this->orderBy)
            ->offset($this->offset)->limit($this->limit);
    }
}