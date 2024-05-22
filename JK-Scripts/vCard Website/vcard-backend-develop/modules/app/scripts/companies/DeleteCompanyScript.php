<?php


namespace app\modules\app\scripts\companies;


use app\components\Script;
use app\models\ext\Company;
use Throwable;
use vr\core\validators\ExistValidator;
use yii\db\StaleObjectException;

/**
 * Class DeleteCompanyScript
 * @package app\modules\app\companies
 */
class DeleteCompanyScript extends Script
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
            ['id', ExistValidator::class, 'targetClass' => Company::class]
        ];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function onExecute(): void
    {
        Company::findOne($this->id)->delete();
    }
}