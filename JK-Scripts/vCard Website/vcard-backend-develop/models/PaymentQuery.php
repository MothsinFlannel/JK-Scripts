<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Payment]].
 *
 * @see Payment
 */
class PaymentQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * {@inheritdoc}
     * @return Payment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Payment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $until
     * @return self
     */
    public function until($until)
    {
        return $this->andFilterCompare('date([[createdAt]])', $until, '<=');
    }

}
