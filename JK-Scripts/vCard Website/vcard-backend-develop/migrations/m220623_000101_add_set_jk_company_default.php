<?php

use yii\db\Migration;

/**
 * Class m220623_000101_add_set_jk_company_default
 */
class m220623_000101_add_set_jk_company_default extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('company', ['isDefault' => true], ['name' => 'JK Group']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220623_000101_add_set_jk_company_default cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220623_000101_add_set_jk_company_default cannot be reverted.\n";

        return false;
    }
    */
}
