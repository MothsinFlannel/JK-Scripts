<?php


namespace app\modules\app\scripts\account;


use app\models\ext\User;
use vr\core\Script;

/**
 * Class GetAccountScript
 * @package app\modules\app\scripts\account
 */
class GetAccountScript extends Script
{
    /**
     * @var User|null
     */
    private ?User $_entity;

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
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
        $this->_entity = User::loggedIn();
    }
}