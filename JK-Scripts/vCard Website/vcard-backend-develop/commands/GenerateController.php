<?php


namespace app\commands;


use app\models\CabinetType;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\ext\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Faker\Factory;
use Throwable;
use vr\core\ErrorsException;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class GenerateController
 * @package app\commands
 */
class GenerateController extends Controller
{
    /**
     * @throws ErrorsException
     * @throws Exception
     * @throws Throwable
     */
    public function actionIndex()
    {
        $this->actionUsers();
        $this->actionLocations();
        $this->actionTerminals();
        $this->actionConvolutions('-7 days', 'today');
    }

    /**
     * @param int $count
     * @throws ErrorsException
     * @throws Exception
     */
    public function actionUsers(int $count = 20)
    {
        Console::startProgress(0, $count, 'Generating users');
        $faker = Factory::create();

        $hash = Yii::$app->security->generatePasswordHash('password');

        foreach (range(1, $count) as $i) {
            $name = $faker->name;
            Console::updateProgress($i, $count, 'Generating ' . $name);

            $user = new User([
                'accessToken' => Yii::$app->security->generateRandomString(),
                'email' => $faker->safeEmail,
                'fullName' => $name,
                'phone' => $faker->phoneNumber,
                'role' => $faker->randomElement([User::ROLE_ADMIN, User::ROLE_ROUTEMAN]),
                'isActive' => $faker->boolean(80),
                'createdAt' => $faker->dateTimeBetween('-1 year', null)->format('Y-m-d H:i:s'),
                'password' => $hash
            ]);

            if (!$user->save()) {
                throw new ErrorsException($user->errors);
            }
        }

        Console::endProgress();
    }

    /**
     * @param int $count
     * @throws ErrorsException
     */
    public function actionLocations(int $count = 20)
    {
        Console::startProgress(0, $count, 'Generating locations');
        $faker = Factory::create();

        foreach (range(1, $count) as $i) {
            $name = 'Location #' . $i;
            Console::updateProgress($i, $count, 'Generating ' . $name);

            $user = User::find()->orderBy('random()')->one();

            /** @noinspection PhpUndefinedFieldInspection */
            $location = new Location([
                'name' => $name,
                'contactPhone' => $faker->phoneNumber,
                'timezone' => $faker->timezone,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => strtoupper($faker->state),
                'zipCode' => $faker->postcode,
                'splitPercent' => $faker->numberBetween(1, 10),
                'flatFee' => 0,
                'isActive' => 1,
                'serial' => (string)$faker->randomNumber(8),
                'maxOfflineHours' => $faker->numberBetween(1, 3),
                'maxAddCreditsAmount' => 0,
                'enableAddCredits' => $faker->boolean(80),
                'enableRedemption' => $faker->boolean(80),
                'invoicingMode' => Location::INVOICING_MODE_AUTOMATIC,
                'enableCreditsReplay' => $faker->boolean(80),
                'lastActivityAt' => $faker->dateTimeBetween('-10 days', null)->format('Y-m-d H:i:s'),
                'createdAt' => $faker->dateTimeBetween('-1 year', null)->format('Y-m-d H:i:s'),
            ]);

            if (!$location->save()) {
                throw new ErrorsException($location->errors);
            }

            $location->link('users', $user);
        }

        Console::endProgress();
    }

    /**
     * @param int $count
     * @throws ErrorsException
     * @throws Throwable
     */
    public function actionTerminals(int $count = 100)
    {
        Console::startProgress(0, $count, 'Generating terminals');
        $faker = Factory::create();

        foreach (range(1, $count) as $i) {
            $name = 'Terminal #' . $i;
            Console::updateProgress($i, $count, 'Generating ' . $name);

            $location = Location::find()->orderBy('random()')->one();
            $cabinetType = CabinetType::find()->orderBy('random()')->one();

            do {
                $number = $faker->numberBetween(1, 255);
            } while (Terminal::findOne([
                'number' => $number,
                'locationId' => $location->id,
            ]));

            $terminal = new Terminal([
                'locationId' => $location->id,
                'cabinetTypeId' => $cabinetType->id,
                'number' => $number,
                'programName' => $this->pickUpProgramName(),
                'splitPercent' => $faker->boolean(10) ? $faker->numberBetween(1, 10) : null,
                'flatFee' => $faker->boolean(10) ? $faker->numberBetween(1, 5) * 10 : null,
                'groupName' => null,
                'lastActivityAt' => $faker->dateTimeBetween('-10 days', null)->format('Y-m-d H:i:s'),
            ]);

            if (!$terminal->save()) {
                throw new ErrorsException($terminal->errors);
            }
        }

        Console::endProgress();
    }

    private function pickUpProgramName()
    {
        $names = [
            'Fishtable 1',
            'Fishtable 2',
            'Fishtable 3',
            'Pirates 1',
            'Pirates 2',
            'Pirates 3',
            'Dinosaurs 1',
            'Dinosaurs 2',
            'Dinosaurs 3',
            'Wild Forest 1',
            'Wild Forest 2',
            'Wild Forest 3',
        ];

        return $names[rand(0, count($names) - 1)];
    }

    /**
     * @param string $since
     * @param string $until
     * @throws ErrorsException
     * @throws \Exception
     */
    public function actionConvolutions(string $since, string $until): void
    {
        $total = Terminal::find()->count();

        Console::startProgress(0, $total, 'Generating convolutions');
        $faker = Factory::create();

        $period = new DatePeriod(
            new DateTime($since),
            DateInterval::createFromDateString('1 day'),
            new DateTime($until),
        );

        /**
         * @var int $i
         * @var  Terminal $terminal
         */
        foreach (Terminal::find()->each() as $i => $terminal) {
            /** @var DateTime $datetime */
            foreach ($period as $datetime) {
                $format = $datetime->format('Y-m-d');

                if (Convolution::find()->andWhere([
                    'date' => $format,
                    'terminal' => $terminal->number,
                    'locationId' => $terminal->locationId,
                    'isLive' => $terminal->location->isLive,
                ])->exists()) {
                    continue;
                }

                $moneyIn = $faker->numberBetween(10_000, 20_000) / 100.0;
                $moneyOut = $faker->numberBetween(2_000, 12_000) / 100.0;

                $splitPercent = $terminal->splitPercent ?: $terminal->location->splitPercent;

                $convolution = new Convolution([
                    'terminal' => $terminal->number,
                    'locationId' => $terminal->locationId,
                    'date' => $datetime->format('Y-m-d'),
                    'moneyIn' => $moneyIn,
                    'moneyOut' => $moneyOut,
                    'isLive' => $terminal->location->isLive,
                    'percentageProfit' => round(($moneyIn - $moneyOut) * $splitPercent / 100 + $terminal->flatFee * $terminal->location->getTerminals()->count(), 2),
                    'lastLogAt' => $faker->dateTimeBetween($format, $format . ' 23:59:59')->format('Y-m-d H:i:s'),
                ]);

                if (!$convolution->save()) {
                    throw new ErrorsException($convolution->errors);
                }

                Console::updateProgress($i + 1, $total, "Convolutions for $terminal->number");
            }
        }

        Console::endProgress();
    }
}