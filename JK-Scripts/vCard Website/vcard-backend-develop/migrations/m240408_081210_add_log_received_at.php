<?php

use yii\db\Migration;

/**
 * Class m240408_081210_add_log_received_at
 */
class m240408_081210_add_log_received_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('log', 'receivedAt', 'datetime not null default now()');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('log', 'receivedAt');
    }
}
