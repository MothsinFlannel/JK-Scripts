<?php

namespace app\modules\app\validators;

use Exception;
use vr\core\ArrayHelper;
use yii\validators\Validator;

/**
 *
 */
class PasswordStrengthValidator extends Validator
{
    /**
     *
     */
    private const PATTERNS = [
        [
            '/^.{8,}$/' => 'Minimum 8 characters in length',
            '/^(?=.*?[a-zA-Z]).{1,}$/' => 'At least one English letter',
            '/^(?=.*?[0-9]).{1,}$/' => 'At least one digit',
            '/^(?=.*?[#?!@$%^&*-]).{1,}$/' => 'At least one special character'
        ],
        [
            '/^.{12,}$/' => 'Minimum 12 characters in length',
            '/^(?=.*?[A-Z]).{1,}$/' => 'At least one uppercase English letter',
            '/^(?=.*?[a-z]).{1,}$/' => 'At least one lowercase English letter',
            '/^(?=.*?[0-9]).{1,}$/' => 'At least one digit',
            '/^(?=.*?[#?!@$%^&*-]).{1,}$/' => 'At least one special character'
        ]
    ];

    /**
     * @var bool
     */
    public bool $strong = true;

    /**
     * {@inheritDoc}
     *
     * @param $model
     * @param $attribute
     * @return void
     * @throws Exception
     */
    public function validateAttribute($model, $attribute): void
    {
        $patterns = ArrayHelper::getValue(self::PATTERNS, intval($this->strong));

        foreach ($patterns as $pattern => $message) {
            if (!preg_match($pattern, $model->$attribute)) {
                if (!$model->hasErrors($attribute)) {
                    $model->addError($attribute, 'The password must have:');
                }

                $model->addError($attribute, $message);
            }
        }
    }
}