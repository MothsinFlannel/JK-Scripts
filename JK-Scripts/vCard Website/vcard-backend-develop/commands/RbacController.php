<?php


namespace app\commands;

use app\components\RbacManager;
use Exception;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\db\Connection;

/**
 * Class RbacController
 * @package app\commands
 */
class RbacController extends Controller
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function actionReload()
    {
        Yii::$app->db->transaction(function (Connection $connection) {
            (new RbacManager())->reload();
            $connection->transaction->commit();
        });
    }
}