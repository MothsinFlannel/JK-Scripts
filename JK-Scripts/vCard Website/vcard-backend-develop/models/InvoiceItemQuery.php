<?php

namespace app\models;

use vr\core\ActiveQueryTrait;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[InvoiceItem]].
 *
 * @see InvoiceItem
 */
class InvoiceItemQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return InvoiceItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return InvoiceItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
