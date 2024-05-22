<?php

use vr\core\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m201210_180208_merge_down_terminals
 */
class m201210_180208_merge_down_terminals extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = ActiveRecord::find()
            ->select([
                'number',
                'locationId'
            ])
            ->from('terminal')
            ->groupBy(['number', 'locationId'])
            ->asArray()
            ->having('count(*) > 1');

        foreach ($query->each() as $duplicate) {
            $number     = ArrayHelper::getValue($duplicate, 'number');
            $locationId = ArrayHelper::getValue($duplicate, 'locationId');

            $terminals = ActiveRecord::find()->from('terminal')
                ->select('id')
                ->andWhere([
                    'number'     => $number,
                    'locationId' => $locationId,
                ])
                ->andWhere('[[cabinetTypeId]] is null and [[programName]] is null and [[machineTypeId]] is null')
                ->asArray()
                ->orderBy('[[createdAt]] desc, id desc')
                ->column() ?: [];

            $terminals = implode(',', $terminals);
            Console::output("Location: {$locationId}, Terminal #{$number}, To delete: {$terminals}");

            if ($terminals) {
                $this->delete('terminal', ['id' => $terminals]);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201210_180208_merge_down_terminals cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201210_180208_merge_down_terminals cannot be reverted.\n";

        return false;
    }
    */
}
