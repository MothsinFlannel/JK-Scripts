<?php

namespace app\modules\api\validators;

use app\models\ext\Location;
use vr\core\validators\ExistValidator;

/**
 * Class LocationValidator
 * @package app\modules\api\validators
 */
class LocationValidator extends ExistValidator
{
    /**
     * @var string
     */
    public $targetClass = Location::class;

    /**
     * @var string
     */
    public $targetAttribute = 'serial';
}