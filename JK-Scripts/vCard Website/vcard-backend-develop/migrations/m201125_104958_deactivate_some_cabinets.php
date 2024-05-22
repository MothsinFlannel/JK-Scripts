<?php

use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Class m201125_104958_deactivate_some_cabinets
 */
class m201125_104958_deactivate_some_cabinets extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('cabinetType', ['isActive' => false]);
        $this->dropIndex('cabinetTypeName', 'cabinetType');

        $query = ActiveRecord::find()
            ->from('cabinetType')
            ->asArray();

        foreach ($query->each() as $item) {
            $this->update(
                'cabinetType',
                ['name' => strtoupper($item['name'])],
                ['id' => $item['id']]
            );
        }

        $this->update(
            'cabinetType',
            ['name' => 'BANILLA METAL 32VER'],
            ['name' => 'BANILLA METAL 32']
        );

        $this->insert(
            'cabinetType',
            ['name' => 'CH METAL 32VER']
        );

        $this->insert(
            'cabinetType',
            ['name' => 'CH METAL 43VER']
        );

        $this->insert(
            'cabinetType',
            ['name' => 'CH METAL DUAL']
        );

        $this->insert(
            'cabinetType',
            ['name' => 'IGS']
        );

        $this->insert(
            'cabinetType',
            ['name' => 'NIGS']
        );

        $this->insert(
            'cabinetType',
            ['name' => 'SW']
        );

        $this->insert(
            'cabinetType',
            ['name' => 'WOOD SU']
        );

        $types = [
            'BANILLA METAL 32VER',
            'BANILLA METAL DUAL',
            'CH METAL 32VER',
            'CH METAL 43VER',
            'CH METAL DUAL',
            'CJH',
            'DUAL',
            'IGS',
            'NIGS',
            'SW',
            'WOOD SD',
            'WOOD SU',
        ];

        foreach ($types as $type) {
            $cab = ActiveRecord::find()
                ->from('cabinetType')
                ->andWhere(['name' => $type])
                ->asArray()
                ->one();

            if (!$cab) {
                continue;
            }

            $this->update('cabinetType', [
                'isActive' => true
            ], [
                'id' => $cab['id']
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_104958_deactivate_some_cabinets cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_104958_deactivate_some_cabinets cannot be reverted.\n";

        return false;
    }
    */
}
