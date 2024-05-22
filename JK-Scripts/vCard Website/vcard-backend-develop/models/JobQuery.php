<?php

namespace app\models;

use vr\core\ActiveQueryTrait;

/**
 * This is the ActiveQuery class for [[Job]].
 *
 * @see Job
 */
class JobQuery extends \yii\db\ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * {@inheritdoc}
     * @return Job[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ext\Job|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
