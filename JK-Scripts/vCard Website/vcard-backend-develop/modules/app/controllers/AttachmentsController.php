<?php

namespace app\modules\app\controllers;

use app\models\ext\Attachment;
use app\models\ext\User;
use app\models\Location;
use app\modules\app\components\Controller;
use app\modules\app\scripts\attachments\DeleteAttachmentScript;
use app\modules\app\scripts\attachments\UpsertAttachmentScript;
use Faker\Factory;
use vr\api\components\VerboseException;
use vr\core\ErrorsException;
use Yii;

/**
 *
 */
class AttachmentsController extends Controller
{
    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionCreate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $faker = Factory::create();

            return [
                'accessToken' => @$user->accessToken,
                'attachment' => [
                    'referenceType' => Location::tableName(),
                    'referenceId' => null,
                    'title' => $faker->sentence,
                    'type' => null,
                ],
                'file' => base64_encode(file_get_contents('https://placebear.com/200/300')),
            ];
        });

        return (new UpsertAttachmentScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionUpdate(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $attachment = Attachment::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$attachment->id,
                'attachment' => $attachment->toArray([], ['title']),
            ];
        });

        return (new UpsertAttachmentScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }

    /**
     * @return array
     * @throws VerboseException
     * @throws ErrorsException
     */
    public function actionDelete(): array
    {
        $this->checkInputParams(function () {
            $user = User::find()->active()->one();
            $attachment = Attachment::find()->random()->one();

            return [
                'accessToken' => @$user->accessToken,
                'id' => @$attachment->id,
            ];
        });

        return (new DeleteAttachmentScript(Yii::$app->request->bodyParams))->execute()->toArray();
    }
}