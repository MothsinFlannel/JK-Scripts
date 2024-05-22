<?php

use yii\db\Migration;

/**
 * Class m220221_212934_add_company_id
 */
class m220221_212934_add_company_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'companyId', 'int');
        $this->addForeignKey('fkUserCompanyId', 'user', 'companyId', 'company', 'id', 'set null', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220221_212934_add_company_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220221_212934_add_company_id cannot be reverted.\n";

        return false;
    }
    */
}
