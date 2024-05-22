<?php


namespace app\modules\app\scripts\users;


use app\models\ext\User;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetUserScript
 * @package app\modules\app\users
 */
class GetUserScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var User|null
     */
    private ?User $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => User::class]
        ];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'user' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = User::findOne($this->id);
    }
}