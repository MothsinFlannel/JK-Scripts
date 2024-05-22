<?php

use yii\db\Migration;

/**
 * Class m210316_224427_add_disable_printing
 */
class m210316_224427_add_disable_printing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'disablePrinting', 'boolean not null default false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210316_224427_add_disable_printing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210316_224427_add_disable_printing cannot be reverted.\n";

        return false;
    }
    */
}
