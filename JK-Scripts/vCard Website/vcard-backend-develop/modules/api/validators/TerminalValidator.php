<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace app\modules\api\validators;

use app\models\ext\Location;
use app\models\ext\Terminal;
use app\models\TerminalQuery;
use RuntimeException;
use vr\core\validators\ExistValidator;

/**
 * Class TerminalValidator
 * @package app\modules\api\validators
 */
class TerminalValidator extends ExistValidator
{
    /**
     * @var string
     */
    public $targetClass = Terminal::class;

    /**
     * @var string
     */
    public $targetAttribute = 'number';

    /**
     * @var string
     */
    public $serial = null;

    /**
     *
     */
    public function init(): void
    {
        parent::init();

        if (!$this->serial) {
            throw new RuntimeException('Set up serial first to identify a location');
        }

        $this->filter = $this->filter ?: function (TerminalQuery $query) {
            return $query
                ->rightJoin([
                    'location' => Location::find()->andWhere(['serial' => $this->serial])
                ], 'location.id = [[terminal.locationId]]')
                ->andWhere('terminal.id is not null');
        };
    }
}