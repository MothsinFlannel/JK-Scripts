<?php


namespace app\modules\app\scripts\categories;


use app\components\Script;
use app\models\Category;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteCategoryScript
 * @package app\modules\app\scripts\categories
 */
class DeleteCategoryScript extends Script
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', ExistValidator::class, 'targetClass' => Category::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Category::findOne($this->id)->delete();
    }
}