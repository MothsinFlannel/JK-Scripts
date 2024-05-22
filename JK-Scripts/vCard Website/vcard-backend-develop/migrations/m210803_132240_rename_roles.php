<?php

use yii\db\Migration;

/**
 * Class m210803_132240_rename_roles
 */
class m210803_132240_rename_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->update('user', ['role' => 'location'], ['role' => 'manager']);
        $this->update('user', ['role' => 'routeman'], ['role' => 'operator']);

        $this->execute('ALTER TYPE role RENAME TO old_role');
        $this->execute("create type role as enum('admin', 'location', 'routeman', 'technician')");
        $this->execute('ALTER TABLE "user" ALTER COLUMN role TYPE role USING role::text::role');
        $this->execute('DROP TYPE old_role');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_132240_rename_roles cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210803_132240_rename_roles cannot be reverted.\n";

        return false;
    }
    */
}
