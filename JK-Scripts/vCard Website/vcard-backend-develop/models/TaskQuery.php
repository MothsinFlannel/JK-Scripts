<?php

namespace app\models;

use vr\core\ActiveQueryTrait;

/**
 * This is the ActiveQuery class for [[Task]].
 *
 * @see Task
 */
class TaskQuery extends \yii\db\ActiveQuery
{
    use ActiveQueryTrait;
    
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Task[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Task|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
