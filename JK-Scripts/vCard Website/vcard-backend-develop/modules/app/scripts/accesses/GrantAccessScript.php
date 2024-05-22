<?php


namespace app\modules\app\scripts\accesses;


use app\components\Script;
use app\models\Access;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;

/**
 * Class GrantAccessScript
 * @package app\modules\app\scripts\accesses
 */
class GrantAccessScript extends Script
{
    /**
     * @var array
     */
    public array $access = [];

    /**
     * @var Access
     */
    private Access $_entity;

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            ['access', 'required'],
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
            'access' => $this->_entity->toArray([], ['user']),
        ];
    }

    /**
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->access = ArrayHelper::filter($this->access, ['referenceId', 'referenceType', 'userId']);

        $this->_entity = Access::findOne($this->access) ?: new Access($this->access);
        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}