<?php


namespace app\modules\app\controllers;


use app\models\ext\Invoice;
use app\models\ext\Location;
use app\models\ext\User;
use app\modules\app\components\Controller;
use app\modules\app\scripts\invoices\CreateInvoiceScript;
use app\modules\app\scripts\invoices\DeleteInvoiceScript;
use app\modules\app\scripts\invoices\ExportInvoicesScript;
use app\modules\app\scripts\invoices\GetInvoiceScript;
use app\modules\app\scripts\invoices\InvoicesListScript;
use app\modules\app\scripts\invoices\PayInvoiceScript;
use app\modules\app\scripts\invoices\RebuildInvoiceScript;
use app\modules\app\scripts\invoices\UpdateInvoiceScript;
use app\modules\app\scripts\invoices\ViewInvoiceScript;
use app\scripts\invoices\MassIssueInvoicesScript;
use DateTime;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use vr\core\PagedListScript;
use Yii;

/**
 * Class InvoicesController
 * @package app\modules\app\controllers
 */
class InvoicesController extends Controller
{
    /**
     * @var string[]
     */
    public $authExcept = ['view'];

    /**
     * @return array
     * @throws ErrorsException
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionList(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'query' => null,

                    'locationId' => null,
                    'routeId' => null,
                    'status' => null,
                    'since' => null,
                    'until' => null,
                    'ids' => null,
                ],
                'extraFields' => true,
                'offset' => 0,
                'limit' => PagedListScript::DEFAULT_LIMIT,
                'sort' => null,
            ];
        });

        return (new InvoicesListScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionExport(): void
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
                'filters' => [
                    'locationId' => null,
                    'routeId' => null,
                    'status' => null,
                    'since' => null,
                    'until' => null,
                ],
                'format' => 'html',
                'sort' => null,
            ];
        });

        (new ExportInvoicesScript(Yii::$app->request->bodyParams))->execute();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $location = Location::find()
                ->andWhere(['invoicingMode' => Location::INVOICING_MODE_CUSTOM])
                ->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'invoice' => [
                    'since' => (new DateTime('-7 days'))->format('Y-m-d'),
                    'until' => (new DateTime('yesterday'))->format('Y-m-d'),
                    'locationId' => @$location->id,
                ]
            ];
        });

        return (new CreateInvoiceScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $invoice = Invoice::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$invoice->id,
                'invoice' => $invoice->toArray([
                    'notes',
                    'since',
                    'until',
                    'amount',
                    'invoiceItems.id',
                    'invoiceItems.number',
                    'invoiceItems.title',
                    'invoiceItems.type',
                    'invoiceItems.totalIn',
                    'invoiceItems.totalOut',
                ], ['invoiceItems'])
            ];
        });

        return (new UpdateInvoiceScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionView(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $invoice = Invoice::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'token' => @$invoice->token
            ];
        });

        return (new ViewInvoiceScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionGet(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $invoice = Invoice::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$invoice->id,
            ];
        });

        return (new GetInvoiceScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionRebuild(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $invoice = Invoice::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$invoice->id,
                'splitPercent' => @$invoice->splitPercent,
            ];
        });

        return (new RebuildInvoiceScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }


    /**
     * @return array
     * @throws VerboseException
     * @throws Throwable
     */
    public function actionPay(): array
    {
        // TODO: move to payments
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();

            /** @var Invoice $invoice */
            $invoice = Invoice::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$invoice->id,
                'amount' => @$invoice->unpaidAmount,
                'notes' => null
            ];
        });

        return (new PayInvoiceScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     */
    public function actionIssue(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->admin()->active()->one();

            return [
                'accessToken' => @$user->accessToken,
            ];
        });

        return (new MassIssueInvoicesScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $invoice = Invoice::find()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$invoice->id,
            ];
        });

        return (new DeleteInvoiceScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}