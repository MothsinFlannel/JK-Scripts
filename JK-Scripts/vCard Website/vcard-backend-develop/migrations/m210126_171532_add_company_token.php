<?php

use yii\db\Migration;

/**
 * Class m210126_171532_add_company_token
 */
class m210126_171532_add_company_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company', 'token', 'varchar(128)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210126_171532_add_company_token cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_171532_add_company_token cannot be reverted.\n";

        return false;
    }
    */
}
