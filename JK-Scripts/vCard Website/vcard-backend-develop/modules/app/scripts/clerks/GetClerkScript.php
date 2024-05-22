<?php


namespace app\modules\app\scripts\clerks;


use app\models\Clerk;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetClerkScript
 * @package app\modules\app\clerks
 */
class GetClerkScript extends Script
{
    /**
     * @var int
     */
    public int $id;

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
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Clerk::class]
        ];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'clerk' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = Clerk::findOne($this->id);
    }
}