<?php


namespace reports;

use ReportsTester;

class RemainsCest
{
    /**
     * @param ReportsTester $I
     * @return void
     */
    public function _before(ReportsTester $I): void
    {
        $I->amBearerAuthenticated($I::BEARER_TOKEN);
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
    }

    /**
     * @param ReportsTester $I
     * @return void
     */
    public function _after(ReportsTester $I): void
    {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }

    // tests

    /**
     * @param ReportsTester $I
     * @return void
     */
    public function noParams(ReportsTester $I): void
    {
        $I->sendPost('/reports/locations/remains', [
            'since' => '2023-05-15',
            'until' => '2023-05-21',
            'export' => false,
            'offset' => 0,
            'limit' => 20,
        ]);
    }
}
