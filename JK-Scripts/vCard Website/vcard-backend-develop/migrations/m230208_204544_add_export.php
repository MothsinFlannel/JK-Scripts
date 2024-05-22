<?php

use yii\db\Migration;

/**
 * Class m230208_204544_add_export
 */
class m230208_204544_add_export extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('job', [
            'id'          => 'pk',
            'initiatorId' => 'int',
            'category'    => 'varchar(32) not null',
            'title'       => 'varchar(256) not null',
            'createdAt'   => 'timestamptz(0) not null default now()',
            'endedAt'     => 'timestamptz(0)',
            'progress'    => 'int',
            'output'      => 'varchar(256)',
        ]);

        $this->addForeignKey('fkJobInitiatorId', 'job', 'initiatorId', 'user', 'id', 'set null', 'set null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230208_204544_add_export cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230208_204544_add_export cannot be reverted.\n";

        return false;
    }
    */
}
