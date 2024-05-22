<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m230425_090832_add_audit_pos_records
 */
class m230425_090832_add_audit_pos_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->from('location')
            ->andWhere('serial is not null')
            ->asArray();

        foreach ($query->each() as $each) {
            $this->insert('audit', [
                'entity' => 'location',
                'identifier' => $each['id'],
                'attribute' => 'serial',
                'value' => $each['serial'],
                'createdAt' => $each['createdAt'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230425_090832_add_audit_pos_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230425_090832_add_audit_pos_records cannot be reverted.\n";

        return false;
    }
    */
}
