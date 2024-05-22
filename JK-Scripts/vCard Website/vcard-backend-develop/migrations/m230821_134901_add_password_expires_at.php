<?php

use yii\db\Migration;

/**
 * Class m230821_134901_add_password_expires_at
 */
class m230821_134901_add_password_expires_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'passwordExpiresAt', 'timestamp(0) null default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'passwordExpiresAt');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230821_134901_add_password_expires_at cannot be reverted.\n";

        return false;
    }
    */
}
