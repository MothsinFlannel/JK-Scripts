<?php


namespace app\modules\app\controllers;

use app\models\DoorLock;

/**
 * Class DoorLocksController
 * @package app\modules\app\controllers
 */
class DoorLocksController extends ReferenceController
{
    /**
     * @var string
     */
    public string $targetClass = DoorLock::class;
}