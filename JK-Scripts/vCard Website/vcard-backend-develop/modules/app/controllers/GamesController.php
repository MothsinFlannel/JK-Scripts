<?php


namespace app\modules\app\controllers;

use app\models\Game;

/**
 * Class GamesController
 * @package app\modules\app\controllers
 */
class GamesController extends ReferenceController
{
    /**
     * @var string
     */
    public string $targetClass = Game::class;
}