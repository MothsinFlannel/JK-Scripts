<?php


namespace app\modules\app\controllers;


use app\models\Padlock;

/**
 * Class PadlocksController
 * @package app\modules\app\controllers
 */
class PadlocksController extends ReferenceController
{
    public string $targetClass = Padlock::class;
}