<?php

use yii\db\Migration;

/**
 * Class m211215_130800_add_recovery_token
 */
class m211215_130800_add_recovery_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'recoveryToken', 'varchar(64) null default null');
        $this->createIndex('idxUserRecoveryToken', 'user', 'recoveryToken', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211215_130800_add_recovery_token cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211215_130800_add_recovery_token cannot be reverted.\n";

        return false;
    }
    */
}
