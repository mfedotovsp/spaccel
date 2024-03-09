<?php


namespace app\modules\client\controllers;

use app\models\Client;
use app\models\ClientRatesPlan;
use app\models\ClientSettings;
use app\models\ClientUser;
use app\models\ConversationAdmin;
use app\models\PatternHttpException;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class UsersController extends AppClientController
{

    public $layout = '@app/modules/client/views/layouts/users';

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {

        if (in_array($action->id, ['index', 'admins', 'experts'])) {

            if (User::isUserAdminCompany(Yii::$app->user->identity['username'])) {
                return parent::beforeAction($action);
            }
            PatternHttpException::noAccess();

        } elseif (in_array($action->id, ['status-update', 'add-admin'])) {

            if (User::isUserAdminCompany(Yii::$app->user->identity['username'])) {
                if ($action->id === 'status-update') {
                    $this->enableCsrfValidation = false;
                }
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

            if ($user->getId() === Yii::$app->user->getId() || (User::isUserAdminCompany(Yii::$app->user->identity['username']) && Yii::$app->user->getId() === $clientSettings->getAdminId())) {
                return parent::beforeAction($action);
            }
            PatternHttpException::noAccess();

        }else{
            return parent::beforeAction($action);
        }
    }


    /**
     * Список проектантов организации
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $user = User::findOne(Yii::$app->user->getId());
        $clientUser = $user->clientUser;
        $client = $clientUser->client;
        $countUsersOnPage = 20;
        $query = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_USER, 'confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['updated_at' => SORT_DESC]);

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();

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
            $client = $user->clientUser->client;
            /** @var ClientRatesPlan  $clientRatesPlan */
            $clientRatesPlan = $client->findLastClientRatesPlan();
            $ratesPlan = $clientRatesPlan->ratesPlan;

            $countUsersCompany = User::find()
                ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                ->andWhere(['client_user.client_id' => $client->getId()])
                ->andWhere(['role' => User::ROLE_USER])
                ->andWhere(['not', ['status' => [User::STATUS_NOT_ACTIVE, User::STATUS_DELETED]]])
                ->count();

            if ($ratesPlan->getMaxCountProjectUser() > $countUsersCompany || !in_array($user->getStatus(), [User::STATUS_NOT_ACTIVE, User::STATUS_DELETED], true)) {

                $admins = User::find()->with('clientUser')
                    ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                    ->andWhere(['role' => User::ROLE_ADMIN, 'confirm' => User::CONFIRM, 'status' => User::STATUS_ACTIVE])
                    ->andWhere(['client_user.client_id' => $user->clientUser->getClientId()])
                    ->all();

                $response = ['renderAjax' => $this->renderAjax('get_modal_add_admin_to_user', ['user' => $user, 'admins' => $admins])];
            } else {
                $response = ['messageError' => 'Согласно тарифу Вы не можете активировать более ' . $countUsersCompany . ' проектантов'];
            }

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
        if (Yii::$app->request->isAjax){

            $user = User::findOne($id);
            $client = $user->clientUser->client;
            /** @var ClientRatesPlan  $clientRatesPlan */
            $clientRatesPlan = $client->findLastClientRatesPlan();
            $ratesPlan = $clientRatesPlan->ratesPlan;

            if ($user->getRole() === User::ROLE_USER) {

                $countUsersCompany = User::find()
                    ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                    ->andWhere(['client_user.client_id' => $client->getId()])
                    ->andWhere(['role' => User::ROLE_USER])
                    ->andWhere(['not', ['status' => [User::STATUS_NOT_ACTIVE, User::STATUS_DELETED]]])
                    ->count();

                if ($ratesPlan->getMaxCountProjectUser() > $countUsersCompany || !in_array($user->getStatus(), [User::STATUS_NOT_ACTIVE, User::STATUS_DELETED], true)) {
                    $response = ['renderAjax' => $this->renderAjax('get_modal_update_status', ['model' => $user])];
                } else {
                    $response = ['messageError' => 'Согласно тарифу Вы не можете активировать более ' . $countUsersCompany . ' проектантов'];
                }
            } elseif ($user->getRole() === User::ROLE_ADMIN) {

                $countTrackersCompany = User::find()
                    ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                    ->andWhere(['client_user.client_id' => $client->getId()])
                    ->andWhere(['role' => User::ROLE_ADMIN_COMPANY])
                    ->andWhere(['not', ['status' => [User::STATUS_NOT_ACTIVE, User::STATUS_DELETED]]])
                    ->count();

                if ($ratesPlan->getMaxCountTracker() > $countTrackersCompany || !in_array($user->getStatus(), [User::STATUS_NOT_ACTIVE, User::STATUS_DELETED], true)) {
                    $response = ['renderAjax' => $this->renderAjax('get_modal_update_status', ['model' => $user])];
                } else {
                    $response = ['messageError' => 'Согласно тарифу Вы не можете активировать более ' . $countTrackersCompany . ' трекеров'];
                }
            } else {
                $response = ['renderAjax' => $this->renderAjax('get_modal_update_status', ['model' => $user])];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Список трекеров организации
     *
     * @return string
     */
    public function actionAdmins(): string
    {
        $user = User::findOne(Yii::$app->user->getId());
        $clientUser = $user->clientUser;
        $client = $clientUser->client;
        $countUsersOnPage = 20;
        $query = User::find()->with('clientUser')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_ADMIN, 'confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['updated_at' => SORT_DESC]);

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();
        $clientId = (ClientUser::findOne(['user_id' => Yii::$app->user->id])->getClientId());

        return $this->render('admins',[
            'users' => $users,
            'pages' => $pages,
            'clientId' => $clientId,
        ]);
    }


    /**
     * Список экспертов организации
     *
     * @return string
     */
    public function actionExperts(): string
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
            ->andWhere(['role' => User::ROLE_EXPERT, 'confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['updated_at' => SORT_DESC]);

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $countUsersOnPage]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $users = $query->offset($pages->offset)->limit($countUsersOnPage)->all();

        return $this->render('experts',[
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
                        //Создание беседы между трекером и админом организации
                        $model->createConversationMainAdmin();

                    } elseif (($model->getStatus() === User::STATUS_ACTIVE) && ($model->getRole() === User::ROLE_EXPERT)) {
                        //Создание беседы между экспертом и админом организации
                        User::createConversationExpert($model->mainAdmin, $model);

                    } elseif (($model->getStatus() === User::STATUS_ACTIVE) && ($model->getRole() === User::ROLE_USER)) {
                        //Создание беседы между трекером и проектантом
                        $model->createConversationAdmin($model);
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

        return $this->render('group',[
            'admin' => $admin,
            'users' => $users,
            'pages' => $pages,
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