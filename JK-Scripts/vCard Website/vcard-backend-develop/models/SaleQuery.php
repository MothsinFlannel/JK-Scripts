<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Sale]].
 *
 * @see Sale
 */
class SaleQuery extends \yii\db\ActiveQuery
{
    /**
     * @param $since
     * @return self
     */
    public function since($since)
    {
        return $this->andFilterCompare('[[sale.createdAt]]', $since, '>=');
    }

    /**
     * @param $until
     * @return self
     */
    public function until($until)
    {
        return $this->andFilterCompare('[[sale.createdAt]]', $until, '<=');
    }

    /**
     * {@inheritdoc}
     * @return Sale[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Sale|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
