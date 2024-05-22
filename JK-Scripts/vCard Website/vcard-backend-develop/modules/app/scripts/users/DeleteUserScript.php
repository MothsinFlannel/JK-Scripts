<?php


namespace app\modules\app\scripts\users;


use app\components\Script;
use app\models\ext\User;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;
use yii\validators\CompareValidator;

/**
 * Class DeleteUserScript
 * @package app\modules\app\users
 */
class DeleteUserScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => User::class],
            [
                'id',
                'compare',
                'compareValue' => User::loggedIn()->id,
                'operator' => '!=',
                'type' => CompareValidator::TYPE_NUMBER,
                'message' => 'You cannot delete your own account'
            ]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        User::findOne($this->id)->delete();
    }
}