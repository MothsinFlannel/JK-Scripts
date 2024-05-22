<?php


namespace app\modules\app\scripts\companies;


use app\models\ext\Company;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetCompanyScript
 * @package app\modules\app\companies
 */
class GetCompanyScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var Company|null
     */
    private ?Company $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Company::class]
        ];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'company' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Company::findOne($this->id);
    }
}