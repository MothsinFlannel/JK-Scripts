<?php

namespace app\modules\app\scripts\misc;

use Throwable;
use vr\core\Script;

/**
 *
 */
class StatesListScript extends Script
{
    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws Throwable
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'results' => [
                'ALABAMA',
                'ALASKA',
                'ARIZONA',
                'ARKANSAS',
                'CALIFORNIA',
                'COLORADO',
                'CONNECTICUT',
                'DELAWARE',
                'DISTRICT OF COLUMBIA',
                'FLORIDA',
                'GEORGIA',
                'HAWAII',
                'IDAHO',
                'ILLINOIS',
                'INDIANA',
                'IOWA',
                'KANSAS',
                'KENTUCKY',
                'LOUISIANA',
                'MAINE',
                'MONTANA',
                'NEBRASKA',
                'NEVADA',
                'NEW HAMPSHIRE',
                'NEW JERSEY',
                'NEW MEXICO',
                'NEW YORK',
                'NORTH CAROLINA',
                'NORTH DAKOTA',
                'OHIO',
                'OKLAHOMA',
                'OREGON',
                'MARYLAND',
                'MASSACHUSETTS',
                'MICHIGAN',
                'MINNESOTA',
                'MISSISSIPPI',
                'MISSOURI',
                'PENNSYLVANIA',
                'RHODE ISLAND',
                'SOUTH CAROLINA',
                'SOUTH DAKOTA',
                'TENNESSEE',
                'TEXAS',
                'UTAH',
                'VERMONT',
                'VIRGINIA',
                'WASHINGTON',
                'WEST VIRGINIA',
                'WISCONSIN',
                'WYOMING',
            ]
        ];
    }

    /**
     * @return void
     */
    protected function onExecute(): void
    {

    }
}