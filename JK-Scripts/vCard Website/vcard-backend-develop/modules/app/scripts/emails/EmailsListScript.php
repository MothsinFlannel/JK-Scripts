<?php


namespace app\modules\app\scripts\emails;


use app\models\NotificationEmail;
use app\models\NotificationEmailQuery;
use vr\core\ArrayHelper;
use vr\core\PagedListScript;
use vr\core\validators\NestedValidator;

/**
 * Class EmailsListScript
 * @package app\modules\app\emails
 */
class EmailsListScript extends PagedListScript
{
    /**
     * @var int|null
     */
    public ?int $locationId = null;

    /**
     * @var array|object
     */
    public array|object $filters = [];

    /**
     * @var NotificationEmailQuery
     */
    private NotificationEmailQuery $_query;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['locationId', 'required'],
            [
                'filters',
                NestedValidator::class,
                'rules' => [

                ],
                'objectify' => true,
            ]
        ];
    }

    /**
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array|string[]
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'count' => (int)$this->_query->count(),
            'results' => ArrayHelper::getColumn($this->_query->all(), function (NotificationEmail $email) {
                return $email->toArray();
            })
        ];
    }

    /**
     *
     */
    protected function onExecute(): void
    {
        $this->_query = NotificationEmail::find()
            ->andWhere(['locationId' => $this->locationId])
            ->orderBy('email')
            ->offset($this->offset)->limit($this->limit);
    }
}