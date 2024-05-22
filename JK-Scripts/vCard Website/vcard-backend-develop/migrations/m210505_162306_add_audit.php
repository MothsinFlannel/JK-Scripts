<?php

use yii\db\Migration;

/**
 * Class m210505_162306_add_audit
 */
class m210505_162306_add_audit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('audit', [
            'id'         => 'pk',
            'entity'     => 'varchar(128) not null',
            'identifier' => 'int not null',
            'attribute'  => 'varchar(128) not null',
            'value'      => 'text',
            'createdAt'  => 'timestamptz(0) not null default now()',
        ]);

        $this->createIndex('idxAudit', 'audit', ['entity', 'identifier']);
        $this->createIndex('idxCreatedAt', 'audit', 'createdAt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('audit');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210505_162306_add_audit cannot be reverted.\n";

        return false;
    }
    */
}
