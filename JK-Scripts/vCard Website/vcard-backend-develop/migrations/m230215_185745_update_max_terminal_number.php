<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m230215_185745_update_max_terminal_number
 */
class m230215_185745_update_max_terminal_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->from('location')
            ->asArray();

        foreach ($query->all() as $location) {
            $max = ActiveRecord::find()->from('terminal')
                ->andWhere(['locationId' => $location['id']])
                ->max('number') ?: 99;

            $this->update('location', [
                'maxTerminalNumber' => $max,
            ], [
                'id' => $location['id'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230215_185745_update_max_terminal_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230215_185745_update_max_terminal_number cannot be reverted.\n";

        return false;
    }
    */
}
