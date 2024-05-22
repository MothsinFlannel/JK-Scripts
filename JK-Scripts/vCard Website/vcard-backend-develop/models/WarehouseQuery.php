<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Warehouse]].
 *
 * @see Warehouse
 */
class WarehouseQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @param string|null $query
     * @return $this
     */
    public function filter(?string $query): self
    {
        if (empty($query)) {
            return $this;
        }

        return $this->andWhere([
            'ilike',
            'warehouse.name',
            explode(' ', $query)
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Warehouse[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Warehouse|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
