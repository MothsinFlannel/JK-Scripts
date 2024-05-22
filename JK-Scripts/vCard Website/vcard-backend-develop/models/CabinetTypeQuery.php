<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[CabinetType]].
 *
 * @see CabinetType
 */
class CabinetTypeQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @param bool $active
     * @return CabinetTypeQuery
     */
    public function active($active = true)
    {
        return $this->andWhere([
            'isActive' => $active
        ]);
    }

    /**
     * {@inheritdoc}
     * @return CabinetType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CabinetType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
