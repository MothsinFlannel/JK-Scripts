<?php

namespace app\components;

use DateTime;
use yii\behaviors\AttributesBehavior;
use yii\db\BaseActiveRecord;

/**
 *
 */
class ReformatTimestampBehavior extends AttributesBehavior
{
    /**
     *
     */
    public function init()
    {
        parent::init();

        $result = [];
        foreach ($this->attributes as $item) {
            $result[$item] = [
                BaseActiveRecord::EVENT_AFTER_FIND => function ($event, $attribute) {
                    if (($attribute = $event->sender->$attribute) === null) {
                        return null;
                    }
                    return (new DateTime($attribute))->format('Y-m-d H:i:s');
                },
            ];
        }
        $this->attributes = $result;
    }
}