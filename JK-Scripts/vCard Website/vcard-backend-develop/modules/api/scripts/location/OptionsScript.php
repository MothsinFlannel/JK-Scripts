<?php


namespace app\modules\api\scripts\location;


use app\components\Script;
use app\models\Clerk;
use app\modules\api\models\Location;
use vr\core\ArrayHelper;
use Yii;

/**
 * Class OptionsScript
 * @package app\modules\api\scripts\location
 */
class OptionsScript extends Script
{
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        /** @var Location $location */
        $location = Yii::$app->user->identity;

        return [
            'categories' => ArrayHelper::getColumn($location->categories, 'name') ?: [],
            'clerks' => ArrayHelper::getColumn($location->clerks, function (Clerk $clerk) {
                return [
                    'manager' => $clerk->isManager,
                    'name' => $clerk->fullName,
                    'pin' => $clerk->pin,
                ];
            }) ?: [],
            'locationName' => $location->name,
            'locationAddress' => $location->address,
            'locationCity' => $location->city,
            'locationState' => $location->state,
            'locationZip' => $location->zipCode,
            'locationPhone' => $location->contactPhone,
            'maxAddCreditsAmount' => $location->maxAddCreditsAmount * 100, // Convert to cents
            'maxTerminalAddress' => $location->maxTerminalNumber,
            'creditingEnabled' => $location->enableAddCredits,
            'redemptionEnabled' => $location->enableRedemption,
            'replayEnabled' => $location->enableCreditsReplay,
            'disableScreenLock' => $location->disableScreenLock,
            'disablePrinting' => $location->disablePrinting,
            'timezone' => $location->timezone,
        ];
    }

    protected function onExecute()
    {

    }
}