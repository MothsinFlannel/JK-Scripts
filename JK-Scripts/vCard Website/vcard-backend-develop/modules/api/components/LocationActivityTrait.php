<?php


namespace app\modules\api\components;


use app\models\ext\Location;
use Throwable;
use vr\core\ErrorsException;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

/**
 * Trait LocationActivityTrait
 * @package app\modules\api\components
 */
trait LocationActivityTrait
{
    /**
     * @param $identity
     * @throws ErrorsException
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function refreshLastActivity($identity): void
    {
        $location = Location::find()
            ->andWhere(['serial' => $identity])->forUpdate()->one();

        if (!$location) {
            throw new NotFoundHttpException('Location not found');
        }

        $location->lastActivityAt = new Expression('now()');
        if (!$location->save(false, ['lastActivityAt'])) {
            throw new ErrorsException($location->errors);
        }
    }
}