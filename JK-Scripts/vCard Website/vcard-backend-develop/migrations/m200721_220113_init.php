<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use vr\core\ArrayHelper;
use yii\db\Migration;

/**
 * Class m200721_220113_init
 */
class m200721_220113_init extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $this->execute("drop type if exists role");
        $this->execute("create type role as enum('admin', 'operator', 'manager', 'technician')");

        $this->execute("drop type if exists invoiceStatus");
        $this->execute("create type invoiceStatus as enum('unpaid', 'paid')");

        $this->createTable('user', [
            'id'          => 'pk',
            'accessToken' => 'varchar(32) not null',
            'email'       => 'varchar(128) not null',
            'fullName'    => 'varchar(128) not null',
            'phone'       => 'varchar(128)',
            'role'        => 'role not null',
            'isActive'    => 'boolean default true',
            'createdAt'   => 'timestamptz(0) not null default now()',
            'password'    => 'varchar(64)',
            'oid'         => 'varchar(24)'
        ]);

        $this->createIndex('userEmail', 'user', 'email', true);
        $this->createIndex('userOid', 'user', 'oid', true);

        $this->createTable('location', [
            'id'                  => 'pk',
            'name'                => 'varchar(128) not null',
            'contactPhone'        => 'varchar(32)',
            'timezone'            => 'varchar(32)',
            'address'             => 'varchar(256) not null',
            'city'                => 'varchar(128) not null',
            'state'               => 'varchar(64) not null',
            'zipCode'             => 'varchar(16) not null',
            'splitPercent'        => 'double not null',
            'flatFee'             => 'double not null default 0',
            'serial'              => 'varchar(32)',
            'maxOfflineHours'     => 'tinyint',
            'maxAddCreditsAmount' => 'double',
            'maxTerminalNumber'   => 'int',
            'enableAddCredits'    => 'boolean not null default false',
            'enableRedemption'    => 'boolean not null default false',
            'enableCreditsReplay' => 'boolean not null default false',
            'lastActivityAt'      => 'timestamptz(0) not null default now()',
            'isActive'            => 'boolean not null default true',
            'createdAt'           => 'timestamptz(0) not null default now()',
            'oid'                 => 'varchar(24)'
        ]);

        $this->createIndex('locationSerial', 'location', 'serial', true);
        $this->createIndex('locationOid', 'location', 'oid', true);

        $this->createTable('staff', [
            'locationId' => 'int not null',
            'userId'     => 'int not null',
        ]);

        $this->addForeignKey('fkStaffLocationId', 'staff', 'locationId', 'location', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fkStaffUserId', 'staff', 'userId', 'user', 'id', 'cascade', 'cascade');
        $this->addPrimaryKey('primary', 'staff', ['locationId', 'userId']);

        $this->createTable('cabinetType', [
            'id'   => 'pk',
            'name' => 'varchar(64)',
        ]);

        $this->createIndex('cabinetTypeName', 'cabinetType', 'name', true);

        $this->insert('cabinetType', ['name' => 'Single Screen Wood']);
        $this->insert('cabinetType', ['name' => 'Dual Screen Wood']);
        $this->insert('cabinetType', ['name' => 'Vertical Screen Wood']);
        $this->insert('cabinetType', ['name' => 'Single Screen Metal']);
        $this->insert('cabinetType', ['name' => 'Dual Screen Metal']);

        $this->createTable('clerk', [
            'id'         => 'pk',
            'locationId' => 'int',
            'fullName'   => 'varchar(32) not null',
            'pin'        => 'varchar(16) not null',
            'isManager'  => 'boolean not null default false',
            'createdAt'  => 'timestamptz(0) not null default now()',
        ]);

        $this->addForeignKey('fkClerkLocationId', 'clerk', 'locationId', 'location', 'id', 'cascade', 'cascade');

        $this->createTable('terminal', [
            'id'             => 'pk',
            'locationId'     => 'int not null',
            'cabinetTypeId'  => 'int null default null',
            'number'         => 'int not null',
            'programName'    => 'varchar(64)',
            'splitPercent'   => 'double null default null',
            'flatFee'        => 'double null default null',
            'groupName'      => 'varchar(64)',
            'lastActivityAt' => 'timestamp null default null',
            'createdAt'      => 'timestamptz(0) not null default now()',
            'oid'            => 'varchar(24)'
        ]);

        $this->addForeignKey('fkTerminalLocationId', 'terminal', 'locationId', 'location', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fkTerminalCabinetTypeId', 'terminal', 'cabinetTypeId', 'cabinetType', 'id', 'cascade', 'cascade');

        $this->createIndex('terminalOid', 'terminal', 'oid', true);

        $this->createTable('category', [
            'id'         => 'pk',
            'locationId' => 'int',
            'name'       => 'varchar(32) not null',
        ]);

        $this->addForeignKey('fkCategoryLocationId', 'category', 'locationId', 'location', 'id', 'cascade', 'cascade');
        $this->createIndex('categoryName', 'category', ['name', 'locationId'], true);

        $this->createTable('log', [
            'serial'    => 'varchar(32) not null',
            'terminal'  => 'int not null',
            'moneyIn'   => 'double not null default 0',
            'moneyOut'  => 'double not null default 0',
            'createdAt' => 'timestamptz(0) not null',
        ]);

        $this->addPrimaryKey('pk', 'log', ['createdAt', 'serial', 'terminal']);

        $this->createTable('convolution', [
            'id'               => 'pk',
            'locationId'       => 'int not null',
            'terminal'         => 'int not null',
            'date'             => 'date not null',
            'moneyIn'          => 'double not null',
            'moneyOut'         => 'double not null',
            'percentageProfit' => 'double not null',
            'flatFee'          => 'double not null default 0',
        ]);

        $this->addForeignKey('fkConvolutionLocationId', 'convolution', 'locationId', 'location', 'id', 'cascade', 'cascade');
        $this->createIndex('unique', 'convolution', ['locationId', 'date', 'terminal'], true);

        $this->createTable('invoice', [
            'id'         => 'pk',
            'locationId' => 'int not null',
            'amount'     => 'double not null',
            'since'      => 'date not null',
            'until'      => 'date not null',
            'status'     => 'invoiceStatus',
            'token'      => 'varchar(64)',
            'createdAt'  => 'timestamptz(0) not null default now()',
            'oid'        => 'varchar(24)'
        ]);

        $this->createIndex('invoiceToken', 'invoice', 'token', true);
        $this->addForeignKey('fkInvoiceLocationId', 'invoice', 'locationId', 'location', 'id', 'cascade', 'cascade');

        $this->createTable('payment', [
            'id'        => 'pk',
            'invoiceId' => 'int not null',
            'amount'    => 'double not null',
            'paidOn'    => 'date',
            'createdAt' => 'timestamptz(0) not null default now()',
        ]);

        $this->addForeignKey('fkPaymentInvoiceId', 'payment', 'invoiceId', 'invoice', 'id', 'cascade', 'cascade');

        $this->createTable('redemption', [
            'id'               => 'pk',
            'locationId'       => 'int not null',
            'clerk'            => 'varchar(32) null default null',
            'terminal'         => 'int null default null',
            'replayTerminal'   => 'int null default null',
            'totalAmount'      => 'double not null',
            'replayAmount'     => 'double not null default 0',
            'redemptionAmount' => 'double not null default 0',
            'createdAt'        => 'timestamptz(0) not null',
        ]);

        $this->addForeignKey('fkRedemptionLocationId', 'redemption', 'locationId', 'location', 'id', 'cascade', 'cascade');

        $this->createTable('redemptionItem', [
            'id'           => 'pk',
            'redemptionId' => 'int not null',
            'category'     => 'varchar(32) not null',
            'amount'       => 'double not null',
        ]);

        $this->addForeignKey('fkRedemptionItemRedemptionId', 'redemptionItem', 'redemptionId', 'redemption', 'id', 'cascade', 'cascade');

        $this->createTable('sale', [
            'id'         => 'pk',
            'locationId' => 'int not null',
            'terminal'   => 'int null default null',
            'clerk'      => 'varchar(32) null default null',
            'amount'     => 'double not null',
            'createdAt'  => 'timestamptz(0) not null default now()',
        ]);

        $this->addForeignKey('fkSaleLocationId', 'sale', 'locationId', 'location', 'id', 'cascade', 'cascade');

        $this->createTable('notificationEmail', [
            'id'         => 'pk',
            'locationId' => 'int not null',
            'email'      => 'varchar(128) not null',
            'createdAt'  => 'timestamptz(0) not null default now()',
        ]);

        $this->addForeignKey('fkNotificationEmailLocationId', 'notificationEmail', 'locationId', 'location', 'id', 'cascade', 'cascade');

        $this->createTable('route', [
            'id'        => 'pk',
            'name'      => 'varchar(64)',
            'createdAt' => 'timestamptz(0) not null default now()',
        ]);

        $this->addColumn('location', 'routeId', 'int null default null');
        $this->addForeignKey('fkLocationRouteId', 'location', 'routeId', 'route', 'id', 'set null', 'cascade');

        /////////////////////////////////////////
        ///         Generate some data
        ///

        $this->insert('user', [
            'email'       => ArrayHelper::getValue(Yii::$app->params, 'adminEmail'),
            'accessToken' => '9AptA5XcnVvU7R8L2P3Bd9EL5gRyfALJ',
            'fullName'    => 'Admin',
            'phone'       => null,
            'role'        => 'admin',
            'password'    => Yii::$app->security->generatePasswordHash('password'),
            'isActive'    => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200721_220113_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200721_220113_init cannot be reverted.\n";

        return false;
    }
    */
}
