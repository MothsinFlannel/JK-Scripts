<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @return self
     */
    public function admin(): self
    {
        return $this->andWhere(['role' => ext\User::ROLE_ADMIN]);
    }

    /**
     * @return self
     */
    public function routeman(): self
    {
        return $this->andWhere(['role' => ext\User::ROLE_ROUTEMAN]);
    }

    /**
     * @param $query
     * @return $this
     */
    public function filter($query)
    {
        if (empty($query)) {
            return $this;
        }

        if (is_string($query)) {
            $query = explode(' ', $query);
        }

        return $this->andWhere([
            'or',
            ['like', 'email', $query],
            ['like', 'fullName', $query],
            ['like', 'phone', $query],
        ]);
    }

    /**
     * @param bool $active
     * @return self
     */
    public function active($active = true)
    {
        return $this->andWhere([
            'isActive' => $active
        ]);
    }

    /**
     * {@inheritdoc}
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
