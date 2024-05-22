<?php


namespace app\models\ext;

use app\components\ReformatTimestampBehavior;
use app\models\ConvolutionQuery;
use Yii;

/**
 * Class Convolution
 * @package app\models\ext
 * @property-read float $revenue
 */
class Convolution extends \app\models\Convolution
{
    /**
     * {@inheritdoc}
     * @return ConvolutionQuery the active query used by this AR class.
     */
    public static function find(): ConvolutionQuery
    {
        $query = new ConvolutionQuery(get_called_class());

        if (!Yii::$app->user->isGuest) {
            $query->andWhere([
                'convolution.isLive' => User::loggedIn()->isLive
            ]);
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
                'attributes' => ['lastLogAt']
            ]
        ];
    }

    /**
     * @return float
     */
    public function getRevenue(): float
    {
        return $this->moneyIn - $this->moneyOut;
    }
}