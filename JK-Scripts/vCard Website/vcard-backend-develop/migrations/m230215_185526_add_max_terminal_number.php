<?php

use yii\db\Migration;

/**
 * Class m230215_185526_add_max_terminal_number
 */
class m230215_185526_add_max_terminal_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'maxTerminalNumber', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230215_185526_add_max_terminal_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230215_185526_add_max_terminal_number cannot be reverted.\n";

        return false;
    }
    */
}
