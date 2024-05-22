<?php


namespace app\scripts\convolutions;


use app\components\Script;
use app\models\ext\Convolution;
use app\models\ext\Location;
use DateTime;
use DateTimeZone;
use Exception;
use vr\core\ErrorsException;
use vr\core\validators\ExistValidator;

/**
 * Class GetConvolutionScript
 * @package app\scripts\convolutions
 * @property-read ?Convolution $entity
 */
class GetConvolutionScript extends Script
{
    /**
     * @var int
     */
    public int $locationId;

    /**
     * @var int
     */
    public int $terminal;

    /**
     * @var string
     */
    public string $date;

    /**
     * @var Convolution|null
     */
    private ?Convolution $_entity;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['locationId', 'terminal', 'date'], 'required'],
            ['terminal', 'number'],
            ['locationId', ExistValidator::class, 'targetClass' => Location::class, 'targetAttribute' => 'id'],
            //            ['date', 'date', 'format' => 'php:Y-m-d'],
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
            'convolution' => $this->_entity->toArray(),
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
     *
     * @throws Exception
     */
    protected function onExecute()
    {
        $location   = Location::findOne($this->locationId);
        $this->date = (new DateTime($this->date, new DateTimeZone($location->timezone)))->format('Y-m-d');

        $params = [
            'locationId' => $this->locationId,
            'date' => $this->date,
            'terminal' => $this->terminal,
            'isLive' => $location->isLive
        ];

        $this->_entity = Convolution::find()->forUpdate()->andWhere($params)->one();

        if (!$this->_entity) {
            $this->_entity                   = new Convolution($params);
            $this->_entity->flatFee          = 0;
            $this->_entity->moneyIn          = 0;
            $this->_entity->moneyOut         = 0;
            $this->_entity->percentageProfit = 0;

            if (!$this->_entity->save() || !$this->_entity->refresh()) {
                throw new ErrorsException($this->_entity->errors);
            }
        }
    }
}