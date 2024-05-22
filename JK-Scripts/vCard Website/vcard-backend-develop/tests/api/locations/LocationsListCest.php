<?php

namespace api\locations;

use ApiTester;
use vr\core\PagedListScript;

/**
 *
 */
class LocationsListCest
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
     */
    public function noParams(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', []);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function secondPage(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'offset' => PagedListScript::DEFAULT_LIMIT,
            'limit' => PagedListScript::DEFAULT_LIMIT
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterSince(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'since' => '2020-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterUntil(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'since' => '2020-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterUntilInTheFuture(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'since' => '2030-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterInRange(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'since' => '2020-11-01',
                'until' => '2021-11-01'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterQuery(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'query' => 'A'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterOnlineOnly(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'offline' => false
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterOfflineOnly(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'offline' => true
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterActiveOnly(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'activeOnly' => true
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterSortName(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'sort' => 'name'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterSortNameAsc(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'sort' => 'name+asc'
            ]
        ]);
    }

    /**
     * @param ApiTester $I
     * @return void
     */
    public function filterSortNameDesc(ApiTester $I): void
    {
        $I->sendPost('/app/locations/list', [
            'filters' => [
                'sort' => 'name+desc'
            ]
        ]);
    }
}
