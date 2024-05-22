<?php


namespace app\modules\app\scripts\companies;


use app\components\Script;
use app\models\ext\Company;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;

/**
 * Class EditCompanyScript
 * @package app\modules\app\companies
 */
class EditCompanyScript extends Script
{
    /**
     * @var array
     */
    public array $company = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

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
            ['company', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Company::class]
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
            'company' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Company::findOne($this->id) ?: new Company();
        $this->_entity->attributes = $this->company;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}