<?php

use yii\db\Migration;

/**
 * Class m200922_211203_add_installation
 */
class m200922_211203_add_installation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('installation', [
            'id'        => 'pk',
            'serial'    => 'varchar(32) not null',
            'email'     => 'varchar(128) not null',
            'createdAt' => 'timestamptz(0) not null default now()',
            'handledAt' => 'timestamptz(0)',
        ]);

        $this->createIndex('installationSerial', 'installation', 'serial', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_211203_add_installation cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_211203_add_installation cannot be reverted.\n";

        return false;
    }
    */
}
