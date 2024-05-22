<?php

namespace app\models;

use vr\core\ActiveQueryTrait;

/**
 * This is the ActiveQuery class for [[Installation]].
 *
 * @see Installation
 */
class InstallationQuery extends \yii\db\ActiveQuery
{
    use ActiveQueryTrait;

    /**
     * {@inheritdoc}
     * @return Installation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Installation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
