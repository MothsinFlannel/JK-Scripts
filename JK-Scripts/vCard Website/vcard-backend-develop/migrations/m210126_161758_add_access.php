<?php

use yii\db\Migration;

/**
 * Class m210126_161758_add_access
 */
class m210126_161758_add_access extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('access', [
            'id'            => 'pk',
            'userId'        => 'int not null',
            'referenceType' => 'varchar(32) not null',
            'referenceId'   => 'int not null',
            'createdAt'     => 'timestamptz(0) not null default now()',
        ]);

        $this->addForeignKey('fkAccessUserId', 'access', 'userId', 'user', 'id', 'cascade', 'cascade');
        $this->createIndex('unique', 'access', [
            'userId',
            'referenceType',
            'referenceId'
        ], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('access');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_161758_add_access cannot be reverted.\n";

        return false;
    }
    */
}
