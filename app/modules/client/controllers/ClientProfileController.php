<?php

namespace app\modules\client\controllers;

use app\models\Client;
use app\models\ClientSettings;
use app\models\PatternHttpException;
use app\models\User;
use app\modules\client\models\form\AvatarCompanyForm;
use app\modules\client\models\form\FormUpdateClient;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException as StaleObjectExceptionAlias;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Контроллер для редактирования профиля организации
 *
 * Class ClientProfileController
 * @package app\modules\client\controllers
 */
class ClientProfileController extends AppClientController
{

    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        if ($action->id === 'index') {

            $admin = User::findOne(Yii::$app->user->getId());
            $clientSettings = ClientSettings::findOne(['admin_id' => $admin->getId()]);
            if ($clientSettings && User::isUserAdminCompany($admin->getUsername())) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        } else {
            return parent::beforeAction($action);
        }
    }


    /**
     * @return string
     */
    public function actionIndex(): string
    {
        /** @var Client $client */
        $client = Client::find()
            ->leftJoin('client_settings','`client_settings`.`client_id` = `client`.`id`')
            ->andWhere(['client_settings.admin_id' => Yii::$app->user->getId()])
            ->one();
        $model = new FormUpdateClient($client);
        $avatarForm = new AvatarCompanyForm($client->getId());

        return $this->render('index', [
            'client' => $client,
            'model' => $model,
            'avatarForm' => $avatarForm
        ]);
    }


    /**
     * @return array|bool
     * @throws Exception
     * @throws StaleObjectExceptionAlias
     * @throws \Throwable
     */
    public function actionLoadAvatarImage()
    {
        if (Yii::$app->request->isAjax) {

            $admin = User::findOne(Yii::$app->user->getId());
            $clientSettings = ClientSettings::findOne(['admin_id' => $admin->getId()]);
            $avatarForm = new AvatarCompanyForm($clientSettings->getClientId());

            if (isset($_POST['imageMin'])) {

                if ($avatarForm->loadMinImage()) {

                    $client = Client::findOne($clientSettings->getClientId());

                    $response = [
                        'success' => true, 'client' => $client,
                        'renderAjax' => $this->renderAjax('ajax_view', [
                            'client' => $client, 'model' => new FormUpdateClient($client),
                            'avatarForm' => new AvatarCompanyForm($client->getId())
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }

            } else {

                if ($result = $avatarForm->loadMaxImage()) {

                    $response = $result;
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
                return false;
            }
        }
        return false;
    }


    /**
     * @return array|bool
     */
    public function actionGetDataAvatar()
    {
        if (Yii::$app->request->isAjax) {

            $admin = User::findOne(Yii::$app->user->getId());
            $clientSettings = ClientSettings::findOne(['admin_id' => $admin->getId()]);
            $response = ['path_max' => '/upload/company-' . $clientSettings->getClientId() . '/avatar/' . $clientSettings->getAvatarMaxImage()];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;

        }
        return false;
    }


    /**
     * @return bool
     */
    public function actionDeleteUnusedImage(): bool
    {
        if (Yii::$app->request->isAjax) {

            $admin = User::findOne(Yii::$app->user->getId());
            $clientSettings = ClientSettings::findOne(['admin_id' => $admin->getId()]);
            $avatarForm = new AvatarCompanyForm($clientSettings->getClientId());

            if (isset($_POST['imageMax']) && $avatarForm->deleteUnusedImage()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array|bool
     * @throws StaleObjectExceptionAlias
     * @throws \Throwable
     */
    public function actionDeleteAvatar()
    {
        if (Yii::$app->request->isAjax) {

            $admin = User::findOne(Yii::$app->user->getId());
            $clientSettings = ClientSettings::findOne(['admin_id' => $admin->getId()]);
            $avatarForm = new AvatarCompanyForm($clientSettings->getClientId());

            if ($avatarForm->deleteOldAvatarImages()) {

                $client = Client::findOne($clientSettings->getClientId());

                $response = [
                    'success' => true, 'client' => $client,
                    'renderAjax' => $this->renderAjax('ajax_view', [
                        'client' => $client, 'model' => new FormUpdateClient($client),
                        'avatarForm' => new AvatarCompanyForm($client->getId())
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     * @throws StaleObjectExceptionAlias
     * @throws \Throwable
     */
    public function actionUpdateProfile(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $client = Client::findOne($id);
            $model = new FormUpdateClient($client);

            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    if ($model->update()) {

                        $response = [
                            'success' => true, 'client' => $client,
                            'renderAjax' => $this->renderAjax('ajax_view', [
                                'client' => $client, 'model' => $model,
                                'avatarForm' => new AvatarCompanyForm($id)
                            ]),
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }
            }
        }
        return false;
    }

}
