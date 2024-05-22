<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;
use app\models\UserQuery;

/**
 * Class Access
 * @package app\models\ext
 *
 * @property User $user
 */
class Access extends \app\models\Access
{
    /**
     * @return string[]
     */
    public function extraFields(): array
    {
        return [
            'user'
        ];
    }

    /**
     * @return UserQuery
     */
    public function getUser(): UserQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class'      => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }
}