<?php


namespace app\modules\app\scripts\emails;


use app\models\NotificationEmail;
use vr\core\Script;
use vr\core\validators\ExistValidator;

/**
 * Class GetEmailScript
 * @package app\modules\app\emails
 */
class GetEmailScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var NotificationEmail|null
     */
    private ?NotificationEmail $_entity;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => NotificationEmail::class]
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
            'email' => $this->_entity->toArray(),
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_entity = NotificationEmail::findOne($this->id);
    }
}