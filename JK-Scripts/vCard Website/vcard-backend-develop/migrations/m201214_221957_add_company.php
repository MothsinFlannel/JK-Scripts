<?php

use yii\db\Migration;

/**
 * Class m201214_221957_add_company
 */
class m201214_221957_add_company extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company', [
            'id'              => 'pk',
            'name'            => 'varchar(256) not null',
            'contactName'     => 'varchar(256)',
            'contactEmail'    => 'varchar(256)',
            'invoicingOnline' => 'boolean not null default true',
            'isActive'        => 'boolean not null default true',
            'createdAt'       => 'timestamptz(0) not null default now()',
        ]);

        $this->addColumn('location', 'companyId', 'int');
        $this->addForeignKey('fkLocationCompanyId', 'location', 'companyId', 'company', 'id', 'set null', 'set null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201214_221957_add_company cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201214_221957_add_company cannot be reverted.\n";

        return false;
    }
    */
}
