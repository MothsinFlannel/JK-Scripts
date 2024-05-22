<?php

use yii\db\Migration;

/**
 * Class m221101_183157_add_notes
 */
class m221101_183157_add_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment', 'notes', 'text');
        $this->addColumn('invoice', 'notes', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221101_183157_add_notes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221101_183157_add_notes cannot be reverted.\n";

        return false;
    }
    */
}
