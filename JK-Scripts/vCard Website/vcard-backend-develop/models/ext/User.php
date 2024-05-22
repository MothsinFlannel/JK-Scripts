<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;
use app\models\CompanyQuery;
use Closure;
use DateInterval;
use DateTime;
use Exception;
use vr\core\ArrayHelper;
use vr\rbac\IRolesHolderInterface;
use Yii;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * Class User
 * @package app\models\ext
 * @property-read bool $isLive
 * @property Company $company
 */
class User extends \app\models\User implements IdentityInterface, IRolesHolderInterface
{
    /**
     *
     */
    const ROLE_ADMIN = 'admin';

    /**
     *
     */
    const ROLE_ROUTEMAN = 'routeman';

    /**
     *
     */
    const ROLE_LOCATION = 'location';

    /**
     *
     */
    const ROLE_TECHNICIAN = 'technician';

    /**
     * @return self|null
     */
    public static function loggedIn(): ?self
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::$app->user->identity;
    }

    /**
     * @param int|string $id
     * @return self|null
     */
    public static function findIdentity($id): self|null
    {
        return User::findOne([
            'id' => $id,
            'isActive' => true,
        ]);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return self|null
     * @throws UnauthorizedHttpException
     * @throws Exception
     */
    public static function findIdentityByAccessToken($token, $type = null): self|null
    {
        return User::findOne([
            'accessToken' => $token,
            'isActive' => true,
        ]);
    }

    /**
     * @return array|Closure[]
     */
    public function extraFields(): array
    {
        return [
            'accessToken',
            'locationsCount' => function () {
                return $this->getStaff()->count();
            },
            'company'
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        if (!YII_DEBUG) {
            ArrayHelper::removeValue($fields, 'accessToken');
        }
        ArrayHelper::removeValue($fields, 'password');

        return $fields;
    }

    /**
     * @return array|array[]
     */
    public function scenarios(): array
    {
        $attributes = parent::scenarios()[self::SCENARIO_DEFAULT];

        ArrayHelper::removeValue($attributes, 'id');
        ArrayHelper::removeValue($attributes, 'password');
        ArrayHelper::removeValue($attributes, 'accessToken');

        return [
            self::SCENARIO_DEFAULT => $attributes
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($this->isAttributeChanged('password')) {
            $expiry = ArrayHelper::getValue(Yii::$app->params, 'passwordExpiresIn');

            $this->recoveryToken     = null;
            $this->passwordExpiresAt = (new DateTime())
                ->add(DateInterval::createFromDateString($expiry))
                ->format('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
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
        return $this->accessToken;
    }

    /**
     * @return array|null
     */
    public function roles(): ?array
    {
        return [$this->role];
    }

    /**
     * @return bool
     */
    public function getIsLive(): bool
    {
        return $this->role !== self::ROLE_TECHNICIAN;
    }

    /**
     * @return CompanyQuery
     */
    public function getCompany(): CompanyQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasOne(Company::class, ['id' => 'companyId']);
    }
}