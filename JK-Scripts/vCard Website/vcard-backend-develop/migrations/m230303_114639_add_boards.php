<?php

use yii\db\Migration;

/**
 * Class m230303_114639_add_boards
 */
class m230303_114639_add_boards extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('board', [
            'id' => 'pk',
            'title' => 'varchar(256) not null',
            'externalId' => 'varchar(32)',
            'columns' => 'varchar(256)[]',
            'isActive' => 'boolean default true',
            'createdAt' => 'timestamptz(0) not null default now()',
        ]);

        $this->createTable('task', [
            'id' => 'pk',
            'authorId' => 'int not null',
            'boardId' => 'int not null',
            'column' => 'varchar(64)',
            'summary' => 'varchar(256)',
            'description' => 'text',
        ]);

        $this->createIndex('idxTaskColumn', 'task', 'column');
        $this->addForeignKey('fkTaskBoardId', 'task', 'boardId', 'board', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fkTaskAuthorId', 'task', 'authorId', 'user', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230303_114639_add_boards cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230303_114639_add_boards cannot be reverted.\n";

        return false;
    }
    */
}
