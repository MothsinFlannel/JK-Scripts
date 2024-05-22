<?php

use yii\db\Migration;

/**
 * Class m211216_085908_add_partial_status
 */
class m211216_085908_add_partial_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TYPE invoicestatus ADD VALUE \'partial\' AFTER \'paid\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211216_085908_add_partial_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211216_085908_add_partial_status cannot be reverted.\n";

        return false;
    }
    */
}
