<?php

use yii\db\Migration;

/**
 * Class m230306_144450_add_attachments
 */
class m230306_144450_add_attachments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('attachment', [
            'id' => 'pk',
            'referenceType' => 'varchar(64) not null',
            'referenceId' => 'int not null',
            'title' => 'varchar(256)',
            'file' => 'varchar(256)',
            'type' => 'varchar(32)',
            'createdAt' => 'timestamptz(0) not null default now()',
        ]);

        $this->createIndex('idxAttachmentReference', 'attachment', ['referenceType', 'referenceId']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230306_144450_add_attachments cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_144450_add_attachments cannot be reverted.\n";

        return false;
    }
    */
}
