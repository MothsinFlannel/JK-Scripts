<?php

namespace app\models\ext;

use app\components\ReformatTimestampBehavior;
use app\models\CompanyQuery;

/**
 *
 */
class Company extends \app\models\Company
{
    /**
     * {@inheritdoc}
     * @return CompanyQuery the active query used by this AR class.
     */
    public static function find(): CompanyQuery
    {
        return (new CompanyQuery(get_called_class()))
            ->andFilterWhere([
                'company.id' => User::loggedIn()->companyId,
            ]);
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => ReformatTimestampBehavior::class,
                'attributes' => ['createdAt']
            ]
        ];
    }
}