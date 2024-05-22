<?php

namespace api\categories;

use ApiTester;
use app\models\Category;
use Exception;

/**
 *
 */
class GetCategoryCest
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
        $category = Category::find()->random()->one();

        $I->sendPost('/app/categories/get', ['id' => $category->id]);
        $I->seeResponseCodeIs(200);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function invalidId(ApiTester $I): void
    {
        $I->sendPost('/app/categories/get', ['id' => -1]);
        $I->seeResponseCodeIs(400);
    }
}
