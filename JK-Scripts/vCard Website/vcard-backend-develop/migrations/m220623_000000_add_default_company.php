<?php

use yii\db\Migration;

/**
 * Class m220623_000000_add_default_company
 */
class m220623_000000_add_default_company extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company', 'isDefault', 'boolean not null default false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220623_000000_add_default_company cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220623_000000_add_default_company cannot be reverted.\n";

        return false;
    }
    */
}
