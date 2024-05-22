<?php

declare(strict_types=1);


/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class ReportsTester extends \Codeception\Actor
{
    use _generated\ReportsTesterActions;

    /**
     * Define custom actions here
     */

    const BEARER_TOKEN = '9AptA5XcnVvU7R8L2P3Bd9EL5gRyfALJ';
}
