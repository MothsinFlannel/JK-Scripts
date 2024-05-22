<?php

use yii\db\Migration;

/**
 * Class m210803_083313_add_location_notes
 */
class m210803_083313_add_location_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'notes', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_083313_add_location_notes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210803_083313_add_location_notes cannot be reverted.\n";

        return false;
    }
    */
}
