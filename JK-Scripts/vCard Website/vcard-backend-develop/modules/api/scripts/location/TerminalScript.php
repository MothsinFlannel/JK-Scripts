<?php


namespace app\modules\api\scripts\location;


use app\components\Script;
use app\models\ext\Convolution;
use app\models\ext\Location;
use app\models\ext\Log;
use app\models\ext\Terminal;
use app\scripts\convolutions\GetConvolutionScript;
use DateTime;
use Throwable;
use vr\core\ErrorsException;
use yii\db\Expression;

/**
 * Class TerminalScript
 * @package app\modules\api\scripts\location
 * @property Convolution $entity
 */
class TerminalScript extends Script
{
    /**
     * @var string|null
     */
    public ?string $serial = null;

    /**
     * @var string|null
     */
    public ?string $terminal = null;

    /**
     * @var float
     */
    public float $moneyIn = 0;

    /**
     * @var float
     */
    public float $moneyOut = 0;

    /**
     * @var string|null
     */
    public ?string $timestamp = null;

    /**
     * @var Convolution|null
     */
    private ?Convolution $_entity = null;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['serial', 'terminal', 'moneyIn', 'moneyOut', 'timestamp'], 'required'],
            [['terminal', 'moneyIn', 'moneyOut'], 'number']
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'convolution' => $this->_entity,
        ];
    }

    /**
     * @return Convolution|null
     */
    public function getEntity(): ?Convolution
    {
        return $this->_entity;
    }

    /**
     * @throws ErrorsException
     * @throws Throwable
     */
    protected function onExecute(): void
    {
        $location = Location::find()
            ->forUpdate()
            ->andWhere(['serial' => $this->serial])
            ->one();

        $location->lastActivityAt = new Expression('now()');

        if (!$location->save(false, ['lastActivityAt'])) {
            throw new ErrorsException($location->errors);
        }

        if (!$this->moneyIn && !$this->moneyOut) {
            return;
        }

        $params = [
            'createdAt' => $this->timestamp,
            'serial' => $this->serial,
            'terminal' => $this->terminal,
            'isLive' => $location->isLive,
        ];

        if (Log::findOne($params)) {
            return;
        }

        $log = new Log($params);
        $log->moneyIn = $this->moneyIn;
        $log->moneyOut = $this->moneyOut;
        $log->setAttribute('receivedAt', (new \DateTime('now', new \DateTimeZone('America/New_York')))->format('Y-m-d H:i:s'));

        if (!$log->save()) {
            throw new ErrorsException($log->errors);
        }

        $terminal = Terminal::find()->andWhere([
            'locationId' => $location->id,
            'number' => $this->terminal
        ])->one();

        if (!$terminal) {
            $terminal = new Terminal([
                'locationId' => $location->id,
                'number' => $this->terminal,
            ]);
        }

        $terminal->lastActivityAt = new Expression('now()');
        if (!$terminal->save()) {
            throw new ErrorsException($terminal->errors);
        }

        $this->fetchConvolution($location, $log);
    }

    /**
     * @param Location $location
     * @param Log $log
     * @throws ErrorsException
     * @throws Throwable
     */
    private function fetchConvolution(Location $location, Log $log): void
    {
        $dateTime = new DateTime($this->timestamp);

        $this->_entity = (new GetConvolutionScript([
            'terminal' => $this->terminal,
            'locationId' => $location->id,
            'date' => $dateTime->format('Y-m-d')
        ]))->execute()->entity;

        $this->_entity->moneyOut += round($log->moneyOut / 100.0, 2);
        $this->_entity->moneyIn += round($log->moneyIn / 100.0, 2);
        $this->_entity->percentageProfit += round(($log->moneyIn / 100.0 - $log->moneyOut / 100.0) * $location->splitPercent / 100.0, 2);
        $this->_entity->lastLogAt = $this->timestamp;

        if (!$this->_entity->save()) {
            throw new ErrorsException($this->_entity->errors);
        }
    }
}