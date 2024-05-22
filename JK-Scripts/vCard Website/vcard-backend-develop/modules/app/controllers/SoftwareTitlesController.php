<?php

namespace app\modules\app\controllers;

use app\models\SoftwareTitle;

/**
 *
 */
class SoftwareTitlesController extends ReferenceController
{
    /**
     * @var string
     */
    public string $targetClass = SoftwareTitle::class;
}