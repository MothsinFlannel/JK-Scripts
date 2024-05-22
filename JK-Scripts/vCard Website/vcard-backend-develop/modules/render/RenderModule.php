<?php

namespace app\modules\render;

use yii\base\Module;

/**
 * render module definition class
 */
class RenderModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\render\controllers';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // custom initialization code goes here
    }
}
