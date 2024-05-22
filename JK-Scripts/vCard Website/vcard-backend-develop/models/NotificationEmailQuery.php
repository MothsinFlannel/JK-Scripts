<?php

namespace app\models;

use vr\core\ActiveQueryTrait;

/**
 * This is the ActiveQuery class for [[NotificationEmail]].
 *
 * @see NotificationEmail
 */
class NotificationEmailQuery extends \yii\db\ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * {@inheritdoc}
     * @return NotificationEmail[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NotificationEmail|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
