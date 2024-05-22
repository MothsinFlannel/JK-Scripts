<?php


namespace app\modules\app\controllers;

use app\models\MachineType;

/**
 * Class MachineTypesController
 * @package app\modules\app\controllers
 */
class MachineTypesController extends ReferenceController
{
    /**
     * @var string
     */
    public string $targetClass = MachineType::class;
}