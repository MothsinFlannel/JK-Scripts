<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;
use app\models\PaymentQuery;
use Throwable;
use Yii;

/**
 * Class Payment
 * @package app\models\ext
 */
class Payment extends \app\models\Payment
{
    /**
     * {@inheritdoc}
     * @return PaymentQuery the active query used by this AR class.
     * @throws Throwable
     */
    public static function find(): PaymentQuery
    {
        $query = new PaymentQuery(get_called_class());

        if (Yii::$app->user->getIdentity() && !User::loggedIn()->isLive) {
            $query->andWhere('1 = 0');
        }

        return $query;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class'      => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }
}