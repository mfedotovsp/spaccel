<?php


namespace app\modules\admin\controllers;

use app\models\Client;
use app\models\ClientSettings;
use app\models\ClientUser;
use app\models\ConversationAdmin;
use app\models\PatternHttpException;
use app\models\User;
use app\modules\admin\models\ConversationManager;
use Yii;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Контроллер с методами для редактирования и получения информации по пользователям системы
 *
 * Class UsersController
 * @package app\modules\admin\controllers
 */
class UsersController extends AppAdminController
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
        $currentUser = User::findOne(Yii::$app->user->getId());
        $currentClientUser = $currentUser->clientUser;

        if (in_array($action->id, ['index', 'admins', 'experts'])) {

            if (User::isUserDev($currentUser->getUsername()) || User::isUserMainAdmin($currentUser->getUsername())) {

                if ((int)Yii::$app->request->get('id')) {

                    $client = Client::findOne((int)Yii::$app->request->get('id'));

                    if ($currentClientUser->getClientId() === $client->getId()) {
                        return parent::beforeAction($action);
                    }

                    if ($client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                        return parent::beforeAction($action);
                    }

                    PatternHttpException::noAccess();
                }

                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif (in_array($action->id, ['status-update', 'add-admin'])) {

            if (User::isUserDev($currentUser->getUsername()) || User::isUserMainAdmin($currentUser->getUsername())) {
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'group') {

            $user = User::findOne((int)Yii::$app->request->get('id'));
            if (!$user) {
                PatternHttpException::noData();
            }

            $clientUser = $user->clientUser;
            $clientSettings = ClientSettings::findOne(['client_id' => $clientUser->getClientId()]);
            $admin = User::findOne($clientSettings->getAdminId());

            if (User::isUserMainAdmin($admin->getUsername())) {
                if ($user->getId() === $currentUser->getId() || User::isUserDev($currentUser->getUsername())
                    || User::isUserMainAdmin($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }

            } elseif (User::isUserAdminCompany($admin->getUsername()) && $clientSettings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                if (User::isUserDev($currentUser->getUsername()) || User::isUserMainAdmin($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }

            }
            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }


    }


    /**
     * Список проектантов организации
     *
     * @param int|null $id
     * @return string
     */
    public function actionIndex(int $id = null): string
    {
        if ($id) {
            $client = Client::findOne($id);
        } else {
            $user = User::findOne(Yii::$app->user->getId());
            /**
             * @var ClientUser $clientUser
             * @var Client $client
             */
            $clientUser = $user->clientUser;
            $client = $clientUser->client;
        }

        $countUsersOnPage = 20;
        $query = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_USER, 'confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['updated_at' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage, ]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();

        if ($id) {
            return $this->render('index_company',[
                'client' => $client,
                'users' => $users,
                'pages' => $pages,
            ]);
        }

        return $this->render('index',[
            'users' => $users,
            'pages' => $pages,
        ]);
    }


    /**
     * Получить данные для модального окна назначения трекера проектанту
     *
     * @param int $id
     * @return array|bool
     */
    public function actionGetModalAddAdminToUser(int $id)
    {
        if (Yii::$app->request->isAjax){

            $user = User::findOne($id);
            $admins = User::find()->with('clientUser')
                ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                ->andWhere(['role' => User::ROLE_ADMIN, 'confirm' => User::CONFIRM, 'status' => User::STATUS_ACTIVE])
                ->andWhere(['client_user.client_id' => $user->clientUser->getClientId()])
                ->all();

            $response = [
                'renderAjax' => $this->renderAjax('get_modal_add_admin_to_user', ['user' => $user, 'admins' => $admins]),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Получить данные для модального окна изменения статуса пользователя
     *
     * @param int $id
     * @return array|bool
     */
    public function actionGetModalUpdateStatus(int $id)
    {
        $model = User::findOne($id);
        if (Yii::$app->request->isAjax){
            $response = ['renderAjax' => $this->renderAjax('get_modal_update_status', ['model' => $model])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Список трекеров организации
     *
     * @param int|null $id
     * @return string
     */
    public function actionAdmins(int $id = null): string
    {
        if ($id) {
            $client = Client::findOne($id);
        } else {
            $user = User::findOne(Yii::$app->user->getId());
            /**
             * @var ClientUser $clientUser
             * @var Client $client
             */
            $clientUser = $user->clientUser;
            $client = $clientUser->client;
        }

        $countUsersOnPage = 20;
        $query = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_ADMIN, 'confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['updated_at' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();
        $clientId = (ClientUser::findOne(['user_id' => Yii::$app->user->getId()])->getClientId());

        if ($id) {
            return $this->render('admins_company',[
                'client' => $client,
                'users' => $users,
                'pages' => $pages
            ]);
        }

        return $this->render('admins',[
            'users' => $users,
            'pages' => $pages,
            'clientId' => $clientId
        ]);
    }


    /**
     * Список экспертов организации
     *
     * @param int|null $id
     * @return string
     */
    public function actionExperts(int $id = null): string
    {
        if ($id) {
            $client = Client::findOne($id);
        } else {
            $user = User::findOne(Yii::$app->user->getId());
            /**
             * @var ClientUser $clientUser
             * @var Client $client
             */
            $clientUser = $user->clientUser;
            $client = $clientUser->client;
        }

        $countUsersOnPage = 20;
        $query = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_EXPERT, 'confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['updated_at' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();

        if ($id) {
            return $this->render('experts_company',[
                'client' => $client,
                'users' => $users,
                'pages' => $pages,
            ]);
        }

        return $this->render('experts',[
            'users' => $users,
            'pages' => $pages,
        ]);
    }


    /**
     * Список экспертов организации
     *
     * @return string
     */
    public function actionManagers(): string
    {
        $user = User::findOne(Yii::$app->user->getId());
        /**
         * @var ClientUser $clientUser
         * @var Client $client
         */
        $clientUser = $user->clientUser;
        $client = $clientUser->client;
        $countUsersOnPage = 20;
        $query = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_MANAGER, 'confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['updated_at' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage, ]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();

        return $this->render('managers',[
            'users' => $users,
            'pages' => $pages,
        ]);
    }


    /**
     * Изменение статуса пользователя
     *
     * @param int $id
     * @return array|bool
     */
    public function actionStatusUpdate(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $model = User::findOne($id);

            if ($model->load(Yii::$app->request->post())) {

                if ($model->save()) {

                    if (($model->getStatus() === User::STATUS_ACTIVE) && ($model->getRole() === User::ROLE_ADMIN)) {
                        //Создание беседы между трекером и главным админом
                        $model->createConversationMainAdmin();

                    } elseif (($model->getStatus() === User::STATUS_ACTIVE) && ($model->getRole() === User::ROLE_EXPERT)) {
                        //Создание беседы между экспертом и главным админом
                        User::createConversationExpert($model->mainAdmin, $model);

                    } elseif (($model->getStatus() === User::STATUS_ACTIVE) && ($model->getRole() === User::ROLE_USER)) {
                        //Создание беседы между трекером и проектантом
                        $model->createConversationAdmin($model);

                    } elseif (($model->getStatus() === User::STATUS_ACTIVE) && ($model->getRole() === User::ROLE_MANAGER)) {
                        //Создание беседы между главным алмином и менедром по клиентам
                        ConversationManager::createRecordWithMainAdmin($model->getId(), $model->mainAdmin);
                    }

                    if (($model->getStatus() === User::STATUS_ACTIVE) && ($model->getRole() !== User::ROLE_DEV)) {
                        //Создание беседы между техподдержкой и пользователем
                        $model->createConversationDevelopment();
                    }

                    //Отправка письма на почту пользователю при изменении его статуса
                    $model->sendEmailUserStatus();

                    $response = ['model' => $model];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;

                }
            }
        }
        return false;
    }


    /**
     * Назначение трекера проектанту
     *
     * @param int $id
     * @param int $id_admin
     * @return array|bool
     */
    public function actionAddAdmin(int $id, int $id_admin)
    {
        if (Yii::$app->request->isAjax) {

            $model = User::findOne($id);
            $admin = User::findOne($id_admin);

            if ($model->load(Yii::$app->request->post())) {

                if ($model->save()) {

                    $conversation = ConversationAdmin::findOne(['user_id' => $model->getId()]);
                    if ($conversation) {
                        $conversation->setAdminId($model->getIdAdmin());
                        $conversation->save();
                    }

                    $response = ['user' => $model, 'admin' => $admin];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;

                }
            }
        }
        return false;
    }


    /**
     * Список проектантов для трекера организации
     *
     * @param int $id
     * @param int|null $page
     * @return string
     */
    public function actionGroup(int $id, int $page = null): string
    {
        $admin = User::findOne($id);
        $clientUser = $admin->clientUser;
        $countUsersOnPage = 20;
        $query = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_USER, 'confirm' => User::CONFIRM, 'id_admin' => $id])
            ->andWhere(['client_user.client_id' => $clientUser->getClientId()])
            ->orderBy(['updated_at' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => $countUsersOnPage]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();
        $currentUser = User::findOne(Yii::$app->user->getId());
        $checkCurrentUserToClient = $clientUser->getClientId() === $currentUser->clientUser->getClientId();

        return $this->render('group',[
            'admin' => $admin,
            'users' => $users,
            'pages' => $pages,
            'checkCurrentUserToClient' => $checkCurrentUserToClient,
        ]);

    }


    /**
     * Обновить на странице данных для проверки онлайн пользователь или нет
     *
     * @param int $id
     * @return array|bool
     */
    public function actionUpdateDataColumnUser(int $id)
    {
        if (Yii::$app->request->isAjax){

            $user = User::findOne($id);
            $response = ['renderAjax' => $this->renderAjax('update_column_user', ['user' => $user])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }

}