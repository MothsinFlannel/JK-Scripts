<?php

namespace api\clerks;

use ApiTester;

class ClerksListCest
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
        $I->sendPost('/app/clerks/list', []);
    }
}
