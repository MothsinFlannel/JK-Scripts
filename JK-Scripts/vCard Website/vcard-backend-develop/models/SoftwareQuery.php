<?php

namespace app\models;

use vr\core\ActiveQueryTrait;

/**
 * This is the ActiveQuery class for [[software]].
 *
 * @see Software
 */
class SoftwareQuery extends \yii\db\ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * {@inheritdoc}
     * @return Software[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Software|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
