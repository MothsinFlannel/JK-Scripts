<?php

namespace app\modules\app\scripts\cabinets;

use app\models\CabinetType;
use app\modules\app\scripts\reference\ItemsListScript;
use yii\base\InvalidConfigException;

/**
 *
 */
class CabinetTypesListScript extends ItemsListScript
{
    /**
     * @var string
     */
    public string $targetClass = CabinetType::class;

    /**
     * @return void
     * @throws InvalidConfigException
     */
    protected function onExecute(): void
    {
        parent::onExecute();

        if (@$this->filters->activeOnly) {
            $this->_query->andWhere([
                'isActive' => true
            ]);
        }
    }
}