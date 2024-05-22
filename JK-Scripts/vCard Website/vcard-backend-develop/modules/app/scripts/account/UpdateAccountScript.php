<?php


namespace app\modules\app\scripts\account;


use app\components\Script;
use app\models\ext\User;
use Exception;
use vr\core\ArrayHelper;
use vr\core\ErrorsException;
use Yii;

/**
 * Class UpdateAccountScript
 * @package app\modules\app\scripts\account
 */
class UpdateAccountScript extends Script
{
    /**
     * @var array|null
     */
    public ?array $user = null;

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
            'user' => $this->_entity->toArray()
        ];
    }

    /**
     * @throws ErrorsException
     * @throws Exception
     */
    protected function onExecute(): void
    {
        $this->_entity = User::loggedIn();
        $this->_entity->attributes = ArrayHelper::filter($this->user, ['fullName', 'phone']);

        if ($password = ArrayHelper::getValue($this->user, 'password')) {
            $this->_entity->password = Yii::$app->security->generatePasswordHash($password);
        }

        if (!$this->_entity->save() || !$this->_entity->refresh()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}