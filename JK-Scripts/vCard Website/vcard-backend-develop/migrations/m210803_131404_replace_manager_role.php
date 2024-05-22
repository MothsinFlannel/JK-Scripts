<?php

use yii\db\Migration;

/**
 * Class m210803_131404_replace_manager_role
 */
class m210803_131404_replace_manager_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TYPE "role" ADD VALUE \'location\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_131404_replace_manager_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210803_131404_replace_manager_role cannot be reverted.\n";

        return false;
    }
    */
}
