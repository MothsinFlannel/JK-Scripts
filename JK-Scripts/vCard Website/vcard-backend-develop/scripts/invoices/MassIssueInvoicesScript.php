<?php


namespace app\scripts\invoices;


use app\models\ext\Invoice;
use app\models\ext\Location;
use DateInterval;
use DateTime;
use Throwable;
use vr\core\Script;

/**
 * Class MassIssueInvoicesScript
 * @package app\scripts\invoices
 */
class MassIssueInvoicesScript extends Script
{
    /**
     * @var int
     */
    public int $weeksEarlier = 0;

    /**
     * @throws Throwable
     */
    protected function onExecute()
    {
        $query = Location::find()
            ->live()
            ->orderBy('id');

        /** @var Location $location */
        foreach ($query->each() as $location) {
            foreach (range(0, $this->weeksEarlier) as $index) {
                list($since, $until) = $this->getPeriod($index);

                $params = [
                    'since' => $since,
                    'until' => $until,
                    'locationId' => $location->id,
                ];

                Invoice::findOne($params) ?: (new IssueInvoiceScript($params))->execute();
            }
        }
    }

    /**
     * @param $weekEarlier
     * @return array
     */
    private function getPeriod($weekEarlier): array
    {
        $bias = DateInterval::createFromDateString("$weekEarlier weeks");
        return [
            (new DateTime('last Sunday'))
                ->sub($bias)
                ->sub(DateInterval::createFromDateString('6 days'))->format('Y-m-d'),
            (new DateTime('last Sunday'))->sub($bias)->format('Y-m-d')
        ];
    }
}