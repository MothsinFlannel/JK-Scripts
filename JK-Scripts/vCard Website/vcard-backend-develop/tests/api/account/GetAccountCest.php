<?php

namespace api\account;

use ApiTester;
use app\models\ext\User;

/**
 *
 */
class GetAccountCest
{
    /**
     * @param ApiTester $I
     * @return void
     */
    public function _before(ApiTester $I): void
    {
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
     */
    public function admin(ApiTester $I): void
    {
        $user = User::find()->andWhere(['role' => User::ROLE_ADMIN])->one();

        $I->amBearerAuthenticated($user->accessToken);
        $I->sendPost('/app/account/get', []);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function routeman(ApiTester $I): void
    {
        $user = User::find()->andWhere(['role' => User::ROLE_ROUTEMAN])->one();

        $I->amBearerAuthenticated($user->accessToken);
        $I->sendPost('/app/account/get', []);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function location(ApiTester $I): void
    {
        $user = User::find()->andWhere(['role' => User::ROLE_LOCATION])->one();

        $I->amBearerAuthenticated($user->accessToken);
        $I->sendPost('/app/account/get', []);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function technician(ApiTester $I): void
    {
        $user = User::find()->andWhere(['role' => User::ROLE_TECHNICIAN])->one();

        $I->amBearerAuthenticated($user->accessToken);
        $I->sendPost('/app/account/get', []);
    }
}
