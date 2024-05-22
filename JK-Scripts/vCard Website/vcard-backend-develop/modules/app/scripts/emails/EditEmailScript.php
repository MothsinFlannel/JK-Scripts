<?php


namespace app\modules\app\scripts\emails;


use app\components\Script;
use app\models\NotificationEmail;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;
use vr\core\validators\NestedValidator;

/**
 * Class EditEmailScript
 * @package app\modules\app\emails
 */
class EditEmailScript extends Script
{
    /**
     * @var array
     */
    public array $email = [];

    /**
     * @var int|null
     */
    public ?int $id = null;

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
            ['email', 'required'],
            ['id', ExistValidator::class, 'targetClass' => NotificationEmail::class],
            [
                'email',
                NestedValidator::class,
                'rules' => [
                    ['email', 'required'],
                    ['email', 'email']
                ]
            ]
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
     * @throws ErrorsException
     */
    protected function onExecute(): void
    {
        $this->_entity = NotificationEmail::findOne($this->id) ?: new NotificationEmail();
        $this->_entity->attributes = $this->email;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}