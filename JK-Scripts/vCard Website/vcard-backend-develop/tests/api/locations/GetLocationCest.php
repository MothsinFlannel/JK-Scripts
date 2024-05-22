<?php

namespace api\locations;

use ApiTester;
use app\models\ext\Location;
use Throwable;

/**
 *
 */
class GetLocationCest
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
        $location = Location::find()->random()->one();

        $I->sendPost('/app/locations/get', ['id' => $location->id]);
        $I->seeResponseCodeIs(200);
    }

    // tests

    /**
     * @param ApiTester $I
     * @return void
     */
    public function invalidId(ApiTester $I): void
    {
        $I->sendPost('/app/locations/get', ['id' => -1]);
        $I->seeResponseCodeIs(400);
    }
}
