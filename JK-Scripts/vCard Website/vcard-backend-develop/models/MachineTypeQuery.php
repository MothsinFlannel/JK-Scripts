<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[MachineType]].
 *
 * @see MachineType
 */
class MachineTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return MachineType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return MachineType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
