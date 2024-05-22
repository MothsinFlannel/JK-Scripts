<?php


namespace app\modules\api\scripts\account;


use app\components\Script;
use app\models\LocationQuery;
use app\modules\api\models\Location;
use vr\core\utils\HttpCode;
use vr\core\validators\ExistValidator;
use Yii;

/**
 * Class LoginScript
 * @package app\modules\api\scripts\account
 */
class LoginScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $serial = null;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['serial', 'required'],
            [
                'serial',
                ExistValidator::class,
                'targetClass' => Location::class,
                'targetAttribute' => 'serial',
                'statusCode' => HttpCode::FORBIDDEN,
                'message' => 'Invalid serial number'
            ],
            [
                'serial',
                ExistValidator::class,
                'targetClass' => Location::class,
                'targetAttribute' => 'serial',
                'statusCode' => HttpCode::FORBIDDEN,
                'filter' => function (LocationQuery $query) {
                    return $query->active();
                },
                'message' => 'Location is inactive'
            ],
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $location = Location::findOne([
            'serial' => $this->serial
        ]);

        Yii::$app->user->login($location);
    }
}