<?php


namespace app\models\ext;


use app\components\ReformatTimestampBehavior;
use vr\core\ErrorsException;
use yii\db\ActiveRecordInterface;

/**
 * Class Audit
 * @package app\models\ext
 */
class Audit extends \app\models\Audit
{
    /**
     * @param ActiveRecordInterface $activeRecord
     * @param string $attribute
     * @return Audit
     * @throws ErrorsException
     */
    public static function log(ActiveRecordInterface $activeRecord, string $attribute): Audit
    {
        $audit = new Audit([
            'entity'     => call_user_func([$activeRecord, 'tableName']),
            'identifier' => $activeRecord->getPrimaryKey(),
            'attribute'  => $attribute,
            'value'      => (string)$activeRecord->getAttribute($attribute)
        ]);

        if (!$audit->save() || !$audit->refresh()) {
            throw new ErrorsException($audit->errors);
        }

        return $audit;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class'      => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }
}