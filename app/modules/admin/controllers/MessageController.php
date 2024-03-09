<?php

namespace app\modules\admin\controllers;

use app\models\ConversationDevelopment;
use app\models\forms\FormCreateMessageDevelopment;
use app\models\MessageAdmin;
use app\models\MessageDevelopment;
use app\models\MessageFiles;
use app\models\PatternHttpException;
use app\models\User;
use app\modules\admin\models\ConversationMainAdmin;
use app\modules\admin\models\ConversationManager;
use app\modules\admin\models\form\FormCreateMessageMainAdmin;
use app\modules\admin\models\form\FormCreateMessageManager;
use app\modules\admin\models\form\SearchForm;
use app\modules\admin\models\MessageMainAdmin;
use app\models\ConversationAdmin;
use app\modules\admin\models\MessageManager;
use app\modules\expert\models\ConversationExpert;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\Pagination;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MessageController extends AppAdminController
{

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {

        if ($action->id === 'view'){

           if (!User::isUserManager(Yii::$app->user->identity['username'])) {

               if (!Yii::$app->request->get('type')) {

                   $conversation = ConversationMainAdmin::findOne((int)Yii::$app->request->get('id'));
                   if (!$conversation) {
                       PatternHttpException::noData();
                   }

                   $admin = User::findOne(['id' => $conversation->getAdminId()]);
                   
                   if (User::isUserMainAdmin(Yii::$app->user->identity['username']) || $admin->getId() === Yii::$app->user->getId()){
                       // ОТКЛЮЧАЕМ CSRF
                       $this->enableCsrfValidation = false;
                       return parent::beforeAction($action);
                   }

                   PatternHttpException::noAccess();
               }

               if (Yii::$app->request->get('type') === 'manager') {

                   $conversation = ConversationManager::findOne((int)Yii::$app->request->get('id'));
                   if (!$conversation) {
                       PatternHttpException::noData();
                   }

                   $user = User::findOne(['id' => $conversation->getUserId()]);
                   
                   if ($user->getId() === Yii::$app->user->getId()){
                       // ОТКЛЮЧАЕМ CSRF
                       $this->enableCsrfValidation = false;
                       return parent::beforeAction($action);
                   }
                   PatternHttpException::noAccess();
               }

               PatternHttpException::noAccess();
           }

            $conversation = ConversationManager::findOne((int)Yii::$app->request->get('id'));
            if (!$conversation) {
                PatternHttpException::noData();
            }

            $manager = User::findOne(['id' => $conversation->getManagerId()]);
            
            if (User::isUserMainAdmin(Yii::$app->user->identity['username']) || $manager->getId() === Yii::$app->user->getId()){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'technical-support'){

            $conversation = ConversationDevelopment::findOne((int)Yii::$app->request->get('id'));
            if (!$conversation) {
                PatternHttpException::noData();
            }

            $user = $conversation->user;
            $development = $conversation->development;
            
            if (in_array(Yii::$app->user->getId(), [$user->getId(), $development->getId()], false)){

                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'index') {

            $admin = User::findOne(['id' => (int)Yii::$app->request->get('id'), 'role' => User::ROLE_ADMIN]);
            $mainAdmin = User::findOne(['id' => (int)Yii::$app->request->get('id'), 'role' => User::ROLE_MAIN_ADMIN]);
            $development = User::findOne(['id' => (int)Yii::$app->request->get('id'), 'role' => User::ROLE_DEV]);
            $manager = User::findOne(['id' => (int)Yii::$app->request->get('id'), 'role' => User::ROLE_MANAGER]);
            
            if (in_array(Yii::$app->user->getId(), [
                $admin ? $admin->getId() : null,
                $mainAdmin ? $mainAdmin->getId() : null,
                $development ? $development->getId() : null,
                $manager ? $manager->getId() : null
            ], false)){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }
    }


    /**
     * @param int $id
     * @return bool|string
     */
    public function actionIndex(int $id)
    {
        if (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {

            $main_admin = User::findOne($id);
            // Форма поиска
            $searchForm = new SearchForm();
            // Беседа главного админа с техподдержкой
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $id]);
            // Беседы главного админа с экспертами
            $expertConversations = ConversationExpert::find()
                ->andWhere(['role' => User::ROLE_MAIN_ADMIN])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();
            // Беседы главного админа с менеджерами
            $managerConversations = ConversationManager::find()
                ->andWhere(['role' => User::ROLE_MAIN_ADMIN])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();
            // Все беседы главного админа с трекерами
            $allConversations = ConversationMainAdmin::find()->joinWith('admin')
                ->andWhere(['main_admin_id' => $id])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            return $this->render('index', [
                'main_admin' => $main_admin,
                'searchForm' => $searchForm,
                'conversation_development' => $conversation_development,
                'expertConversations' => $expertConversations,
                'managerConversations' => $managerConversations,
                'allConversations' => $allConversations,
            ]);
        }

        if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

            $admin = User::findOne($id);
            // Форма поиска
            $searchForm = new SearchForm();
            // Беседа трекера с главным админом
            $conversationAdminMain = ConversationMainAdmin::findOne(['admin_id' => $admin->getId()]);
            // Беседа трекера с техподдержкой
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $id]);
            // Все беседы трекера с проектантами
            $allConversations = ConversationAdmin::find()->joinWith('user')
                ->andWhere(['user.id_admin' => $id])
                ->andWhere(['admin_id' => $id])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();
            // Все беседы трекера с экспертами
            $conversationsExpert = ConversationExpert::find()
                ->andWhere(['user_id' => $id])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            return $this->render('index-admin', [
                'admin' => $admin,
                'searchForm' => $searchForm,
                'conversationAdminMain' => $conversationAdminMain,
                'conversation_development' => $conversation_development,
                'allConversations' => $allConversations,
                'conversationsExpert' => $conversationsExpert,
            ]);
        }

        if (User::isUserDev(Yii::$app->user->identity['username'])) {

            $development = User::findOne($id);
            // Форма поиска
            $searchForm = new SearchForm();
            // Все беседы техподдержки
            $allConversations = ConversationDevelopment::find()->joinWith('user')
                ->andWhere(['dev_id' => $id])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            return $this->render('index-development', [
                'development' => $development,
                'searchForm' => $searchForm,
                'allConversations' => $allConversations,
            ]);
        }

        if (User::isUserManager(Yii::$app->user->identity['username'])) {

            $manager = User::findOne($id);
            // Форма поиска
            $searchForm = new SearchForm();
            // Беседа менеджера с главным админом Spaccel
            $conversationAdminMain = ConversationManager::findOne(['manager_id' => $id, 'user_id' => $manager->mainAdmin->getId()]);
            // Беседа менеджера с техподдержкой
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $id]);
            // Все беседы менеджера с админами организаций
            $conversationsAdmin = ConversationManager::find()->andWhere(['manager_id' => $id, 'role' => User::ROLE_ADMIN_COMPANY])->orderBy(['updated_at' => SORT_DESC])->all();

            return $this->render('index-manager', [
                'manager' => $manager,
                'searchForm' => $searchForm,
                'conversationAdminMain' => $conversationAdminMain,
                'conversation_development' => $conversation_development,
                'conversationsAdmin' => $conversationsAdmin,
            ]);
        }
        return false;
    }


    /**
     * @param int $id
     * @param string $pathname
     * @return array|bool
     */
    public function actionGetListUpdateConversations(int $id, string $pathname)
    {
        if (Yii::$app->request->isAjax) {

            if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

                if ($pathname === 'index') {

                    $admin = User::findOne($id);
                    $conversationAdminMain = ConversationMainAdmin::findOne(['admin_id' => $admin->getId()]);
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $admin->getId()]);

                } elseif ($pathname === 'view') {

                    $conversationAdminMain = ConversationMainAdmin::findOne($id);
                    $admin = $conversationAdminMain->admin;
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $admin->getId()]);

                } elseif ($pathname === 'technical-support') {

                    $conversation_development = ConversationDevelopment::findOne($id);
                    $admin = $conversation_development->user;
                    $conversationAdminMain = ConversationMainAdmin::findOne(['admin_id' => $admin->getId()]);

                } else {
                    return false;
                }

                $response = [
                    'blockConversationAdminMain' => '#adminMainConversation-' . $conversationAdminMain->getId(),
                    'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $conversation_development->getId(),
                    'conversationAdminMainForAdminAjax' => $this->renderAjax('update_conversation_main_admin_for_admin', [
                        'conversationAdminMain' => $conversationAdminMain, 'admin' => $admin,
                    ]),
                    'conversationDevelopmentForAdminAjax' => $this->renderAjax('update_conversation_development_for_admin', [
                        'conversation_development' => $conversation_development, 'admin' => $admin,
                    ]),
                    'conversationsUserForAdminAjax' => $this->renderAjax('update_conversations_user_for_admin', [
                        'allConversations' => ConversationAdmin::find()->joinWith('user')
                            ->andWhere(['user.id_admin' => $admin->getId()])->andWhere(['admin_id' => $admin->getId()])
                            ->orderBy(['updated_at' => SORT_DESC])->all(),
                    ]),
                    'conversationsExpertForAdminAjax' => $this->renderAjax('update_conversations_expert_for_admin',[
                        'conversationsExpert' => ConversationExpert::find()->andWhere(['user_id' => $admin->getId()])
                            ->orderBy(['updated_at' => SORT_DESC])->all(),
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            if (User::isUserManager(Yii::$app->user->identity['username'])) {

                if ($pathname === 'index') {

                    $manager = User::findOne($id);
                    $conversationAdminMain = ConversationManager::findOne(['manager_id' => $id, 'user_id' => $manager->mainAdmin->getId()]);
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $id]);

                } elseif ($pathname === 'view') {

                    $conversationAdminMain = ConversationManager::findOne($id);
                    $manager = $conversationAdminMain->manager;
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $manager->getId()]);

                } elseif ($pathname === 'technical-support') {

                    $conversation_development = ConversationDevelopment::findOne($id);
                    $manager = $conversation_development->user;
                    $conversationAdminMain = ConversationManager::findOne(['manager_id' => $manager->getId(), 'role' => User::ROLE_MAIN_ADMIN]);

                } else {
                    return false;
                }

                $response = [
                    'blockConversationAdminMain' => '#adminMainConversation-' . $conversationAdminMain->getId(),
                    'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $conversation_development->getId(),
                    'conversationAdminMainForManagerAjax' => $this->renderAjax('update_conversation_main_admin_for_manager', [
                        'conversationAdminMain' => $conversationAdminMain, 'manager' => $manager,
                    ]),
                    'conversationDevelopmentForManagerAjax' => $this->renderAjax('update_conversation_development_for_manager', [
                        'conversation_development' => $conversation_development, 'manager' => $manager,
                    ]),
                    'conversationsClientForManagerAjax' => $this->renderAjax('update_conversations_client_for_manager',[
                        'conversationsAdmin' => ConversationManager::find()->andWhere(['manager_id' => $manager->getId(), 'role' => User::ROLE_ADMIN_COMPANY])
                            ->orderBy(['updated_at' => SORT_DESC])->all(),
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            if (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {

                if ($pathname === 'index') {

                    $main_admin = User::findOne($id);
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $main_admin->getId()]);

                } elseif ($pathname === 'view') {

                    $conversation = ConversationMainAdmin::findOne($id);
                    $main_admin = $conversation->mainAdmin;
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $main_admin->getId()]);

                } elseif ($pathname === 'view-manager') {

                    $conversation = ConversationManager::findOne($id);
                    $main_admin = $conversation->user;
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $main_admin->getId()]);

                } elseif ($pathname === 'technical-support') {

                    $conversation_development = ConversationDevelopment::findOne($id);
                    $main_admin = $conversation_development->user;

                } else {
                    return false;
                }

                $response = [
                    'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $conversation_development->getId(),
                    'conversationDevelopmentForAdminMainAjax' => $this->renderAjax('update_conversation_development_for_main_admin', [
                        'conversation_development' => $conversation_development, 'main_admin' => $main_admin,
                    ]),
                    'conversationsAdminForAdminMainAjax' => $this->renderAjax('update_conversations_admin_for_main_admin', [
                        'allConversations' => ConversationMainAdmin::find()->andWhere(['main_admin_id' => $main_admin->getId()])
                            ->orderBy(['updated_at' => SORT_DESC])->all(),
                    ]),
                    'conversationsExpertForAdminMainAjax' => $this->renderAjax('update_conversations_expert_for_main_admin', [
                        'expertConversations' => ConversationExpert::find()->andWhere(['user_id' => $main_admin->getId()])
                            ->orderBy(['updated_at' => SORT_DESC])->all(),
                    ]),
                    'conversationsManagerForAdminMainAjax' => $this->renderAjax('update_conversations_manager_for_main_admin', [
                        'managerConversations' => ConversationManager::find()->andWhere(['user_id' => $main_admin->getId()])
                            ->orderBy(['updated_at' => SORT_DESC])->all(),
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            if (User::isUserDev(Yii::$app->user->identity['username'])) {

                if ($pathname === 'index') {

                    $development = User::findOne($id);

                } elseif ($pathname === 'technical-support') {

                    $conversation = ConversationDevelopment::findOne($id);
                    $development = $conversation->development;

                } else {
                    return false;
                }

                $response = [
                    'conversationsForDevelopmentAjax' => $this->renderAjax('update_conversations_for_development', [
                        'allConversations' => ConversationDevelopment::find()->joinWith('user')
                            ->andWhere(['dev_id' => $development->getId()])->orderBy(['updated_at' => SORT_DESC])->all(),
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
     */
    public function actionCheckingUnreadMessageMainAdmin(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $message = MessageMainAdmin::findOne($id);

            if ($message->getStatus() === MessageMainAdmin::READ_MESSAGE) {

                $response = ['checkRead' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            $response = ['checkRead' => false];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionCheckingUnreadMessageManager(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $message = MessageManager::findOne($id);

            if ($message->getStatus() === MessageManager::READ_MESSAGE) {

                $response = ['checkRead' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            $response = ['checkRead' => false];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @param int $idLastMessageOnPage
     * @return array|bool
     */
    public function actionCheckNewMessagesMainAdmin(int $id, int $idLastMessageOnPage)
    {
        if(Yii::$app->request->isAjax) {

            $conversation = ConversationMainAdmin::findOne($id);
            $main_admin = $conversation->mainAdmin;
            $admin = $conversation->admin;
            $lastMessageOnPage = MessageMainAdmin::findOne($idLastMessageOnPage);
            $messages = MessageMainAdmin::find()->andWhere(['conversation_id' => $conversation->getId()])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

            if ($messages) {

                $response = [
                    'checkNewMessages' => true,
                    'addNewMessagesAjax' => $this->renderAjax('check_new_messages_main_admin', [
                        'messages' => $messages, 'main_admin' => $main_admin, 'admin' => $admin, 'lastMessageOnPage' => $lastMessageOnPage,
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            $response = ['checkNewMessages' => false];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @param int $idLastMessageOnPage
     * @return array|bool
     */
    public function actionCheckNewMessagesManager(int $id, int $idLastMessageOnPage)
    {
        if(Yii::$app->request->isAjax) {

            $conversation = ConversationManager::findOne($id);
            $main_admin = $conversation->user;
            $manager = $conversation->manager;
            $lastMessageOnPage = MessageManager::findOne($idLastMessageOnPage);
            $messages = MessageManager::find()->andWhere(['conversation_id' => $conversation->getId()])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

            if ($messages) {

                $response = [
                    'checkNewMessages' => true,
                    'addNewMessagesAjax' => $this->renderAjax('check_new_messages_main_admin_for_manager', [
                        'messages' => $messages, 'main_admin' => $main_admin, 'manager' => $manager, 'lastMessageOnPage' => $lastMessageOnPage,
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            $response = ['checkNewMessages' => false];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @param string|null $type
     * @return bool|string
     */
    public function actionView(int $id, string $type = null)
    {
        $conversation = ConversationMainAdmin::findOne($id);
        $formMessage = new FormCreateMessageMainAdmin();
        $main_admin = $conversation->mainAdmin;
        $admin = $conversation->admin;
        $searchForm = new SearchForm(); // Форма поиска
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        // Вывод сообщений через пагинацию
        $query = MessageMainAdmin::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);
        $countMessages = MessageMainAdmin::find()->andWhere(['conversation_id' => $id])->count();

        if (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {

            if (!$type) {
                // Беседа админа с техподдержкой
                $conversation_development = ConversationDevelopment::findOne(['user_id' => $main_admin->getId()]);
                // Все беседы главного админа с менеджерами
                $managerConversations = ConversationManager::find()
                    ->andWhere(['user_id' => $main_admin->getId()])
                    ->orderBy(['updated_at' => SORT_DESC])
                    ->all();
                // Все беседы главного админа с трекерами
                $allConversations = ConversationMainAdmin::find()
                    ->andWhere(['main_admin_id' => $main_admin->getId()])
                    ->orderBy(['updated_at' => SORT_DESC])
                    ->all();
                // Все беседы главного админа с экспертами
                $expertConversations = ConversationExpert::find()
                    ->andWhere(['user_id' => $main_admin->getId()])
                    ->orderBy(['updated_at' => SORT_DESC])
                    ->all();

                // Если есть кэш, добавляем его в форму сообщения
                $cache->cachePath = '../runtime/cache/forms/user-' . $main_admin->getId() . '/messages/category_main_admin/conversation-' . $conversation->getId() . '/';
                $cache_form_message = $cache->get('formCreateMessageMainAdminCache');
                if ($cache_form_message) {
                    $formMessage->setDescription($cache_form_message['FormCreateMessageMainAdmin']['description']);
                }

                return $this->render('view', [
                    'formMessage' => $formMessage,
                    'main_admin' => $main_admin,
                    'admin' => $admin,
                    'searchForm' => $searchForm,
                    'messages' => $messages,
                    'countMessages' => $countMessages,
                    'pagesMessages' => $pagesMessages,
                    'conversation_development' => $conversation_development,
                    'managerConversations' => $managerConversations,
                    'allConversations' => $allConversations,
                    'expertConversations' => $expertConversations,
                ]);

            }

            if ($type === 'manager') {

                $conversation = ConversationManager::findOne($id);
                $formMessage = new FormCreateMessageManager();
                $main_admin = $conversation->user;
                $manager = $conversation->manager;
                $searchForm = new SearchForm(); // Форма поиска
                $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
                // Вывод сообщений через пагинацию
                $query = MessageManager::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
                $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
                $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
                $messages = array_reverse($messages);
                $countMessages = MessageManager::find()->andWhere(['conversation_id' => $id])->count();

                // Беседа админа с техподдержкой
                $conversation_development = ConversationDevelopment::findOne(['user_id' => $main_admin->getId()]);
                // Все беседы главного админа с менеджерами
                $managerConversations = ConversationManager::find()
                    ->andWhere(['user_id' => $main_admin->getId()])
                    ->orderBy(['updated_at' => SORT_DESC])
                    ->all();
                // Все беседы главного админа с трекерами
                $allConversations = ConversationMainAdmin::find()
                    ->andWhere(['main_admin_id' => $main_admin->getId()])
                    ->orderBy(['updated_at' => SORT_DESC])
                    ->all();
                // Все беседы главного админа с экспертами
                $expertConversations = ConversationExpert::find()
                    ->andWhere(['user_id' => $main_admin->getId()])
                    ->orderBy(['updated_at' => SORT_DESC])
                    ->all();

                // Если есть кэш, добавляем его в форму сообщения
                $cache->cachePath = '../runtime/cache/forms/user-' . $main_admin->getId() . '/messages/category_manager/conversation-' . $conversation->getId() . '/';
                $cache_form_message = $cache->get('formCreateMessageManagerCache');
                if ($cache_form_message) {
                    $formMessage->setDescription($cache_form_message['FormCreateMessageManager']['description']);
                }

                return $this->render('view_manager_for_main_admin', [
                    'formMessage' => $formMessage,
                    'main_admin' => $main_admin,
                    'manager' => $manager,
                    'searchForm' => $searchForm,
                    'messages' => $messages,
                    'countMessages' => $countMessages,
                    'pagesMessages' => $pagesMessages,
                    'conversation_development' => $conversation_development,
                    'managerConversations' => $managerConversations,
                    'allConversations' => $allConversations,
                    'expertConversations' => $expertConversations,
                ]);
            }
        }

        if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

            // Беседа трекера с главным админом
            $conversationAdminMain = ConversationMainAdmin::findOne(['admin_id' => $admin->getId()]);
            // Беседа трекера с техподдержкой
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $admin->getId()]);
            // Все беседы трекера с экспертами
            $expertConversations = ConversationExpert::find()
                ->andWhere(['user_id' => $admin->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();
            // Все беседы трекера с проектантами
            $allConversations = ConversationAdmin::find()->joinWith('user')
                ->andWhere(['user.id_admin' => $admin->getId()])
                ->andWhere(['admin_id' => $admin->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$admin->getId().'/messages/category_main_admin/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageMainAdminCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageMainAdmin']['description']);
            }

            return $this->render('view-admin', [
                'formMessage' => $formMessage,
                'main_admin' => $main_admin,
                'admin' => $admin,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'conversationAdminMain' => $conversationAdminMain,
                'conversation_development' => $conversation_development,
                'expertConversations' => $expertConversations,
                'allConversations' => $allConversations,
            ]);
        }

        if (User::isUserManager(Yii::$app->user->identity['username'])) {

            // Беседа менеджера с главным админом Spaccel
            $conversation = ConversationManager::findOne($id);
            $formMessage = new FormCreateMessageManager();
            $main_admin = $conversation->user;
            $manager = $conversation->manager;

            // Вывод сообщений через пагинацию
            $query = MessageManager::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
            $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
            $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
            $messages = array_reverse($messages);
            $countMessages = MessageManager::find()->andWhere(['conversation_id' => $id])->count();

            // Беседа менеджера с техподдержкой
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $manager->getId()]);
            // Все беседы менеджера с админами организаций
            $conversationsAdmin = ConversationManager::find()->andWhere(['manager_id' => $manager->getId(), 'role' => User::ROLE_ADMIN_COMPANY])->orderBy(['updated_at' => SORT_DESC])->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$manager->getId().'/messages/category_manager/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageManagerCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageManager']['description']);
            }

            return $this->render('view-manager', [
                'conversation' => $conversation,
                'formMessage' => $formMessage,
                'main_admin' => $main_admin,
                'manager' => $manager,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'conversation_development' => $conversation_development,
                'conversationsAdmin' => $conversationsAdmin
            ]);
        }

        return false;
    }


    /**
     * @param int $id
     * @param int $page
     * @param int $final
     * @return array|bool
     */
    public function actionGetPageMessage(int $id, int $page, int $final)
    {
        if(Yii::$app->request->isAjax) {

            $conversation = ConversationMainAdmin::findOne($id);
            $main_admin = $conversation->mainAdmin;
            $admin = $conversation->admin;
            $query = MessageMainAdmin::find()->andWhere(['conversation_id' => $id])->andWhere(['<', 'id', $final])->orderBy(['id' => SORT_DESC]);
            $pagesMessages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => 20]);
            $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
            $messages = array_reverse($messages);

            // Проверяем является ли страница последней
            $lastPage = false;
            /**
             * @var MessageMainAdmin[] $messages
             * @var MessageMainAdmin $lastMessage
             */
            $lastMessage = MessageMainAdmin::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_ASC])->one();
            foreach ($messages as $message) {
                if ($message->getId() === $lastMessage->getId()) {
                    $lastPage = true;
                }
            }

            $response = ['nextPageMessageAjax' => $this->renderAjax('message_main_admin_pagination_ajax', [
                'messages' => $messages, 'pagesMessages' => $pagesMessages,
                'main_admin' => $main_admin, 'admin' => $admin,
            ]), 'lastPage' => $lastPage];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @param int $page
     * @param int $final
     * @return array|bool
     */
    public function actionGetPageMessageManagerMainAdmin(int $id, int $page, int $final)
    {
        if(Yii::$app->request->isAjax) {

            $conversation = ConversationManager::findOne($id);
            $main_admin = $conversation->user;
            $manager = $conversation->manager;
            $query = MessageManager::find()->andWhere(['conversation_id' => $id])->andWhere(['<', 'id', $final])->orderBy(['id' => SORT_DESC]);
            $pagesMessages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => 20]);
            $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
            $messages = array_reverse($messages);

            // Проверяем является ли страница последней
            $lastPage = false;
            /**
             * @var MessageManager[] $messages
             * @var MessageManager $lastMessage
             */
            $lastMessage = MessageManager::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_ASC])->one();
            foreach ($messages as $message) {
                if ($message->getId() === $lastMessage->getId()) {
                    $lastPage = true;
                }
            }

            $response = ['nextPageMessageAjax' => $this->renderAjax('message_manager_main_admin_pagination_ajax', [
                'messages' => $messages, 'pagesMessages' => $pagesMessages,
                'main_admin' => $main_admin, 'manager' => $manager,
            ]), 'lastPage' => $lastPage];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return bool
     */
    public function actionSaveCacheMessageMainAdminForm(int $id): bool
    {
        if(Yii::$app->request->isAjax) {

            $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
            $data = $_POST; //Массив, который будем записывать в кэш
            $conversation = ConversationMainAdmin::findOne($id);
            $user = User::findOne(Yii::$app->user->getId());

            if ($conversation->getMainAdminId() === $user->getId() || $conversation->getAdminId() === $user->getId()) {

                $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_main_admin/conversation-'.$conversation->getId().'/';
                $key = 'formCreateMessageMainAdminCache'; //Формируем ключ
                $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return bool
     */
    public function actionSaveCacheMessageManagerForm(int $id): bool
    {
        if(Yii::$app->request->isAjax) {

            $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
            $data = $_POST; //Массив, который будем записывать в кэш
            $conversation = ConversationManager::findOne($id);
            $user = User::findOne(Yii::$app->user->getId());

            if ($conversation->getUserId() === $user->getId() || $conversation->getManagerId() === $user->getId()) {

                $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_manager/conversation-'.$conversation->getId().'/';
                $key = 'formCreateMessageManagerCache'; //Формируем ключ
                $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @param int $idLastMessageOnPage
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws ErrorException
     * @throws Exception
     */
    public function actionSendMessage(int $id, int $idLastMessageOnPage)
    {
        if (Yii::$app->request->isAjax){

            $conversation = ConversationMainAdmin::findOne($id);
            $main_admin = $conversation->mainAdmin;
            $admin = $conversation->admin;
            $formMessage = new FormCreateMessageMainAdmin();
            $lastMessageOnPage = MessageMainAdmin::findOne($idLastMessageOnPage);

            if ($formMessage->load(Yii::$app->request->post())) {

                if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($admin->getId());
                    $formMessage->setAdresseeId($main_admin->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$admin->getId().'/messages/category_main_admin/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageMainAdmin::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        $response =  [
                            'sender' => 'admin',
                            'countUnreadMessages' => $admin->countUnreadMessages,
                            'blockConversationAdminMain' => '#adminMainConversation-' . $id,
                            'conversationAdminMainForAdminAjax' => $this->renderAjax('update_conversation_main_admin_for_admin', [
                                'conversationAdminMain' => ConversationMainAdmin::findOne($id), 'admin' => $admin,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_main_admin', [
                                'messages' => $messages, 'main_admin' => $main_admin, 'admin' => $admin, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                elseif (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($main_admin->getId());
                    $formMessage->setAdresseeId($admin->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$main_admin->getId().'/messages/category_main_admin/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageMainAdmin::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        $response =  [
                            'sender' => 'main_admin',
                            'countUnreadMessages' => $main_admin->countUnreadMessages,
                            'conversationsAdminForAdminMainAjax' => $this->renderAjax('update_conversations_admin_for_main_admin', [
                                'allConversations' => ConversationMainAdmin::find()->andWhere(['main_admin_id' => $main_admin->getId()])
                                    ->orderBy(['updated_at' => SORT_DESC])->all(),
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_main_admin', [
                                'messages' => $messages, 'main_admin' => $main_admin, 'admin' => $admin, 'lastMessageOnPage' => $lastMessageOnPage,
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


    /**
     * @param int $id
     * @param int $idLastMessageOnPage
     * @return array|bool
     * @throws ErrorException
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionSendMessageManagerMainAdmin(int $id, int $idLastMessageOnPage)
    {
        if (Yii::$app->request->isAjax){

            $conversation = ConversationManager::findOne($id);
            $main_admin = $conversation->user;
            $manager = $conversation->manager;
            $formMessage = new FormCreateMessageManager();
            $lastMessageOnPage = MessageManager::findOne($idLastMessageOnPage);

            if ($formMessage->load(Yii::$app->request->post())) {

                if (User::isUserManager(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($manager->getId());
                    $formMessage->setAdresseeId($main_admin->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$manager->getId().'/messages/category_manager/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageManager::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        $response =  [
                            'sender' => 'manager',
                            'countUnreadMessages' => $manager->countUnreadMessages,
                            'blockConversationAdminMain' => '#adminMainConversation-' . $id,
                            'conversationAdminMainForAdminAjax' => $this->renderAjax('update_conversation_main_admin_for_manager', [
                                'conversationAdminMain' => ConversationManager::findOne($id), 'manager' => $manager,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_main_admin_for_manager', [
                                'messages' => $messages, 'main_admin' => $main_admin, 'manager' => $manager, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                elseif (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($main_admin->getId());
                    $formMessage->setAdresseeId($manager->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$main_admin->getId().'/messages/category_manager/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageManager::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        $response =  [
                            'sender' => 'main_admin',
                            'countUnreadMessages' => $main_admin->countUnreadMessages,
                            'conversationsManagerForAdminMainAjax' => $this->renderAjax('update_conversations_manager_for_main_admin', [
                                'managerConversations' => ConversationManager::find()->andWhere(['user_id' => $main_admin->getId()])
                                    ->orderBy(['updated_at' => SORT_DESC])->all(),
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_main_admin_for_manager', [
                                'messages' => $messages, 'main_admin' => $main_admin, 'manager' => $manager, 'lastMessageOnPage' => $lastMessageOnPage,
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


    /**
     * @param int $id
     * @return array|false
     */
    public function actionGetAdminConversationQuery(int $id)
    {
        if (Yii::$app->request->isAjax){

            $query = trim($_POST['SearchForm']['search']);
            //Беседы с менеджерами, которые попали в запрос
            $manager_conversations_query = ConversationManager::find()->joinWith('manager')
                ->andWhere(['user_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            //Беседы с трекерами, которые попали в запрос
            $conversations_query = ConversationMainAdmin::find()->joinWith('admin')
                ->andWhere(['main_admin_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            //Беседы с экспертами, которые попали в запрос
            $expert_conversations_query = ConversationExpert::find()->joinWith('expert')
                ->andWhere(['user_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            $response = ['renderAjax' => $this->renderAjax('admin_conversations_query', [
                'conversations_query' => $conversations_query, 'expert_conversations_query' => $expert_conversations_query,
                'manager_conversations_query' => $manager_conversations_query])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionGetConversationQuery(int $id)
    {
        if (Yii::$app->request->isAjax){

            $query = trim($_POST['SearchForm']['search']);
            //Беседы с пользователями, которые попали в запрос
            $conversations_query = ConversationAdmin::find()->joinWith('user')
                ->andWhere(['user.id_admin' => $id])
                ->andWhere(['admin_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            $expert_conversations_query = ConversationExpert::find()->joinWith('expert')
                ->andWhere(['user_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            $response = ['renderAjax' => $this->renderAjax('conversations_query', [
                'conversations_query' => $conversations_query, 'expert_conversations_query' => $expert_conversations_query])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionGetDevelopmentConversationQuery(int $id)
    {
        if (Yii::$app->request->isAjax){

            $query = trim($_POST['SearchForm']['search']);
            //Беседы техподдержки, которые попали в запрос
            $conversations_query = ConversationDevelopment::find()->joinWith('user')
                ->andWhere(['dev_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            $response = ['renderAjax' => $this->renderAjax('conversations_query_development', ['conversations_query' => $conversations_query])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionGetManagerConversationQuery(int $id)
    {
        if (Yii::$app->request->isAjax){

            $query = trim($_POST['SearchForm']['search']);
            //Беседы с админами, которые попали в запрос
            $conversations_query = ConversationManager::find()->joinWith('user')
                ->andWhere(['manager_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            $response = ['renderAjax' => $this->renderAjax('conversations_query_manager', ['conversations_query' => $conversations_query])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionReadMessageMainAdmin(int $id)
    {
        if (Yii::$app->request->isAjax){

            $model = MessageMainAdmin::findOne($id);
            $model->setStatus(MessageMainAdmin::READ_MESSAGE);

            if ($model->save()) {
                $user = User::findOne($model->getAdresseeId());
                $countUnreadMessagesForConversation = MessageMainAdmin::find()
                    ->andWhere([
                        'adressee_id' => $model->getAdresseeId(),
                        'sender_id' => $model->getSenderId(),
                        'status' => MessageMainAdmin::NO_READ_MESSAGE
                    ])->count();

                // Передаем id блока беседы
                $blockConversation = '';
                if (User::isUserMainAdmin($user->getUsername())) {
                    $blockConversation = '#adminConversation-' . $model->getConversationId();
                } elseif (User::isUserAdmin($user->getUsername())) {
                    $blockConversation = '#adminMainConversation-' . $model->getConversationId();
                }

                $response = [
                    'success' => true,
                    'message' => $model,
                    'countUnreadMessages' => $user->countUnreadMessages,
                    'blockConversation' => $blockConversation,
                    'countUnreadMessagesForConversation' => $countUnreadMessagesForConversation,
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
     */
    public function actionReadMessageManager(int $id)
    {
        if (Yii::$app->request->isAjax){

            $model = MessageManager::findOne($id);
            $model->setStatus(MessageManager::READ_MESSAGE);

            if ($model->save()) {
                $user = User::findOne($model->getAdresseeId());
                $countUnreadMessagesForConversation = MessageManager::find()
                    ->andWhere([
                        'adressee_id' => $model->getAdresseeId(),
                        'sender_id' => $model->getSenderId(),
                        'status' => MessageManager::NO_READ_MESSAGE
                    ])->count();

                // Передаем id блока беседы
                $blockConversation = '';
                if (User::isUserMainAdmin($user->getUsername())) {
                    $blockConversation = '#managerConversation-' . $model->getConversationId();
                } elseif (User::isUserManager($user->getUsername())) {
                    $blockConversation = '#adminMainConversation-' . $model->getConversationId();
                }

                $response = [
                    'success' => true,
                    'message' => $model,
                    'countUnreadMessages' => $user->countUnreadMessages,
                    'blockConversation' => $blockConversation,
                    'countUnreadMessagesForConversation' => $countUnreadMessagesForConversation,
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @param int $category
     * @param int $id
     * @return false|\yii\console\Response|Response
     * @throws NotFoundHttpException
     */
    public function actionDownload(int $category, int $id)
    {
        /** @var MessageFiles $model */
        $model = MessageFiles::find()
            ->andWhere([
                'category' => $category,
                'id' => $id
            ])->one();

        if ($category === MessageFiles::CATEGORY_ADMIN) {
            $message = MessageAdmin::findOne($model->getMessageId());
        } elseif ($category === MessageFiles::CATEGORY_MAIN_ADMIN) {
            $message = MessageMainAdmin::findOne($model->getMessageId());
        } elseif ($category === MessageFiles::CATEGORY_TECHNICAL_SUPPORT) {
            $message = MessageDevelopment::findOne($model->getMessageId());
        } elseif ($category === MessageFiles::CATEGORY_MANAGER) {
            $message = MessageManager::findOne($model->getMessageId());
        } else {
            return false;
        }

        $path = UPLOAD.'/user-'.$message->getSenderId().'/messages/category-'.$category.'/message-'.$message->getId().'/';
        $file = $path . $model->getServerFile();

        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file, $model->getFileName());
        }
        throw new NotFoundHttpException('Данный файл не найден');
    }


    /**
     * @param int $id
     * @return bool|string
     */
    public function actionTechnicalSupport(int $id)
    {
        $conversation = ConversationDevelopment::findOne($id);
        $formMessage = new FormCreateMessageDevelopment();
        $development = $conversation->development;
        $user = $conversation->user;
        $searchForm = new SearchForm(); // Форма поиска
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        // Вывод сообщений через пагинацию
        $query = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);
        $countMessages = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->count();

        if (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {

            // Все беседы главного админа с менеджерами
            $managerConversations = ConversationManager::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Все беседы главного админа с трекерами
            $allConversations = ConversationMainAdmin::find()->joinWith('admin')
                ->andWhere(['main_admin_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Все беседы главного админа с экспертами
            $expertConversations = ConversationExpert::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageDevelopmentCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageDevelopment']['description']);
            }

            return $this->render('technical-support-main-admin', [
                'conversation_development' => $conversation,
                'formMessage' => $formMessage,
                'main_admin' => $user,
                'development' => $development,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'allConversations' => $allConversations,
                'expertConversations' => $expertConversations,
                'managerConversations' => $managerConversations,
            ]);
        }

        if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

            // Беседа трекера с главным админом
            $conversationAdminMain = ConversationMainAdmin::findOne(['admin_id' => $user->getId()]);
            $main_admin = $conversationAdminMain->mainAdmin;
            // Все беседы трекера с экспертами
            $expertConversations = ConversationExpert::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();
            // Все беседы трекера с проектантами
            $allConversations = ConversationAdmin::find()->joinWith('user')
                ->andWhere(['user.id_admin' => $user->getId()])
                ->andWhere(['admin_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageDevelopmentCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageDevelopment']['description']);
            }

            return $this->render('technical-support-admin', [
                'conversation_development' => $conversation,
                'formMessage' => $formMessage,
                'main_admin' => $main_admin,
                'admin' => $user,
                'development' => $development,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'conversationAdminMain' => $conversationAdminMain,
                'expertConversations' => $expertConversations,
                'allConversations' => $allConversations,
            ]);

        }

        if (User::isUserManager(Yii::$app->user->identity['username'])) {

            // Все беседы менеджера с админами организаций
            $conversationsAdmin = ConversationManager::find()
                ->andWhere([
                    'manager_id' => $user->getId(),
                    'role' => User::ROLE_ADMIN_COMPANY
                ])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Бесда менеджера с админом Spaccel
            $conversationAdminMain = ConversationManager::findOne(['manager_id' => $user->getId(), 'role' => User::ROLE_MAIN_ADMIN]);

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageDevelopmentCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageDevelopment']['description']);
            }

            return $this->render('technical-support-manager', [
                'conversation_development' => $conversation,
                'formMessage' => $formMessage,
                'development' => $development,
                'manager' => $user,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'conversationsAdmin' => $conversationsAdmin,
                'conversationAdminMain' => $conversationAdminMain,
            ]);

        }

        if (User::isUserDev(Yii::$app->user->identity['username'])) {

            $allConversations = ConversationDevelopment::find()->joinWith('user')
                ->andWhere(['dev_id' => $development->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$development->getId().'/messages/category_technical_support/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageDevelopmentCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageDevelopment']['description']);
            }

            if (User::isUserAdmin($user->getUsername())) {

                return $this->render('technical-support-development-for-admin', [
                    'formMessage' => $formMessage,
                    'admin' => $user,
                    'searchForm' => $searchForm,
                    'messages' => $messages,
                    'countMessages' => $countMessages,
                    'pagesMessages' => $pagesMessages,
                    'development' => $development,
                    'allConversations' => $allConversations,
                ]);
            }

            if (User::isUserMainAdmin($user->getUsername())) {

                return $this->render('technical-support-development-for-main-admin', [
                    'formMessage' => $formMessage,
                    'main_admin' => $user,
                    'searchForm' => $searchForm,
                    'messages' => $messages,
                    'countMessages' => $countMessages,
                    'pagesMessages' => $pagesMessages,
                    'development' => $development,
                    'allConversations' => $allConversations,
                ]);
            }

            if (User::isUserManager($user->getUsername())) {

                return $this->render('technical-support-development-for-manager', [
                    'formMessage' => $formMessage,
                    'manager' => $user,
                    'searchForm' => $searchForm,
                    'messages' => $messages,
                    'countMessages' => $countMessages,
                    'pagesMessages' => $pagesMessages,
                    'development' => $development,
                    'allConversations' => $allConversations,
                ]);
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionCheckingUnreadMessageDevelopment(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $message = MessageDevelopment::findOne($id);

            if ($message->getStatus() === MessageDevelopment::READ_MESSAGE) {

                $response = ['checkRead' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            $response = ['checkRead' => false];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @param int $idLastMessageOnPage
     * @return array|bool
     */
    public function actionCheckNewMessagesDevelopment(int $id, int $idLastMessageOnPage)
    {
        if(Yii::$app->request->isAjax) {

            $conversation = ConversationDevelopment::findOne($id);
            $development = $conversation->development;
            $user = $conversation->user;
            $lastMessageOnPage = MessageDevelopment::findOne($idLastMessageOnPage);
            $messages = MessageDevelopment::find()->andWhere(['conversation_id' => $conversation->getId()])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

            if ($messages) {

                if (User::isUserMainAdmin($user->username)) {

                    $response = [
                        'checkNewMessages' => true,
                        'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_main_admin', [
                            'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                        ]),
                    ];
                }
                elseif (User::isUserAdmin($user->username) || User::isUserManager($user->username)) {

                    $response = [
                        'checkNewMessages' => true,
                        'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_admin', [
                            'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                        ]),
                    ];
                } else {
                    return false;
                }

                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;

            }

            $response = ['checkNewMessages' => false];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }

    /**
     * @param int $id
     * @param int $page
     * @param int $final
     * @return array|bool
     */
    public function actionGetPageMessageDevelopment(int $id, int $page, int $final)
    {
        if(Yii::$app->request->isAjax) {

            $conversation = ConversationDevelopment::findOne($id);
            $user = $conversation->user;
            $development = $conversation->development;
            $query = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->andWhere(['<', 'id', $final])->orderBy(['id' => SORT_DESC]);
            $pagesMessages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => 20]);
            $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
            $messages = array_reverse($messages);

            // Проверяем является ли страница последней
            $lastPage = false;
            /**
             * @var MessageDevelopment $lastMessage
             * @var MessageDevelopment[] $messages
             */
            $lastMessage = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_ASC])->one();
            foreach ($messages as $message) {
                if ($message->getId() === $lastMessage->getId()) {
                    $lastPage = true;
                }
            }

            if (User::isUserMainAdmin($user->getUsername())) {

                $response = ['nextPageMessageAjax' => $this->renderAjax('message_development_and_main_admin_pagination_ajax', [
                    'messages' => $messages, 'pagesMessages' => $pagesMessages,
                    'development' => $development, 'user' => $user,
                ]), 'lastPage' => $lastPage];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            if (User::isUserAdmin($user->getUsername())) {

                $response = ['nextPageMessageAjax' => $this->renderAjax('message_development_and_admin_pagination_ajax', [
                    'messages' => $messages, 'pagesMessages' => $pagesMessages,
                    'development' => $development, 'user' => $user,
                ]), 'lastPage' => $lastPage];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function actionSaveCacheMessageDevelopmentForm(int $id): bool
    {
        if(Yii::$app->request->isAjax) {

            $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
            $data = $_POST; //Массив, который будем записывать в кэш
            $conversation = ConversationDevelopment::findOne($id);
            $user = User::findOne(Yii::$app->user->getId());

            if ($conversation->getDevId() === $user->getId() || $conversation->getUserId() === $user->getId()) {

                $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId().'/';
                $key = 'formCreateMessageDevelopmentCache'; //Формируем ключ
                $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @param int $idLastMessageOnPage
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws ErrorException
     * @throws Exception
     */
    public function actionSendMessageDevelopment(int $id, int $idLastMessageOnPage)
    {
        if (Yii::$app->request->isAjax){

            $conversation = ConversationDevelopment::findOne($id);
            $development = $conversation->development;
            $user = $conversation->user;
            $formMessage = new FormCreateMessageDevelopment();
            $lastMessageOnPage = MessageDevelopment::findOne($idLastMessageOnPage);

            if ($formMessage->load(Yii::$app->request->post())) {

                if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($user->getId());
                    $formMessage->setAdresseeId($development->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageDevelopment::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        $response =  [
                            'sender' => 'admin',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $id,
                            'conversationDevelopmentForAdminAjax' => $this->renderAjax('update_conversation_development_for_admin', [
                                'conversation_development' => ConversationDevelopment::findOne($id), 'admin' => $user,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_admin', [
                                'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                elseif (User::isUserManager(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($user->getId());
                    $formMessage->setAdresseeId($development->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageDevelopment::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        $response =  [
                            'sender' => 'admin',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $id,
                            'conversationDevelopmentForAdminAjax' => $this->renderAjax('update_conversation_development_for_manager', [
                                'conversation_development' => ConversationDevelopment::findOne($id), 'manager' => $user,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_admin', [
                                'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                elseif (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($user->getId());
                    $formMessage->setAdresseeId($development->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageDevelopment::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        $response =  [
                            'sender' => 'main_admin',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $id,
                            'conversationDevelopmentForAdminMainAjax' => $this->renderAjax('update_conversation_development_for_main_admin', [
                                'conversation_development' => ConversationDevelopment::findOne($id), 'main_admin' => $user,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_main_admin', [
                                'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                elseif (User::isUserDev(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($development->getId());
                    $formMessage->setAdresseeId($user->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$development->getId().'/messages/category_technical_support/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageDevelopment::find()
                            ->andWhere(['conversation_id' => $id])
                            ->andWhere(['>', 'id', $idLastMessageOnPage])
                            ->all();

                        if (User::isUserAdmin($user->getUsername()) || User::isUserManager($user->getUsername())) {

                            $response =  [
                                'sender' => 'development',
                                'countUnreadMessages' => $development->countUnreadMessages,
                                'conversationsForDevelopmentAjax' => $this->renderAjax('update_conversations_for_development', [
                                    'allConversations' => ConversationDevelopment::find()->joinWith('user')->andWhere(['dev_id' => $development->getId()])->orderBy(['updated_at' => SORT_DESC])->all(),
                                ]),
                                'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_admin', [
                                    'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                                ]),
                            ];

                            Yii::$app->response->format = Response::FORMAT_JSON;
                            Yii::$app->response->data = $response;
                            return $response;
                        }

                        if (User::isUserMainAdmin($user->getUsername())) {

                            $response =  [
                                'sender' => 'development',
                                'countUnreadMessages' => $development->countUnreadMessages,
                                'conversationsForDevelopmentAjax' => $this->renderAjax('update_conversations_for_development', [
                                    'allConversations' => ConversationDevelopment::find()->joinWith('user')->andWhere(['dev_id' => $development->getId()])->orderBy(['updated_at' => SORT_DESC])->all(),
                                ]),
                                'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_main_admin', [
                                    'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                                ]),
                            ];

                            Yii::$app->response->format = Response::FORMAT_JSON;
                            Yii::$app->response->data = $response;
                            return $response;
                        }
                    }
                }
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionReadMessageDevelopment(int $id)
    {
        if (Yii::$app->request->isAjax){

            $model = MessageDevelopment::findOne($id);
            $model->setStatus(MessageDevelopment::READ_MESSAGE);
            if ($model->save()) {

                $user = User::findOne($model->getAdresseeId());
                $countUnreadMessagesForConversation = MessageDevelopment::find()
                    ->andWhere([
                        'adressee_id' => $model->getAdresseeId(),
                        'sender_id' => $model->getSenderId(),
                        'status' => MessageDevelopment::NO_READ_MESSAGE
                    ])->count();

                // Передаем id блока беседы
                $blockConversation = '';
                if (User::isUserMainAdmin($user->getUsername())) {
                    $blockConversation = '#conversationTechnicalSupport-' . $model->getConversationId();
                } elseif (User::isUserAdmin($user->getUsername())) {
                    $blockConversation = '#conversationTechnicalSupport-' . $model->getConversationId();
                } elseif (User::isUserDev($user->getUsername())) {
                    $blockConversation = '#adminConversation-' . $model->getConversationId();
                } elseif (User::isUserManager($user->getUsername())) {
                    $blockConversation = '#conversationTechnicalSupport-' . $model->getConversationId();
                }

                $response = [
                    'success' => true,
                    'message' => $model,
                    'countUnreadMessages' => $user->countUnreadMessages,
                    'blockConversation' => $blockConversation,
                    'countUnreadMessagesForConversation' => $countUnreadMessagesForConversation,
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * Создание новой беседы
     *
     * @param int $user_id
     * @param int $expert_id
     * @return array|bool
     */
    public function actionCreateExpertConversation(int $user_id, int $expert_id)
    {
        if(Yii::$app->request->isAjax) {

            $user = User::findOne($user_id);
            $expert = User::findOne($expert_id);
            $conversation = User::createConversationExpert($user, $expert);

            if ($conversation) {
                $response = ['success' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }

}