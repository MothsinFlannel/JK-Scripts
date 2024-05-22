<?php

namespace app\modules\app;

use vr\api\Module;

/**
 * app module definition class
 */
class AppModule extends Module
{
    public $docEnabled = true;
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\app\controllers';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();
    }
}
