<?php


namespace app\modules\app\scripts\terminals;


use app\components\ExportQueryTrait;
use Throwable;
use yii\db\Expression;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class ExportTerminalsScript
 * @package app\modules\app\scripts\terminals
 */
class ExportTerminalsScript extends TerminalsListScript
{
    use ExportQueryTrait;

    const HEADERS = [
        'number' => 'Terminal #',
        'gameTitle',
        'groupName' => 'Group',
        'boardAssetNumber' => 'Board Asset #',
        'cabinetAssetNumber' => 'Cabinet Asset #',
        'licenseNumber' => 'License #',
        'machineType',
        'placementDate',
        'padlock',
        'doorLock',
        'referenceNumber',
        'refillDate',
        'location',
        'splitPercent' => 'Split',
        'terminalId',
        'cabinetType',
        'notes',
    ];

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     * @throws RangeNotSatisfiableHttpException
     * @throws Throwable
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        $this->arrayToCsv($this->_query->all(), self::HEADERS, true);

        return [];
    }

    /**
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        parent::onExecute();

        $this->_query
            ->select([
                'location' => 'location.name',
                'location.address',
                'location.city',
                'location.state',
                'location.zipCode',
                'location.county',
                'terminal.number',
                'lastActivity' => new Expression('[[terminal.lastActivityAt]]::timestamp'),
                'cabinetAssetNumber',
                'boardAssetNumber',
                'terminal.licenseNumber',
                'terminal.groupName',
                'cabinetType' => 'cabinetType.name',
                'machineType' => 'machineType.name',
                'gameTitle' => 'programName',
                'placementDate',
                'refillDate',
                'referenceNumber' => '[[terminal.referenceNumber]]',
                'splitPercent' => new Expression('coalesce([[terminal.splitPercent]], [[location.splitPercent]])'),
                'terminalId' => 'terminal.id',
                'notes',
                'number' => 'terminal.number',
                'padlock' => 'padlock.name',
                'doorLock' => 'doorLock.name'
            ])
            ->leftJoin('padlock', '[[padlock.id]] = [[terminal.padlockId]]')
            ->leftJoin('doorLock', '[[doorLock.id]] = [[terminal.doorLockId]]')
            ->asArray()
            ->offset(0)->limit(-1);
    }
}