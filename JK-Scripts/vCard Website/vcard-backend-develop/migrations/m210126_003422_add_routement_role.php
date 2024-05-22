<?php

use yii\db\Migration;

/**
 * Class m210126_003422_add_routement_role
 */
class m210126_003422_add_routement_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TYPE role ADD VALUE \'routeman\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210126_003422_add_routement_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_003422_add_routement_role cannot be reverted.\n";

        return false;
    }
    */
}
