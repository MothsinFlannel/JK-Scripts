<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Padlock]].
 *
 * @see Padlock
 */
class PadlockQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Padlock[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Padlock|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
