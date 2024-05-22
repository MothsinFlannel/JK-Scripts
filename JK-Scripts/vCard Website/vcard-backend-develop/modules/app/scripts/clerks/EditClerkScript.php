<?php


namespace app\modules\app\scripts\clerks;


use app\components\Script;
use app\models\Clerk;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;

/**
 * Class EditClerkScript
 * @package app\modules\app\clerks
 */
class EditClerkScript extends Script
{
    /**
     * @var array
     */
    public array $clerk = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var Clerk|null
     */
    private ?Clerk $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['clerk', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Clerk::class]
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
            'clerk' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = Clerk::findOne($this->id) ?: new Clerk();
        $this->_entity->attributes = $this->clerk;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}