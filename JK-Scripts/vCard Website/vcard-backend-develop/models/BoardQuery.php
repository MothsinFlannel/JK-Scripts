<?php

namespace app\models;

use vr\core\ActiveQueryTrait;

/**
 * This is the ActiveQuery class for [[Board]].
 *
 * @see Board
 */
class BoardQuery extends \yii\db\ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * {@inheritdoc}
     * @return Board[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Board|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
