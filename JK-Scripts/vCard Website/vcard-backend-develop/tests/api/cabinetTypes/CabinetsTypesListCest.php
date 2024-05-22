<?php

namespace api\cabinetTypes;

use ApiTester;

class CabinetsTypesListCest
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I::BEARER_TOKEN);
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
    }

    public function _after(ApiTester $I)
    {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }

    // tests
    public function noParams(ApiTester $I)
    {
        $I->sendPost('/app/cabinet-types/list', []);
    }
}
