<?php

namespace api\categories;

use ApiTester;
use app\models\Category;
use Exception;

/**
 *
 */
class CategoriesListCest
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
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }

    // tests

    /**
     * @param ApiTester $I
     * @return void
     * @throws Exception
     */
    public function noParams(ApiTester $I): void
    {
        $category = Category::find()->random()->one();
        $I->sendPost('/app/categories/list', [
            'locationId' => $category->locationId
        ]);
    }
}
