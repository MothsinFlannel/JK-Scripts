<?php


namespace app\commands;


use app\models\ext\Location;
use app\models\ext\Terminal;
use app\modules\api\scripts\location\TerminalScript;
use DateTime;
use Faker\Factory;
use Throwable;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class PosController
 * @package app\commands
 */
class PosController extends Controller
{

    /**
     * @param int $count
     * @throws Throwable
     */
    public function actionSimulate(int $count = 100)
    {
        Console::startProgress(0, $count);

        foreach (range(1, $count) as $i) {
            $terminal = Terminal::find()
                ->rightJoin([
                    'location' => Location::find()->active()->live()
                ], '[[location.id]] = [[terminal.locationId]]')
                ->andWhere('[[terminal.id]] is not null')
                ->random()
                ->limit(1)
                ->one();

            // TODO: sort out how it is possible that this condition is necessary
            if (!$terminal || !$terminal->location) {
                continue;
            }

            $faker    = Factory::create();
            $positive = $faker->boolean(80);

            (new TerminalScript([
                'serial'    => $terminal->location->serial,
                'terminal'  => $terminal->number,
                'moneyIn'   => $positive ? $faker->randomNumber(2) * 100 : 0,
                'moneyOut'  => !$positive ? $faker->randomNumber(2) * 100 : 0,
                'timestamp' => (new DateTime())->format('Y-m-d H:i:s'),
            ]))->execute();

            Console::updateProgress($i, $count);
        }

        Console::endProgress();
    }
}