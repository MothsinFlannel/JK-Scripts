<?php

use yii\db\Migration;

/**
 * Class m210215_232810_add_statements
 */
class m210215_232810_add_statements extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('statement', [
            'id'        => 'pk',
            'companyId' => 'int not null',
            'isSent'    => 'boolean not null default false',
            'createdAt' => 'timestamptz(0) not null default now()',
        ]);

        $this->addColumn('invoice', 'statementId', 'int');

        $this->addForeignKey('fkInvoiceStatementId', 'invoice', 'statementId', 'statement', 'id', 'set null', 'cascade');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210215_232810_add_statements cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210215_232810_add_statements cannot be reverted.\n";

        return false;
    }
    */
}
