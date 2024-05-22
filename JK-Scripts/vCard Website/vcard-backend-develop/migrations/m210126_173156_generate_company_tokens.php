<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m210126_173156_generate_company_tokens
 */
class m210126_173156_generate_company_tokens extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->from('company')
            ->andWhere('token is null')
            ->asArray();

        foreach ($query->each() as $company) {
            $this->update('company', [
                'token' => Yii::$app->security->generateRandomString(128),
            ], ['id' => $company['id']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210126_173156_generate_company_tokens cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_173156_generate_company_tokens cannot be reverted.\n";

        return false;
    }
    */
}
