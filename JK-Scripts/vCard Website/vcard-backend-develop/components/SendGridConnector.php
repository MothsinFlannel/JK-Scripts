<?php /** @noinspection SpellCheckingInspection */
/** @noinspection SpellCheckingInspection */
/** @noinspection SpellCheckingInspection */

/** @noinspection SpellCheckingInspection */

namespace app\components;

use app\models\ext\Location;
use app\models\ext\User;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use vr\api\components\Json;
use vr\core\ArrayHelper;
use Yii;
use yii\base\Component;
use yii\web\UrlManager;

/**
 *
 */
class SendGridConnector extends Component
{
    /**
     * @var string
     */
    public string $baseUrl = 'https://api.sendgrid.com/v3/';

    /**
     * @var string
     */
    public string $apiKey;

    /**
     * @var Client
     */
    private Client $_client;

    /**
     * @return void
     */
    public function init(): void
    {
        $this->_client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);

        parent::init();
    }

    /**
     * @param array<Location> $locations
     * @throws GuzzleException
     * @throws Exception
     */
    public function offlineLocations(array $locations): void
    {
        $timezone  = date_default_timezone_get();
        $timestamp = (new DateTime())->format('m/d/Y H:ia') . " ($timezone)";

        foreach ($locations as $location) {
            $this->_execute([
                'dynamic_template_data' => [
                    'timestamp' => $timestamp,
                    'name' => $location->name,
                    'address' => $location->address,
                    'lastSeenAt' => $location->lastActivityAt ? (new DateTime($location->lastActivityAt))->format('m/d/y H:ia') : 'n/a',
                    'contactName' => $location->contactName ?: 'n/a',
                    'contactPhone' => $location->contactPhone ?: 'n/a',
                ]
            ], 'd-3fe8098adde74deaa3f7317e14b05ea3');
        }
    }

    /**
     * @param array $payload
     * @param string $template
     * @return array|null
     * @throws GuzzleException
     * @throws Exception
     */
    private function _execute(array $payload, string $template): ?array
    {
        $payload += [
            'to' => ArrayHelper::getValue(Yii::$app->params, ['emails', 'service']),
        ];

        $json     = [
            'from' => [
                'email' => ArrayHelper::getValue(Yii::$app->params, ['emails', 'app']),
                'name' => 'vCard'
            ],
            'personalizations' => [$payload],
            'template_id' => $template
        ];
        $response = $this->_client->post('mail/send', [
            'json' => $json
        ]);

        return Json::decode($response->getBody()->getContents());
    }

    /**
     * @param Location $location
     * @param string $note
     * @return array|null
     * @throws GuzzleException
     */
    public function serviceRequest(Location $location, string $note): ?array
    {
        return $this->_execute([
            'dynamic_template_data' => [
                'body' => $note,
                'location' => $location->name,
                'address' => "$location->address, $location->city, $location->state $location->zipCode",
            ]
        ], 'd-c21e7af5abcd4da2b56deffdea3c4eee');
    }

    /**
     * @param User $user
     * @return array|null
     * @throws GuzzleException
     * @throws Exception
     */
    public function forgotPassword(User $user): ?array
    {
        $urlManager = new UrlManager([
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => ArrayHelper::getValue(Yii::$app->params, 'appBaseUrl')
        ]);

        return $this->_execute([
            'dynamic_template_data' => [
                'fullName' => $user->fullName,
                'url' => $urlManager->createAbsoluteUrl(['password/recovery', 'token' => $user->recoveryToken])
            ]
        ], 'd-315270199bdc4ee7b50ad2a9f2edce15');
    }
}