<?php


namespace app\modules\render\controllers;


use app\models\ext\Invoice;
use app\models\ext\User;
use app\scripts\invoices\InvoicesListTrait;
use Throwable;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class InvoicesController
 * @package app\modules\render\controllers
 */
class InvoicesController extends Controller
{
    use InvoicesListTrait;

    /**
     * @var string
     */
    public $layout = '@app/views/layouts/main';

    public function behaviors(): array
    {
        return [
            'cors' => [
                'class' => Cors::class,
            ],
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'view' => ['GET'],
                    'print' => ['POST', 'GET'],
                ]
            ],
            'auth' => [
                'class' => HttpBearerAuth::class,
                'only' => ['print'],
            ]
        ];
    }

    /**
     * @param string $token
     * @param bool $raw
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(string $token, bool $raw = false): string
    {
        if ($raw) {
            $this->layout = false;
        }

        $invoice = Invoice::findOne(['token' => $token]);
        if (!$invoice) {
            throw new NotFoundHttpException('Invoice not found');
        }

        $this->view->params = ['title' => "Invoice #$invoice->id"];
        return $this->render('//invoices/view', [
            'invoice' => $invoice
        ]);
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function actionPrint(): string
    {
        $this->view->params = ['title' => "Print Invoices"];

        $query = $this->createQuery(User::loggedIn(), Json::decode(Yii::$app->request->rawBody))
            ->with([
                'location',
                'payments',
                'invoiceItems'
            ]);

        return $this->render('//invoices/print', [
            'invoices' => $query->all(),
        ]);
    }
}