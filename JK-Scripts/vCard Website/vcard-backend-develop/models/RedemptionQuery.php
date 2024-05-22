<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Redemption]].
 *
 * @see Redemption
 */
class RedemptionQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * @param $since
     * @return RedemptionQuery
     */
    public function since($since)
    {
        return $this->andFilterCompare('date([[createdAt]])', $since, '>=');
    }

    /**
     * @param $until
     * @return RedemptionQuery
     */
    public function until($until)
    {
        return $this->andFilterCompare('date([[createdAt]])', $until, '<=');
    }

    /**
     * {@inheritdoc}
     * @return Redemption[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Redemption|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
