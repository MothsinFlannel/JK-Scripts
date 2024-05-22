<?php

namespace app\models;

use vr\core\ActiveQueryTrait;

/**
 * This is the ActiveQuery class for [[Log]].
 *
 * @see Log
 */
class LogQuery extends \yii\db\ActiveQuery
{
    use ActiveQueryTrait;

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Log[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Log|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
