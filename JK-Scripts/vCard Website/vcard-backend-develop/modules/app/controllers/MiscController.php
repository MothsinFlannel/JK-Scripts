<?php

namespace app\modules\app\controllers;

use app\modules\app\components\Controller;
use app\modules\app\scripts\misc\StatesListScript;
use app\modules\app\scripts\misc\TimezonesScript;
use Throwable;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;

/**
 * Class MiscController
 * @package app\modules\app\controllers
 */
class MiscController extends Controller
{
    /**
     * @var string[]
     */
    public $authExcept = ['*'];

    /**
     * @return array
     * @throws ErrorsException
     * @throws VerboseException
     */
    public function actionTimezones(): array
    {
        $this->checkInputParams(fn() => []);
        return (new TimezonesScript())->execute()->toArray();
    }

    /**
     * @return array
     * @throws ErrorsException
     * @throws Throwable
     * @throws VerboseException
     */
    public function actionStates(): array
    {
        $this->checkInputParams(fn() => []);
        return (new StatesListScript())->execute()->toArray();
    }
}