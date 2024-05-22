<?php


namespace app\modules\app\scripts\misc;


use vr\core\Script;

/**
 * Class TimezonesScript
 * @package app\modules\app\scripts\misc
 */
class TimezonesScript extends Script
{
    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array|string[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'results' => [
                'US/Hawaii',
                'US/Alaska',
                'US/Pacific',
                'US/Arizona',
                'US/Mountain',
                'US/Central',
                'US/Indiana-Starke',
                'US/East-Indiana',
                'US/Eastern',
                'US/Michigan',
            ]
        ];
    }

    /**
     *
     */
    protected function onExecute()
    {

    }
}