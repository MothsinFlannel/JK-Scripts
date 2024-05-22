<?php

namespace api\invoices;

use ApiTester;
use app\models\ext\Invoice;
use Throwable;

/**
 *
 */
class GetInvoiceCest
{
    /**
     * @param ApiTester $I
     * @return void
     */
    public function _before(ApiTester $I): void
    {
        $I->amBearerAuthenticated($I::BEARER_TOKEN);
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function _after(ApiTester $I): void
    {
        $I->seeResponseIsJson();
    }

    // tests

    /**
     * @param ApiTester $I
     * @return void
     * @throws Throwable
     */
    public function validId(ApiTester $I): void
    {
        $invoice = Invoice::find()->random()->one();

        $I->sendPost('/app/invoices/get', ['id' => $invoice->id]);
        $I->seeResponseCodeIs(200);
    }

    // tests

    /**
     * @param ApiTester $I
     * @return void
     */
    public function invalidId(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/get', ['id' => -1]);
        $I->seeResponseCodeIs(404);
    }
}
