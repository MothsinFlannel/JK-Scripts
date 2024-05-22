<?php

namespace api\charts;

use ApiTester;

class RevenueChartCest
{
    public function _before(ApiTester $I): void
    {
        $I->amBearerAuthenticated($I::BEARER_TOKEN);
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
    }

    public function _after(ApiTester $I): void
    {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }

    // tests
    public function noParams(ApiTester $I): void
    {
        $I->sendPost('/app/charts/revenue', [
            'since' => '2023-03-29',
            'until' => '2023-04-04'
        ]);
    }
}
