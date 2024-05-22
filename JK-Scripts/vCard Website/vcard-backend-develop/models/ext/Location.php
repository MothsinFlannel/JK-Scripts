<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;
use app\models\AttachmentQuery;
use app\models\Category;
use app\models\CategoryQuery;
use app\models\ConvolutionQuery;
use app\models\InvoiceQuery;
use app\models\LocationQuery;
use app\models\RouteQuery;
use app\models\Staff;
use app\models\TerminalQuery;
use app\models\UserQuery;
use Closure;
use DateInterval;
use DateTime;
use Exception;
use Throwable;
use vr\core\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class Location
 * @package app\models\ext
 * @property float $moneyIn
 * @property float $moneyOut
 * @property float $percentageProfit
 * @property float $revenue
 * @property float $paid
 * @property float $profit
 *
 * @property Category[] $categories
 * @property Convolution[] $convolutions
 * @property User[] $users
 * @property Terminal[] $terminals
 * @property Route $route
 */
class Location extends \app\models\Location
{
    /**
     *
     */
    const SERIAL_LENGTH = 16;

    /**
     *
     */
    const INVOICING_MODE_AUTOMATIC = 'automatic';

    /**
     *
     */
    const INVOICING_MODE_CUSTOM = 'custom';

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt', 'lastActivityAt']
            ]
        ];
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getOperatorEmail(): ?string
    {
        return User::find()
            ->select('email')
            ->rightJoin([
                'staff' => Staff::find()->andWhere(['locationId' => $this->id])
            ], '[[staff.userId]] = [[user.id]]')
            ->scalar();
    }

    /**
     * {@inheritdoc}
     * @return LocationQuery the active query used by this AR class.
     * @throws Throwable
     */
    public static function find(): LocationQuery
    {
        $query = new LocationQuery(get_called_class());

        if ($user = User::loggedIn()) {
            $conditions = ['or'];

            if (!Yii::$app->user->can('app/locations/list+all')) {
                $states       = $user->operatesInStates;
                $conditions[] = ['state' => $states];

                $uniq = uniqid() . 'staff';
                $query->leftJoin([
                    $uniq => $user->getStaff()
                ], "[[$uniq.locationId]] = location.id");

                $conditions[] = "[[$uniq.locationId]] is not null";
            }

            if (!Yii::$app->user->can('app/locations/list+test')) {
                $conditions = [
                    'and',
                    ['isLive' => true],
                    $conditions
                ];
            }

            $query->andWhere($conditions);
        }

        $query->andFilterWhere([
            'location.companyId' => ArrayHelper::getValue(User::loggedIn(), 'companyId'),
        ]);

        return $query;
    }

    /**
     * @return array|Closure[]
     */
    public function extraFields(): array
    {
        return [
            'moneyIn',
            'moneyOut',
            'percentageProfit',
            'revenue',
            'flatFee',
            'profit',
            'paid',
            'due',
            'users',
            'operatorEmail',
            'route',
            'attachments'
        ];
    }

    /**
     * @return float
     * @throws Throwable
     */
    public function getDue(): float
    {
        return User::loggedIn()->isLive ? round($this->getInvoices()->sum('[[amount]]') - $this->getPaid(), 2) : 0;
    }

    /**
     * @return InvoiceQuery
     */
    public function getInvoices(): InvoiceQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasMany(Invoice::class, ['locationId' => 'id']);
    }

    /**
     * @return float
     * @throws Throwable
     */
    public function getPaid(): float
    {
        return User::loggedIn()->isLive ? round(Payment::find()
            ->rightJoin([
                'invoice' => $this->getInvoices()
            ], '[[payment.invoiceId]] = invoice.id')
            ->sum('[[payment.amount]]'), 2) : 0;
    }

    /**
     * @return float
     */
    public function getProfit(): float
    {
        return $this->percentageProfit + $this->flatFee;
    }

    /**
     * @return float
     */
    public function getRevenue(): float
    {
        return $this->moneyIn - $this->moneyOut;
    }

    /**
     * @return float
     */
    public function getMoneyIn(): float
    {
        return $this->getSum('moneyIn') ?: 0;
    }

    /**
     * @param string $attribute
     * @return float
     */
    protected function getSum(string $attribute): float
    {
        return round(Convolution::find()
            ->andWhere(['locationId' => $this->id])
            ->sum("[[$attribute]]"), 2);
    }

    /**
     * @return float
     */
    public function getMoneyOut(): float
    {
        return $this->getSum('moneyOut') ?: 0;
    }

    /**
     * @return float
     */
    public function getPercentageProfit(): float
    {
        return $this->getSum('percentageProfit');
    }

    /**
     * @return float
     */
    public function getFlatFee(): float
    {
        return $this->getSum('flatFee');
    }

    /**
     * @param null $on
     * @return array
     * @throws Exception
     */
    public function getFiscalInterval($on = null): array
    {
        $on = new DateTime($on);

        // Check if is it not Monday already
        if ($on->format('N') != 1) {
            $on->modify('last monday');
        }

        return [
            $on->format('Y-m-d'),
            $on->add(DateInterval::createFromDateString('+6 days'))->format('Y-m-d')
        ];
    }

    /**
     * @return AttachmentQuery
     */
    public function getAttachments(): AttachmentQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this
            ->hasMany(Attachment::class, ['referenceId' => 'id'])
            ->andWhere([
                'referenceType' => $this::tableName(),
            ]);
    }

    /**
     * @return ConvolutionQuery
     */
    public function getConvolutions(): ConvolutionQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasMany(Convolution::class, ['locationId' => 'id']);
    }

    /**
     * @return TerminalQuery
     */
    public function getTerminals(): TerminalQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasMany(Terminal::class, ['locationId' => 'id']);
    }

    /**
     * @return CategoryQuery
     */
    public function getCategories(): CategoryQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasMany(Category::class, ['locationId' => 'id']);
    }

    /**
     * @return UserQuery
     * @throws InvalidConfigException
     */
    public function getUsers(): UserQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasMany(User::class, ['id' => 'userId'])->viaTable('staff', ['locationId' => 'id']);
    }

    /**
     * @return RouteQuery
     */
    public function getRoute(): RouteQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->hasOne(Route::class, ['id' => 'routeId']);
    }
}