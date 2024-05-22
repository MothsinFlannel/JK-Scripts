<?php

namespace api\invoices;

use ApiTester;
use Exception;
use vr\core\PagedListScript;

class InvoicesListCest
{
    /**
     * @param ApiTester $I
     */
    public function _before(ApiTester $I): void
    {
        $I->amBearerAuthenticated($I::BEARER_TOKEN);
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
    }

    /**
     * @param ApiTester $I
     */
    public function _after(ApiTester $I): void
    {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }

    // tests

    /**
     * @param ApiTester $I
     */
    public function noParams(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/list', []);
    }

    /**
     * @param ApiTester $I
     */
    public function secondPage(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/list', [
            'offset' => PagedListScript::DEFAULT_LIMIT,
            'limit' => PagedListScript::DEFAULT_LIMIT
        ]);
    }

    /**
     * @param ApiTester $I
     */
    public function filterSince(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/list', [
            'filters' => [
                'since' => '2020-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     */
    public function filterUntil(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/list', [
            'filters' => [
                'since' => '2020-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     */
    public function filterUntilInTheFuture(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/list', [
            'filters' => [
                'since' => '2030-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     */
    public function filterInRange(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/list', [
            'filters' => [
                'since' => '2020-11-01',
                'until' => '2021-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     */
    public function filterPaid(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'status' => 'paid'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     */
    public function filterUnpaid(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'status' => 'unpaid'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @throws Exception
     */
    public function forLocation(ApiTester $I): void
    {
        $I->sendPost('/app/invoices/list', []);

        list($locationId) = $I->grabDataFromResponseByJsonPath('$.results.[0].locationId');
        $I->sendPost('/app/invoices/list', [
            'filters' => [
                'locationId' => $locationId
            ]
        ]);
    }
}
