<?php

namespace api\clerks;

use ApiTester;
use app\models\Clerk;
use Exception;

/**
 *
 */
class GetClerkCest
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
     * @throws Exception
     */
    public function validId(ApiTester $I): void
    {
        $clerk = Clerk::find()->random()->one();

        $I->sendPost('/app/clerks/get', ['id' => $clerk->id]);
        $I->seeResponseCodeIs(200);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function invalidId(ApiTester $I): void
    {
        $I->sendPost('/app/clerks/get', ['id' => -1]);
        $I->seeResponseCodeIs(400);
    }
}
