<?php


namespace app\modules\app\controllers;


use app\models\ext\Invoice;
use app\models\ext\InvoiceItem;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\items\CreateInvoiceItemsScript;
use app\modules\app\scripts\items\UpdateInvoiceItemScript;
use Exception;
use Faker\Factory;
use vr\core\ErrorsException;
use vr\core\Inflector;
use Yii;

/**
 * Class InvoiceItemsController
 * @package app\modules\app\controllers
 */
class InvoiceItemsController extends Controller
{
    /**
     * @return array
     * @throws ErrorsException
     * @throws Exception
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $invoice = Invoice::find()->random()->one();

            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'invoiceItems' => [
                    [
                        'invoiceId' => $invoice->id,
                        'title' => Inflector::titleize($faker->sentence),
                        'balance' => $faker->randomNumber(2) * 10,
                    ]
                ]
            ];
        });

        return (new CreateInvoiceItemsScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws Exception
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $invoiceItem = InvoiceItem::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$invoiceItem->id,
                'invoiceItem' => $invoiceItem->toArray([
                    'title',
                    'totalIn',
                    'totalOut',
                ]),
            ];
        });

        return (new UpdateInvoiceItemScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}