<?php


namespace app\modules\api\models;

use yii\web\IdentityInterface;

/**
 * Class Location
 * @package app\modules\api\models
 */
class Location extends \app\models\ext\Location implements IdentityInterface
{
    /**
     * @param int|string $id
     * @return Location|IdentityInterface|null
     */
    public static function findIdentity($id): Location|IdentityInterface|null
    {
        return self::findOne([
            'id' => $id,
            //            'isActive' => true
        ]);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return Location|IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null): Location|IdentityInterface|null
    {
        return self::findOne([
            'serial' => $token,
            //            'isActive' => true
        ]);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey && $this->isActive;
    }

    /**
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->serial;
    }
}