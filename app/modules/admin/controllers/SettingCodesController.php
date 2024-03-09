<?php

namespace app\modules\admin\controllers;

use app\models\ClientCodes;
use app\models\ClientCodeTypes;
use app\models\PatternHttpException;
use app\models\User;
use Exception;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

/**
 * Контроллер с методами для настройки кодов доступа для регистрации пользователей
 *
 * Class SettingCodesController
 * @package app\modules\admin\controllers
 */
class SettingCodesController extends AppAdminController
{

    public $layout = '@app/modules/admin/views/layouts/users';

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        if (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {
            // ОТКЛЮЧАЕМ CSRF
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action);
        }

        PatternHttpException::noAccess();
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $user = User::findOne(Yii::$app->user->getId());
        $client = $user->clientUser->client;
        $clientCodes = $client->codes;

        $codeRegistrationForSimpleUser = null;
        $codeRegistrationForTracker = null;
        $codeRegistrationForManager = null;
        $codeRegistrationForExpert = null;
        $codeRegistrationForContractor = null;

        if (count($clientCodes) > 0) {
            foreach ($clientCodes as $clientCode) {
                if ($clientCode->getType() === ClientCodeTypes::REGISTRATION_CODE_FOR_SIMPLE_USER) {
                    $codeRegistrationForSimpleUser = $clientCode;
                } elseif ($clientCode->getType() === ClientCodeTypes::REGISTRATION_CODE_FOR_TRACKER) {
                    $codeRegistrationForTracker = $clientCode;
                } elseif ($clientCode->getType() === ClientCodeTypes::REGISTRATION_CODE_FOR_MANAGER) {
                    $codeRegistrationForManager = $clientCode;
                } elseif ($clientCode->getType() === ClientCodeTypes::REGISTRATION_CODE_FOR_EXPERT) {
                    $codeRegistrationForExpert = $clientCode;
                } elseif ($clientCode->getType() === ClientCodeTypes::REGISTRATION_CODE_FOR_CONTRACTOR) {
                    $codeRegistrationForContractor = $clientCode;
                }
            }
        }

        return $this->render('index',[
            'client' => $client,
            'clientCodes' => $clientCodes,
            'codeRegistrationForSimpleUser' => $codeRegistrationForSimpleUser,
            'codeRegistrationForTracker' => $codeRegistrationForTracker,
            'codeRegistrationForManager' => $codeRegistrationForManager,
            'codeRegistrationForExpert' => $codeRegistrationForExpert,
            'codeRegistrationForContractor' => $codeRegistrationForContractor,
        ]);
    }

    public function actionGeneration(int $type)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = User::findOne(Yii::$app->user->getId());
            $client = $user->clientUser->client;
            $clientCode = ClientCodes::getInstance($client->getId(), $type);
            $generationCode = Yii::$app->security->generateRandomString();
            $clientCode->setCode($generationCode);
            $clientCode->save();

            $transaction->commit();
        } catch (Exception $exception) {
            $transaction->rollBack();
            return false;
        }

        return $this->redirect('index');
    }
}