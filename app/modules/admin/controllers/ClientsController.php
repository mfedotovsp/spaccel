<?php


namespace app\modules\admin\controllers;

use app\models\Client;
use app\models\ClientActivation;
use app\models\ClientSettings;
use app\models\ClientUser;
use app\models\ConversationDevelopment;
use app\models\CustomerManager;
use app\models\CustomerWishList;
use app\models\PatternHttpException;
use app\models\User;
use app\modules\admin\models\form\FormChangeAccessWishList;
use app\modules\admin\models\form\FormCreateAdminCompany;
use app\modules\admin\models\form\FormCreateClient;
use yii\base\Exception;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;
use Yii;

/**
 * Контроллер с методами для настройки и получения информации о клиентах (организациях)
 * Доступ только для главного админа Spaccel
 *
 * Class ClientsController
 * @package app\modules\admin\controllers
 */
class ClientsController extends AppAdminController
{

    //TODO: Написать консольную команду,
    // которая будет блокировать клиента (изменять его статус),
    // если время тарифа закончилось. Команда должна запускаться по крону.
    // Пока это не реализвано, изменять нужно в ручную в интерфейсе админки.

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        if (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {
            return parent::beforeAction($action);
        }

        PatternHttpException::noAccess();
    }


    /**
     * Старница со списком клиентов (организаций)
     * @return string
     */
    public function actionIndex(): string
    {
        /** @var Client $clientSpaccel */
        $clientSpaccel = Client::find()
            ->leftJoin('client_user', '`client_user`.`client_id` = `client`.`id`')
            ->leftJoin('user', '`user`.`id` = `client_user`.`user_id`')
            ->andWhere(['user.role' => User::ROLE_MAIN_ADMIN])
            ->one();

        $countUsersOnPage = 20;
        $query = Client::find()->andWhere(['!=', 'id', $clientSpaccel->getId()]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $clients = $query->offset($pages->offset)->limit($countUsersOnPage)->all();

        return $this->render('index', [
            'pages' => $pages,
            'clients' => $clients,
            'clientSpaccel' => $clientSpaccel
        ]);
    }


    /**
     * Получить список активных
     * менеджеров по клиентам
     *
     * @param int $clientId
     * @return array|bool
     */
    public function actionGetListManagers(int $clientId)
    {
        $managers = User::findAll(['role' => User::ROLE_MANAGER, 'status' => User::STATUS_ACTIVE, 'confirm' => User::CONFIRM]);

        $customerManager = CustomerManager::find()->andWhere(['client_id' => $clientId])->orderBy(['created_at' => SORT_DESC])->one();
        if (!$customerManager) {
            $customerManager = new CustomerManager();
            $customerManager->setClientId($clientId);
        }

        if (Yii::$app->request->isAjax){
            $response = ['renderAjax' => $this->renderAjax('list-managers', ['customerManager' => $customerManager, 'managers' => $managers])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Назначить менеджера организации
     *
     * @return array|bool
     */
    public function actionAddManager()
    {
        if (Yii::$app->request->isAjax && $_POST['CustomerManager']) {
            $userId = $_POST['CustomerManager']['user_id'];
            $clientId = $_POST['CustomerManager']['client_id'];

            /** @var CustomerManager $customerManager */
            $customerManager = CustomerManager::find()->andWhere(['client_id' => $clientId])->orderBy(['created_at' => SORT_DESC])->one();
            if ($customerManager) {
                if ($customerManager->getUserId() !== $userId) {
                    CustomerManager::addManager($clientId, $userId);
                    $response = [
                        'renderAjax' => $this->renderAjax('data_client', ['client' => Client::findOne($clientId)]),
                        'client_id' => $clientId,
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
                return false;
            }

            CustomerManager::addManager($clientId, $userId);
            $response = [
                'renderAjax' => $this->renderAjax('data_client', ['client' => Client::findOne($clientId)]),
                'client_id' => $clientId,
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Создание нового клиента (организации)
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate()
    {
        $formCreateAdminCompany = new FormCreateAdminCompany();
        $formCreateClient = new FormCreateClient();
        if ($formCreateAdminCompany->load(Yii::$app->request->post())) {
            $formCreateAdminCompany->setUsername($formCreateAdminCompany->getEmail());
            if ($formCreateAdminCompany->validate()) {
                foreach ($formCreateAdminCompany->attributes as $k => $value) {
                    $formCreateClient->adminCompany .= $k . 'abracadabraKey:' . $value . 'abracadabraValue';
                }
                return $this->render('create_2', [
                    'formCreateClient' => $formCreateClient
                ]);
            }
        }
        if ($formCreateClient->load(Yii::$app->request->post())) {

            $attributesAdminCompany = array();
            $data = explode('abracadabraValue', $formCreateClient->adminCompany);
            foreach ($data as $attribute) {
                $result = explode('abracadabraKey:', $attribute);
                $attributesAdminCompany[$result[0]] = $result[1];
            }
            $formCreateAdminCompany->attributes = $attributesAdminCompany;

            if ($client = $formCreateClient->create()) {
                if ($admin = $formCreateAdminCompany->create()) {
                    if (ClientUser::createRecord($client->getId(), $admin->getId())) {
                        if (ClientSettings::createRecord(['client_id' => $client->getId(), 'admin_id' => $admin->getId()])) {
                            return $this->redirect('/admin/clients/index');
                        }

                        ConversationDevelopment::deleteAll(['user_id' => $admin->getId()]);
                        ClientUser::deleteAll(['client_id' => $client->getId(), 'user_id' => $admin->getId()]);
                        User::deleteAll(['id' => $admin->getId()]);
                        ClientActivation::deleteAll(['client_id' => $client->getId()]);
                        Client::deleteAll(['id' => $client->getId()]);
                    } else {
                        ConversationDevelopment::deleteAll(['user_id' => $admin->getId()]);
                        User::deleteAll(['id' => $admin->getId()]);
                        ClientActivation::deleteAll(['client_id' => $client->getId()]);
                        Client::deleteAll(['id' => $client->getId()]);
                    }
                } else {
                    ClientActivation::deleteAll(['client_id' => $client->getId()]);
                    Client::deleteAll(['id' => $client->getId()]);
                }
            }

            return $this->render('create_1', [
                'formCreateAdminCompany' => $formCreateAdminCompany
            ]);
        }
        return $this->render('create_1', [
            'formCreateAdminCompany' => $formCreateAdminCompany
        ]);
    }


    /**
     * Изменение статуса организации (клиента)
     *
     * @param int $clientId
     * @return array|bool
     */
    public function actionChangeStatus(int $clientId)
    {
        if (Yii::$app->request->isAjax) {
            $client = Client::findOne($clientId);
            /** @var ClientActivation $clientActivationOld */
            $clientActivationOld = $client->findClientActivation();
            $status = $clientActivationOld->getStatus();
            $clientActivation = new ClientActivation();
            $clientActivation->setClientId($clientId);
            if ($status === ClientActivation::ACTIVE) {
                $clientActivation->setStatus(ClientActivation::NO_ACTIVE);
            } else {
                $clientActivation->setStatus(ClientActivation::ACTIVE);
            }
            if ($clientActivation->save()) {
                $response = [
                    'renderAjax' => $this->renderAjax('data_client', ['client' => $client]),
                    'client_id' => $clientId
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
     * @return string
     */
    public function actionView(int $id): string
    {
        $client = Client::findById($id);
        $clientSettings = ClientSettings::findOne(['client_id' => $id]);
        $formChangeAccessWishList = new FormChangeAccessWishList($client);

        return $this->render('view', [
            'client' => $client,
            'clientSettings' => $clientSettings,
            'formChangeAccessWishList' => $formChangeAccessWishList
        ]);
    }

    public function actionChangeAccessWishList(int $id)
    {
        $client = Client::findOne($id);
        $accessGeneralWishList = $client->isAccessGeneralWishList();
        $accessMyWishList = $client->isAccessMyWishList();

        if ($_POST['FormChangeAccessWishList']) {

            $user = User::findOne(Yii::$app->user->getId());
            $clientSpaccel = $user->mainAdmin->clientUser->client;
            $post_accessGeneralWishList = $_POST['FormChangeAccessWishList']['accessGeneralWishList'] === '1';
            $post_accessMyWishList =  $_POST['FormChangeAccessWishList']['accessMyWishList'] === '1';

            try {
                if ($accessGeneralWishList !== $post_accessGeneralWishList) {

                    /** @var CustomerWishList|null $record */
                    $record = CustomerWishList::find()
                        ->andWhere([
                            'client_id' => $clientSpaccel->getId(),
                            'customer_id' => $client->getId(),
                        ])
                        ->orderBy(['created_at' => SORT_DESC])
                        ->one();

                    if ($post_accessGeneralWishList) {
                        if ($record) {
                            $record->setDeletedAt(time());
                            $record->save();
                        }
                        $newRecord = new CustomerWishList();
                        $newRecord->setClientId($clientSpaccel->getId());
                        $newRecord->setCustomerId($client->getId());
                        $newRecord->save();
                    } else {
                        if ($record) {
                            $record->setDeletedAt(time());
                            $record->save();
                        }
                    }
                }
                if ($accessMyWishList !== $post_accessMyWishList) {

                    /** @var CustomerWishList|null $record */
                    $record = CustomerWishList::find()
                        ->andWhere([
                            'client_id' => $client->getId(),
                            'customer_id' => $clientSpaccel->getId(),
                        ])
                        ->orderBy(['created_at' => SORT_DESC])
                        ->one();

                    if ($post_accessMyWishList) {
                        if ($record) {
                            $record->setDeletedAt(time());
                            $record->save();
                        }
                        $newRecord = new CustomerWishList();
                        $newRecord->setClientId($client->getId());
                        $newRecord->setCustomerId($clientSpaccel->getId());
                        $newRecord->save();
                    } else {
                        if ($record) {
                            $record->setDeletedAt(time());
                            $record->save();
                        }
                    }
                }

                return $this->redirect(['view', 'id' => $id]);

            } catch (\Exception $exception) {
                return false;
            }
        }
    }

}