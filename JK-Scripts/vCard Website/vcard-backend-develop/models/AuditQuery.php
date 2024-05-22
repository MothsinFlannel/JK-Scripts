<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Audit]].
 *
 * @see Audit
 */
class AuditQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Audit[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Audit|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
