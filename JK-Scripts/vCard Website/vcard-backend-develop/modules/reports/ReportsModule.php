<?php

namespace app\modules\reports;

use vr\api\Module;

/**
 * reports module definition class
 */
class ReportsModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\reports\controllers';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // custom initialization code goes here
    }
}
