<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m210506_080526_add_initial_audit
 */
class m210506_080526_add_initial_audit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->from('terminal')
            ->asArray();

        foreach ($query->each() as $terminal) {
            $this->insert('audit', [
                'entity'     => 'terminal',
                'identifier' => $terminal['id'],
                'attribute'  => 'locationId',
                'value'      => $terminal['locationId'],
                'createdAt'  => $terminal['createdAt'],
            ]);
        }

        $query = ActiveRecord::find()
            ->from('terminal')
            ->andWhere('[[warehouseId]] is not null')
            ->asArray();

        foreach ($query->each() as $terminal) {
            $this->insert('audit', [
                'entity'     => 'terminal',
                'identifier' => $terminal['id'],
                'attribute'  => 'warehouseId',
                'value'      => $terminal['warehouseId'],
                'createdAt'  => $terminal['archivedAt'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210506_080526_add_initial_audit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210506_080526_add_initial_audit cannot be reverted.\n";

        return false;
    }
    */
}
