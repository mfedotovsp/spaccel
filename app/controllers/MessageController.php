<?php


namespace app\controllers;

use app\models\ConversationDevelopment;
use app\models\forms\FormCreateMessageAdmin;
use app\models\forms\FormCreateMessageDevelopment;
use app\models\MessageDevelopment;
use app\models\MessageFiles;
use app\models\PatternHttpException;
use app\models\User;
use app\models\ConversationAdmin;
use app\modules\admin\models\ConversationMainAdmin;
use app\modules\admin\models\form\SearchForm;
use app\modules\admin\models\MessageMainAdmin;
use app\modules\contractor\models\ConversationContractor;
use app\modules\contractor\models\MessageContractor;
use app\modules\expert\models\ConversationExpert;
use app\modules\contractor\models\form\FormCreateMessageContractor;
use app\modules\expert\models\form\FormCreateMessageExpert;
use app\modules\expert\models\MessageExpert;
use Yii;
use app\models\MessageAdmin;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\Pagination;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class MessageController
 * @package app\controllers
 */
class MessageController extends AppUserPartController
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

            $conversation = ConversationAdmin::findOne((int)Yii::$app->request->get('id'));
            if (!$conversation) {
                PatternHttpException::noData();
            }

            if (($conversation->user->getId() === Yii::$app->user->getId()) || ($conversation->admin->getId() === Yii::$app->user->getId())){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }
        elseif ($action->id === 'technical-support'){

            $conversation = ConversationDevelopment::findOne((int)Yii::$app->request->get('id'));
            if (!$conversation) {
                PatternHttpException::noData();
            }

            if (($conversation->user->getId() === Yii::$app->user->getId()) || ($conversation->development->getId() === Yii::$app->user->getId())){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }
        elseif ($action->id === 'expert'){

            $conversation = ConversationExpert::findOne((int)Yii::$app->request->get('id'));
            if (!$conversation) {
                PatternHttpException::noData();
            }

            if (($conversation->user->getId() === Yii::$app->user->getId()) || ($conversation->expert->getId() === Yii::$app->user->getId())){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }
        elseif ($action->id === 'contractor'){

            $conversation = ConversationContractor::findOne((int)Yii::$app->request->get('id'));
            if (!$conversation) {
                PatternHttpException::noData();
            }

            if (($conversation->user->getId() === Yii::$app->user->getId()) || ($conversation->contractor->getId() === Yii::$app->user->getId())){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }
        elseif ($action->id === 'index'){

            $user = User::findOne((int)Yii::$app->request->get('id'));
            if (!$user) {
                PatternHttpException::noData();
            }

            if (($user->getId() === Yii::$app->user->getId())){
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }
        else{
            return parent::beforeAction($action);
        }

    }


    /**
     * @param $id
     * @return string
     */
    public function actionIndex($id): string
    {
        $user = User::findOne($id);
        $admin = User::findOne($user->getIdAdmin());
        $development = $user->development;
        $conversation_admin = ConversationAdmin::findOne(['user_id' => $id]);
        $conversation_development = ConversationDevelopment::findOne(['user_id' => $id]);
        $conversationsExpert = ConversationExpert::find()->andWhere(['user_id' => $id])
            ->orderBy(['updated_at' => SORT_DESC])->all();
        $conversationsContractor = ConversationContractor::find()->andWhere(['user_id' => $id])
            ->orderBy(['updated_at' => SORT_DESC])->all();

        return $this->render('index', [
            'user' => $user,
            'admin' => $admin,
            'conversation_admin' => $conversation_admin,
            'development' => $development,
            'conversation_development' => $conversation_development,
            'conversationsExpert' => $conversationsExpert,
            'conversationsContractor' => $conversationsContractor
        ]);
    }


    /**
     * @param int $id
     * @param string $pathname
     * @return array|bool
     */
    public function actionGetListUpdateConversations(int $id, string $pathname)
    {
        if (Yii::$app->request->isAjax) {

            if (User::isUserSimple(Yii::$app->user->identity['username'])) {

                if ($pathname === 'index') {

                    $user = User::findOne($id);
                    $admin = User::findOne(['id' => $user->getIdAdmin()]);
                    $development = $user->development;
                    $conversation_admin = ConversationAdmin::findOne(['user_id' => $user->getId()]);
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $user->getId()]);

                } elseif ($pathname === 'view') {

                    $conversation_admin = ConversationAdmin::findOne($id);
                    $user = $conversation_admin->user;
                    $admin = $conversation_admin->admin;
                    $development = $user->development;
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $user->getId()]);

                } elseif ($pathname === 'technical-support') {

                    $conversation_development = ConversationDevelopment::findOne($id);
                    $user = $conversation_development->user;
                    $development = $conversation_development->development;
                    $conversation_admin = ConversationAdmin::findOne(['user_id' => $user->getId()]);
                    $admin = $conversation_admin->admin;

                } elseif ($pathname === 'expert') {

                    $conversation = ConversationExpert::findOne($id);
                    $user = $conversation->user;
                    $development = $user->development;
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $user->getId()]);
                    $conversation_admin = ConversationAdmin::findOne(['user_id' => $user->getId()]);
                    $admin = $conversation_admin->admin;

                } elseif ($pathname === 'contractor') {

                    $conversation = ConversationContractor::findOne($id);
                    $user = $conversation->user;
                    $development = $user->development;
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $user->getId()]);
                    $conversation_admin = ConversationAdmin::findOne(['user_id' => $user->getId()]);
                    $admin = $conversation_admin->admin;

                } else {
                    return false;
                }

                $response = [
                    'conversationAdminForUserAjax' => $this->renderAjax('update_conversation_admin_for_user', [
                        'conversation_admin' => $conversation_admin, 'user' => $user, 'admin' => $admin,
                    ]),
                    'conversationDevelopmentForUserAjax' => $this->renderAjax('update_conversation_development_for_user', [
                        'conversation_development' => $conversation_development, 'development' => $development, 'user' => $user,
                    ]),
                    'blockConversationAdmin' => '#adminConversation-' . $conversation_admin->getId(),
                    'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $conversation_development->getId(),
                    'conversationsExpertForUser' => $this->renderAjax('update_conversations_expert_for_user',[
                        'conversationsExpert' => ConversationExpert::find()->andWhere(['user_id' => $user->getId()])
                            ->orderBy(['updated_at' => SORT_DESC])->all(), 'user' => $user,
                    ]),
                    'conversationsContractorForUser' => $this->renderAjax('update_conversations_contractor_for_user',[
                        'conversationsContractor' => ConversationContractor::find()->andWhere(['user_id' => $user->getId()])
                            ->orderBy(['updated_at' => SORT_DESC])->all(), 'user' => $user,
                    ]),
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

                if ($pathname === 'view') {

                    $conversation = ConversationAdmin::findOne($id);
                    $admin = $conversation->admin;
                    $conversationAdminMain = ConversationMainAdmin::findOne(['admin_id' => $admin->getId()]);
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $admin->getId()]);

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
            }

            if (User::isUserDev(Yii::$app->user->identity['username'])) {

                if ($pathname === 'technical-support') {

                    $conversation = ConversationDevelopment::findOne($id);
                    $development = $conversation->development;

                    $response = [
                        'conversationsForDevelopmentAjax' => $this->renderAjax('update_conversations_for_development', [
                            'allConversations' => ConversationDevelopment::find()->joinWith('user')->andWhere(['dev_id' => $development->getId()])->orderBy(['updated_at' => SORT_DESC])->all(),
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }

            if (User::isUserExpert(Yii::$app->user->identity['username'])) {

                if ($pathname === 'expert') {

                    $conversation = ConversationExpert::findOne($id);
                    $expert = $conversation->expert;
                    // Беседа эксерта с техподдержкой
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $expert->getId()]);
                    // Беседа эксперта с главным админом
                    $conversationAdminMain = ConversationExpert::findOne(['expert_id' => $expert->getId(), 'user_id' => $expert->mainAdmin->getId()]);
                    // Беседы эксперта и трекеров
                    $adminConversations = ConversationExpert::find()
                        ->andWhere(['expert_id' => $expert->getId(), 'role' => User::ROLE_ADMIN])
                        ->orderBy(['updated_at' => SORT_DESC])
                        ->all();
                    // Беседы эксперта и проектантов
                    $userConversations = ConversationExpert::find()
                        ->andWhere(['expert_id' => $expert->getId(), 'role' => User::ROLE_USER])
                        ->orderBy(['updated_at' => SORT_DESC])
                        ->all();


                    $response = [
                        'blockConversationAdminMain' => '#adminMainConversation-' . $conversationAdminMain->getId(),
                        'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $conversation_development->getId(),
                        'conversationAdminMainForExpertAjax' => $this->renderAjax('update_conversation_main_admin_for_expert', [
                            'conversationAdminMain' => $conversationAdminMain, 'expert' => $expert,
                        ]),
                        'conversationDevelopmentForExpertAjax' => $this->renderAjax('update_conversation_development_for_expert', [
                            'conversation_development' => $conversation_development, 'expert' => $expert,
                        ]),
                        'conversationsAdminForExpertAjax' => $this->renderAjax('update_conversations_admin_for_expert', [
                            'adminConversations' => $adminConversations, 'expert' => $expert,
                        ]),
                        'conversationsUserForExpertAjax' => $this->renderAjax('update_conversations_user_for_expert', [
                            'userConversations' => $userConversations, 'expert' => $expert,
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }

            if (User::isUserContractor(Yii::$app->user->identity['username'])) {

                if ($pathname === 'contractor') {

                    $conversation = ConversationContractor::findOne($id);
                    $contractor = $conversation->contractor;
                    // Беседа исполнителя с техподдержкой
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $contractor->getId()]);
                    // Беседы исполнителя и проектантов
                    $userConversations = ConversationContractor::find()
                        ->andWhere(['contractor_id' => $contractor->getId(), 'role' => User::ROLE_USER])
                        ->orderBy(['updated_at' => SORT_DESC])
                        ->all();


                    $response = [
                        'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $conversation_development->getId(),
                        'conversationDevelopmentForContractorAjax' => $this->renderAjax('update_conversation_development_for_contractor', [
                            'conversation_development' => $conversation_development, 'contractor' => $contractor,
                        ]),
                        'conversationsUserForContractorAjax' => $this->renderAjax('update_conversations_user_for_contractor', [
                            'userConversations' => $userConversations, 'contractor' => $contractor,
                        ]),
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionCheckingUnreadMessageAdmin(int $id)
    {
        $message = MessageAdmin::findOne($id);

        if(Yii::$app->request->isAjax) {

            if ($message->getStatus() === MessageAdmin::READ_MESSAGE) {

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
    public function actionCheckNewMessagesAdmin(int $id, int $idLastMessageOnPage)
    {
        $conversation = ConversationAdmin::findOne($id);
        $user = $conversation->user;
        $admin = $conversation->admin;
        $lastMessageOnPage = MessageAdmin::findOne($idLastMessageOnPage);
        $messages = MessageAdmin::find()->andWhere(['conversation_id' => $conversation->getId()])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

        if(Yii::$app->request->isAjax) {

            if ($messages) {

                $response = [
                    'checkNewMessages' => true,
                    'addNewMessagesAjax' => $this->renderAjax('check_new_messages_admin', [
                        'messages' => $messages, 'user' => $user, 'admin' => $admin, 'lastMessageOnPage' => $lastMessageOnPage,
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
     * @return array|bool
     */
    public function actionGetCountUnreadMessages(int $id)
    {
        $user = User::findOne($id);
        $countUnreadMessages = $user->countUnreadMessages;

        if(Yii::$app->request->isAjax) {

            $response = ['countUnreadMessages' => $countUnreadMessages];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }


    /**
     * @param int $id
     * @return bool|string
     */
    public function actionView(int $id)
    {
        $conversation = ConversationAdmin::findOne($id);
        $formMessage = new FormCreateMessageAdmin();
        $user = $conversation->user;
        $admin = $conversation->admin;
        $searchForm = new SearchForm(); // Форма поиска
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        // Вывод сообщений через пагинацию
        $query = MessageAdmin::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);
        $countMessages = MessageAdmin::find()->andWhere(['conversation_id' => $id])->count();

        if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

            $this->layout = '@app/modules/admin/views/layouts/main';
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

            User::isUserMainAdmin($admin->mainAdmin->getUsername()) ? $module = 'admin' : $module = 'client';

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$admin->getId().'/messages/category_admin/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageAdminCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageAdmin']['description']);
            }

            return $this->render('view-admin', [
                'formMessage' => $formMessage,
                'user' => $user,
                'admin' => $admin,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'conversationAdminMain' => $conversationAdminMain,
                'conversation_development' => $conversation_development,
                'expertConversations' => $expertConversations,
                'allConversations' => $allConversations,
                'module' => $module,
            ]);
        }

        if (User::isUserSimple(Yii::$app->user->identity['username'])) {

            $development = $user->development;
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $user->getId()]);
            // Все беседы проектанта с экспертами
            $expertConversations = ConversationExpert::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Все беседы проектанта с исполнителями
            $contractorConversations = ConversationContractor::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_admin/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageAdminCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageAdmin']['description']);
            }

            return $this->render('view', [
                'conversation' => $conversation,
                'formMessage' => $formMessage,
                'user' => $user,
                'admin' => $admin,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'development' => $development,
                'expertConversations' => $expertConversations,
                'contractorConversations' => $contractorConversations,
                'conversation_development' => $conversation_development,
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
        $conversation = ConversationAdmin::findOne($id);
        $user = $conversation->user;
        $admin = $conversation->admin;
        $query = MessageAdmin::find()->andWhere(['conversation_id' => $id])->andWhere(['<', 'id', $final])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);

        // Проверяем является ли страница последней
        $lastPage = false;
        /** @var MessageAdmin $lastMessage */
        $lastMessage = MessageAdmin::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_ASC])->one();
        /** @var MessageAdmin[] $messages */
        foreach ($messages as $message) {
            if ($message->getId() === $lastMessage->getId()) {
                $lastPage = true;
            }
        }

        if(Yii::$app->request->isAjax) {

            $response = ['nextPageMessageAjax' => $this->renderAjax('message_pagination_ajax', [
                'messages' => $messages, 'pagesMessages' => $pagesMessages,
                'user' => $user, 'admin' => $admin,
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
    public function actionSaveCacheMessageAdminForm(int $id): bool
    {
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $data = $_POST; //Массив, который будем записывать в кэш
        $conversation = ConversationAdmin::findOne($id);
        $user = User::findOne(Yii::$app->user->getId());

        if(Yii::$app->request->isAjax) {

            if ($conversation->user->getId() === $user->getId() || $conversation->admin->getId() === $user->getId()) {

                $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_admin/conversation-'.$conversation->getId().'/';
                $key = 'formCreateMessageAdminCache'; //Формируем ключ
                $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
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
    public function actionSendMessage(int $id, int $idLastMessageOnPage)
    {
        $conversation = ConversationAdmin::findOne($id);
        $user = $conversation->user;
        $admin = $conversation->admin;
        $formMessage = new FormCreateMessageAdmin();
        $lastMessageOnPage = MessageAdmin::findOne($idLastMessageOnPage);

        if ($formMessage->load(Yii::$app->request->post())) {

            if (Yii::$app->request->isAjax){

                if (User::isUserAdmin(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($admin->getId());
                    $formMessage->setAdresseeId($user->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$admin->getId().'/messages/category_admin/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageAdmin::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response =  [
                            'sender' => 'admin',
                            'countUnreadMessages' => $admin->countUnreadMessages,
                            'conversationsUserForAdminAjax'=> $this->renderAjax('update_conversations_user_for_admin', [
                                'allConversations' => ConversationAdmin::find()->joinWith('user')
                                    ->andWhere(['user.id_admin' => $admin->getId()])->andWhere(['admin_id' => $admin->getId()])
                                    ->orderBy(['updated_at' => SORT_DESC])->all(),
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_admin', [
                                'messages' => $messages, 'user' => $user, 'admin' => $admin, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($user->getId());
                    $formMessage->setAdresseeId($admin->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_admin/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageAdmin::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response =  [
                            'sender' => 'user',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'blockConversationAdmin' => '#adminConversation-' . $id,
                            'conversationAdminForUserAjax' => $this->renderAjax('update_conversation_admin_for_user', [
                                'conversation_admin' => ConversationAdmin::findOne($id), 'user' => $user, 'admin' => $admin,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_admin', [
                                'messages' => $messages, 'user' => $user, 'admin' => $admin, 'lastMessageOnPage' => $lastMessageOnPage,
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
     * @return array|bool
     */
    public function actionReadMessageAdmin(int $id)
    {
        if (Yii::$app->request->isAjax){
            $model = MessageAdmin::findOne($id);
            $model->setStatus(MessageAdmin::READ_MESSAGE);
            if ($model->save()) {

                $user = User::findOne($model->getAdresseeId());
                $countUnreadMessagesForConversation = MessageAdmin::find()->andWhere(['adressee_id' => $model->getAdresseeId(), 'sender_id' => $model->getSenderId(), 'status' => MessageAdmin::NO_READ_MESSAGE])->count();
                // Передаем id блока беседы
                $blockConversation = '';
                if (User::isUserSimple($user->getUsername())) {
                    $blockConversation = '#adminConversation-' . $model->getConversationId();
                }
                elseif (User::isUserAdmin($user->getUsername())) {
                    $blockConversation = '#conversation-' . $model->getConversationId();
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
        }
        elseif ($category === MessageFiles::CATEGORY_MAIN_ADMIN) {
            $message = MessageMainAdmin::findOne($model->getMessageId());
        }
        elseif ($category === MessageFiles::CATEGORY_TECHNICAL_SUPPORT) {
            $message = MessageDevelopment::findOne($model->getMessageId());
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
        $user = $conversation->user;
        $development = $conversation->development;
        $searchForm = new SearchForm(); // Форма поиска
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        // Вывод сообщений через пагинацию
        $query = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);
        $countMessages = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->count();

        if (User::isUserSimple(Yii::$app->user->identity['username'])) {

            $admin = User::findOne($user->getIdAdmin());
            $conversation_admin = ConversationAdmin::findOne(['user_id' => $user->getId()]);
            $expertConversations = ConversationExpert::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            $contractorConversations = ConversationContractor::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageDevelopmentCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageDevelopment']['description']);
            }

            return $this->render('technical-support', [
                'conversation' => $conversation,
                'formMessage' => $formMessage,
                'user' => $user,
                'admin' => $admin,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'development' => $development,
                'conversation_admin' => $conversation_admin,
                'expertConversations' => $expertConversations,
                'contractorConversations' => $contractorConversations,
            ]);
        }

        if (User::isUserDev(Yii::$app->user->identity['username'])) {

            $this->layout = '@app/modules/admin/views/layouts/main';
            // Все беседы техподдержки
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

            return $this->render('technical-support-development', [
                'formMessage' => $formMessage,
                'user' => $user,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'development' => $development,
                'allConversations' => $allConversations,
            ]);
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionCheckingUnreadMessageDevelopment(int $id)
    {
        $message = MessageDevelopment::findOne($id);

        if(Yii::$app->request->isAjax) {

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

        $conversation = ConversationDevelopment::findOne($id);
        $development = $conversation->development;
        $user = $conversation->user;
        $lastMessageOnPage = MessageDevelopment::findOne($idLastMessageOnPage);
        $messages = MessageDevelopment::find()->andWhere(['conversation_id' => $conversation->getId()])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

        if(Yii::$app->request->isAjax) {

            if ($messages) {

                $response = [
                    'checkNewMessages' => true,
                    'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development', [
                        'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
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
     * @param int $page
     * @param int $final
     * @return array|bool
     */
    public function actionGetPageMessageDevelopment (int $id, int $page, int $final)
    {
        $conversation = ConversationDevelopment::findOne($id);
        $user = $conversation->user;
        $development = $conversation->development;
        $query = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->andWhere(['<', 'id', $final])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);

        // Проверяем является ли страница последней
        $lastPage = false;
        /** @var MessageDevelopment $lastMessage */
        $lastMessage = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_ASC])->one();
        /** @var MessageDevelopment[] $messages */
        foreach ($messages as $message) {
            if ($message->getId() === $lastMessage->getId()) {
                $lastPage = true;
            }
        }

        if(Yii::$app->request->isAjax) {

            $response = ['nextPageMessageAjax' => $this->renderAjax('message_development_pagination_ajax', [
                'messages' => $messages, 'pagesMessages' => $pagesMessages,
                'user' => $user, 'development' => $development,
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
    public function actionSaveCacheMessageDevelopmentForm(int $id): bool
    {
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $data = $_POST; //Массив, который будем записывать в кэш
        $conversation = ConversationDevelopment::findOne($id);
        $user = User::findOne(Yii::$app->user->getId());

        if(Yii::$app->request->isAjax) {

            if ($conversation->user->getId() === $user->getId() || $conversation->development->getId() === $user->getId()) {

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
     * @throws ErrorException
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionSendMessageDevelopment(int $id, int $idLastMessageOnPage)
    {
        $conversation = ConversationDevelopment::findOne($id);
        $user = $conversation->user;
        $development = $conversation->development;
        $formMessage = new FormCreateMessageDevelopment();
        $lastMessageOnPage = MessageDevelopment::findOne($idLastMessageOnPage);

        if ($formMessage->load(Yii::$app->request->post())) {

            if (Yii::$app->request->isAjax){

                if (User::isUserDev(Yii::$app->user->identity['username'])) {

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
                        $messages = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response = [
                            'sender' => 'development',
                            'countUnreadMessages' => $development->countUnreadMessages,
                            'conversationsForDevelopmentAjax' => $this->renderAjax('update_conversations_for_development', [
                                'allConversations' => ConversationDevelopment::find()->joinWith('user')->andWhere(['dev_id' => $development->getId()])->orderBy(['updated_at' => SORT_DESC])->all(),
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development', [
                                'messages' => $messages, 'development' => $development, 'user' => $user, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                }

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {

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
                        $messages = MessageDevelopment::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response =  [
                            'sender' => 'user',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $id,
                            'conversationDevelopmentForUserAjax' => $this->renderAjax('update_conversation_development_for_user', [
                                'conversation_development' => ConversationDevelopment::findOne($id), 'development' => $development, 'user' => $user,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development', [
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
                $countUnreadMessagesForConversation = MessageDevelopment::find()->andWhere(['adressee_id' => $model->getAdresseeId(), 'sender_id' => $model->getSenderId(), 'status' => MessageDevelopment::NO_READ_MESSAGE])->count();
                // Передаем id блока беседы
                $blockConversation = '';
                if (User::isUserSimple($user->getUsername())) {
                    $blockConversation = '#conversationTechnicalSupport-' . $model->getConversationId();
                }
                elseif (User::isUserDev($user->getUsername())) {
                    $blockConversation = '#conversation-' . $model->getConversationId();
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
     * @return bool|string
     */
    public function actionContractor(int $id)
    {
        $conversation = ConversationContractor::findOne($id);
        $formMessage = new FormCreateMessageContractor();
        $contractor = $conversation->contractor;
        $user = $conversation->user;
        $searchForm = new SearchForm(); // Форма поиска
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        // Вывод сообщений через пагинацию
        $query = MessageContractor::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);
        $countMessages = MessageContractor::find()->andWhere(['conversation_id' => $id])->count();

        if (User::isUserContractor(Yii::$app->user->identity['username'])) {

            // Беседа исполнителя с техподдержкой
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $contractor->getId()]);
            // Все беседы исполнителя с проектантами
            $userConversations = ConversationContractor::find()
                ->andWhere(['contractor_id' => $contractor->getId()])
                ->andWhere(['role' => User::ROLE_USER])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$contractor->getId().'/messages/category_contractor/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageContractorCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageContractor']['description']);
            }

            return $this->render('contractor-message-contractor', [
                'conversation' => $conversation,
                'formMessage' => $formMessage,
                'contractor' => $contractor,
                'user' => $user,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'conversation_development' => $conversation_development,
                'userConversations' => $userConversations,
            ]);
        }

        if (User::isUserSimple(Yii::$app->user->identity['username'])) {

            $adminConversation = ConversationAdmin::findOne(['user_id' => $user->getId()]);
            $admin = $adminConversation->admin;
            $development = $user->development;
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $user->getId()]);
            // Все беседы проектанта с экспертами
            $expertConversations = ConversationExpert::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Все беседы проектанта с исполнителями
            $contractorConversations = ConversationContractor::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_contractor/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageContractorCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageContractor']['description']);
            }

            return $this->render('contractor-message-user', [
                'conversation' => $conversation,
                'adminConversation' => $adminConversation,
                'admin' => $admin,
                'formMessage' => $formMessage,
                'user' => $user,
                'contractor' => $contractor,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'development' => $development,
                'expertConversations' => $expertConversations,
                'contractorConversations' => $contractorConversations,
                'conversation_development' => $conversation_development,
            ]);
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionReadMessageContractor(int $id)
    {
        if (Yii::$app->request->isAjax){
            $model = MessageContractor::findOne($id);
            $model->setStatus(MessageContractor::READ_MESSAGE);
            if ($model->save()) {

                $user = User::findOne($model->getAdresseeId());
                $countUnreadMessagesForConversation = MessageContractor::find()->andWhere(['adressee_id' => $model->getAdresseeId(), 'sender_id' => $model->getSenderId(), 'status' => MessageContractor::NO_READ_MESSAGE])->count();
                // Передаем id блока беседы
                $blockConversation = '';
                if (User::isUserSimple($user->getUsername())) {
                    $blockConversation = '#contractorConversation-' . $model->getConversationId();
                }
                elseif (User::isUserContractor($user->getUsername())) {
                    $blockConversation = '#conversation-' . $model->getConversationId();
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
    public function actionCheckingUnreadMessageContractor(int $id)
    {
        $message = MessageContractor::findOne($id);

        if(Yii::$app->request->isAjax) {

            if ($message->getStatus() === MessageContractor::READ_MESSAGE) {

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
     * @throws ErrorException
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionSendMessageContractor(int $id, int $idLastMessageOnPage)
    {
        $conversation = ConversationContractor::findOne($id);
        $user = $conversation->user;
        $contractor = $conversation->contractor;
        $formMessage = new FormCreateMessageContractor();
        $lastMessageOnPage = MessageContractor::findOne($idLastMessageOnPage);

        if ($formMessage->load(Yii::$app->request->post())) {

            if (Yii::$app->request->isAjax){

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($user->getId());
                    $formMessage->setAdresseeId($contractor->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_contractor/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageContractor::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response =  [
                            'sender' => 'user',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'conversationsContractorForUser' => $this->renderAjax('update_conversations_contractor_for_user',[
                                'conversationsContractor' => ConversationContractor::find()->andWhere(['user_id' => $user->getId()])
                                    ->orderBy(['updated_at' => SORT_DESC])->all(), 'user' => $user,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_contractor', [
                                'messages' => $messages, 'user' => $user, 'contractor' => $contractor, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                } elseif (User::isUserContractor(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($contractor->getId());
                    $formMessage->setAdresseeId($user->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$contractor->getId().'/messages/category_contractor/conversation-'.$conversation->getId().'/';
                        if (file_exists($cachePathDelete)) FileHelper::removeDirectory($cachePathDelete);

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageContractor::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response =  [
                            'sender' => 'contractor',
                            'countUnreadMessages' => $contractor->countUnreadMessages,
                            'conversationsUserForContractorAjax' => $this->renderAjax('update_conversations_user_for_contractor', [
                                'userConversations' => ConversationContractor::find()->andWhere(['contractor_id' => $contractor->getId(), 'role' => User::ROLE_USER])
                                    ->orderBy(['updated_at' => SORT_DESC])->all(), 'contractor' => $contractor,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_contractor', [
                                'messages' => $messages, 'user' => $user, 'contractor' => $contractor, 'lastMessageOnPage' => $lastMessageOnPage,
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
     * @return bool
     */
    public function actionSaveCacheMessageContractorForm(int $id): bool
    {
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $data = $_POST; //Массив, который будем записывать в кэш
        $conversation = ConversationContractor::findOne($id);
        $user = User::findOne(Yii::$app->user->getId());

        if(Yii::$app->request->isAjax) {

            if ($conversation->user->getId() === $user->getId() || $conversation->contractor->getId() === $user->getId()) {

                $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_contractor/conversation-'.$conversation->getId().'/';
                $key = 'formCreateMessageContractorCache'; //Формируем ключ
                $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @param int $page
     * @param int $final
     * @return array|bool
     */
    public function actionGetPageMessageContractor(int $id, int $page, int $final)
    {
        $conversation = ConversationContractor::findOne($id);
        $user = $conversation->user;
        $contractor = $conversation->contractor;
        $query = MessageContractor::find()->andWhere(['conversation_id' => $id])->andWhere(['<', 'id', $final])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);

        // Проверяем является ли страница последней
        $lastPage = false;
        /** @var MessageContractor $lastMessage */
        $lastMessage = MessageContractor::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_ASC])->one();
        /** @var MessageContractor[] $messages */
        foreach ($messages as $message) {
            if ($message->getId() === $lastMessage->getId()) {
                $lastPage = true;
            }
        }

        if(Yii::$app->request->isAjax) {

            $response = ['nextPageMessageAjax' => $this->renderAjax('message_contractor_pagination_ajax', [
                'messages' => $messages, 'pagesMessages' => $pagesMessages,
                'user' => $user, 'contractor' => $contractor,
            ]), 'lastPage' => $lastPage];
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
    public function actionCheckNewMessagesContractor(int $id, int $idLastMessageOnPage)
    {
        $conversation = ConversationContractor::findOne($id);
        $user = $conversation->user;
        $contractor = $conversation->contractor;
        $lastMessageOnPage = MessageContractor::findOne($idLastMessageOnPage);
        $messages = MessageContractor::find()->andWhere(['conversation_id' => $conversation->getId()])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

        if(Yii::$app->request->isAjax) {

            if ($messages) {

                $response = [
                    'checkNewMessages' => true,
                    'addNewMessagesAjax' => $this->renderAjax('check_new_messages_contractor', [
                        'messages' => $messages, 'user' => $user, 'contractor' => $contractor, 'lastMessageOnPage' => $lastMessageOnPage,
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
     * @return bool|string
     */
    public function actionExpert(int $id)
    {
        $conversation = ConversationExpert::findOne($id);
        $formMessage = new FormCreateMessageExpert();
        $expert = $conversation->expert;
        $user = $conversation->user;
        $searchForm = new SearchForm(); // Форма поиска
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        // Вывод сообщений через пагинацию
        $query = MessageExpert::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);
        $countMessages = MessageExpert::find()->andWhere(['conversation_id' => $id])->count();

        if (User::isUserExpert(Yii::$app->user->identity['username'])) {

            // Беседа эксперта с техподдержкой
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $expert->getId()]);
            // Беседа эксперта с админом организации
            $conversationAdminMain = ConversationExpert::findOne(['expert_id' => $expert->getId(), 'user_id' => $expert->mainAdmin->getId()]);
            // Все беседы эксперта с трекерами
            $adminConversations = ConversationExpert::find()
                ->andWhere(['expert_id' => $expert->getId()])
                ->andWhere(['role' => User::ROLE_ADMIN])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();
            // Все беседы эксперта с проектантами
            $userConversations = ConversationExpert::find()
                ->andWhere(['expert_id' => $expert->getId()])
                ->andWhere(['role' => User::ROLE_USER])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$expert->getId().'/messages/category_expert/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageExpertCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageExpert']['description']);
            }

            return $this->render('expert-message-expert', [
                'conversation' => $conversation,
                'formMessage' => $formMessage,
                'expert' => $expert,
                'user' => $user,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'conversation_development' => $conversation_development,
                'conversationAdminMain' => $conversationAdminMain,
                'adminConversations' => $adminConversations,
                'userConversations' => $userConversations,
            ]);
        }

        if (User::isUserSimple(Yii::$app->user->identity['username'])) {

            $adminConversation = ConversationAdmin::findOne(['user_id' => $user->getId()]);
            $admin = $adminConversation->admin;
            $development = $user->development;
            $conversation_development = ConversationDevelopment::findOne(['user_id' => $user->getId()]);
            // Все беседы проектанта с экспертами
            $expertConversations = ConversationExpert::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Все беседы проектанта с исполнителями
            $contractorConversations = ConversationContractor::find()
                ->andWhere(['user_id' => $user->getId()])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_expert/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageExpertCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageExpert']['description']);
            }

            return $this->render('expert-message-user', [
                'conversation' => $conversation,
                'adminConversation' => $adminConversation,
                'admin' => $admin,
                'formMessage' => $formMessage,
                'user' => $user,
                'expert' => $expert,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'development' => $development,
                'expertConversations' => $expertConversations,
                'contractorConversations' => $contractorConversations,
                'conversation_development' => $conversation_development,
            ]);
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionReadMessageExpert(int $id)
    {
        if (Yii::$app->request->isAjax){
            $model = MessageExpert::findOne($id);
            $model->setStatus(MessageExpert::READ_MESSAGE);
            if ($model->save()) {

                $user = User::findOne($model->getAdresseeId());
                $countUnreadMessagesForConversation = MessageExpert::find()->andWhere(['adressee_id' => $model->getAdresseeId(), 'sender_id' => $model->getSenderId(), 'status' => MessageExpert::NO_READ_MESSAGE])->count();
                // Передаем id блока беседы
                $blockConversation = '';
                if (User::isUserSimple($user->getUsername())) {
                    $blockConversation = '#expertConversation-' . $model->getConversationId();
                }
                elseif (User::isUserExpert($user->getUsername())) {
                    $blockConversation = '#conversation-' . $model->getConversationId();
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
    public function actionCheckingUnreadMessageExpert(int $id)
    {
        $message = MessageExpert::findOne($id);

        if(Yii::$app->request->isAjax) {

            if ($message->getStatus() === MessageExpert::READ_MESSAGE) {

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
     * @throws ErrorException
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionSendMessageExpert(int $id, int $idLastMessageOnPage)
    {
        $conversation = ConversationExpert::findOne($id);
        $user = $conversation->user;
        $expert = $conversation->expert;
        $formMessage = new FormCreateMessageExpert();
        $lastMessageOnPage = MessageExpert::findOne($idLastMessageOnPage);

        if ($formMessage->load(Yii::$app->request->post())) {

            if (Yii::$app->request->isAjax){

                if (User::isUserSimple(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($user->getId());
                    $formMessage->setAdresseeId($expert->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_expert/conversation-'.$conversation->getId();
                        if (file_exists($cachePathDelete)) {
                            FileHelper::removeDirectory($cachePathDelete);
                        }

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageExpert::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response =  [
                            'sender' => 'user',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'conversationsExpertForUser' => $this->renderAjax('update_conversations_expert_for_user',[
                                'conversationsExpert' => ConversationExpert::find()->andWhere(['user_id' => $user->getId()])
                                    ->orderBy(['updated_at' => SORT_DESC])->all(), 'user' => $user,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_expert', [
                                'messages' => $messages, 'user' => $user, 'expert' => $expert, 'lastMessageOnPage' => $lastMessageOnPage,
                            ]),
                        ];

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }

                } elseif (User::isUserExpert(Yii::$app->user->identity['username'])) {

                    $formMessage->setConversationId($id);
                    $formMessage->setSenderId($expert->getId());
                    $formMessage->setAdresseeId($user->getId());
                    if ($formMessage->create()) {

                        //Удаление кэша формы создания сообщения
                        $cachePathDelete = '../runtime/cache/forms/user-'.$expert->getId().'/messages/category_expert/conversation-'.$conversation->getId().'/';
                        if (file_exists($cachePathDelete)) FileHelper::removeDirectory($cachePathDelete);

                        // Сообщения, которых ещё нет на странице
                        $messages = MessageExpert::find()->andWhere(['conversation_id' => $id])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

                        $response =  [
                            'sender' => 'expert',
                            'countUnreadMessages' => $expert->countUnreadMessages,
                            'conversationsUserForExpertAjax' => $this->renderAjax('update_conversations_user_for_expert', [
                                'userConversations' => ConversationExpert::find()->andWhere(['expert_id' => $expert->getId(), 'role' => User::ROLE_USER])
                                    ->orderBy(['updated_at' => SORT_DESC])->all(), 'expert' => $expert,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_expert', [
                                'messages' => $messages, 'user' => $user, 'expert' => $expert, 'lastMessageOnPage' => $lastMessageOnPage,
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
     * @return bool
     */
    public function actionSaveCacheMessageExpertForm(int $id): bool
    {
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $data = $_POST; //Массив, который будем записывать в кэш
        $conversation = ConversationExpert::findOne($id);
        $user = User::findOne(Yii::$app->user->getId());

        if(Yii::$app->request->isAjax) {

            if ($conversation->user->getId() === $user->getId() || $conversation->expert->getId() === $user->getId()) {

                $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_expert/conversation-'.$conversation->getId().'/';
                $key = 'formCreateMessageExpertCache'; //Формируем ключ
                $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @param int $page
     * @param int $final
     * @return array|bool
     */
    public function actionGetPageMessageExpert(int $id, int $page, int $final)
    {
        $conversation = ConversationExpert::findOne($id);
        $user = $conversation->user;
        $expert = $conversation->expert;
        $query = MessageExpert::find()->andWhere(['conversation_id' => $id])->andWhere(['<', 'id', $final])->orderBy(['id' => SORT_DESC]);
        $pagesMessages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => 20]);
        $messages = $query->offset($pagesMessages->offset)->limit($pagesMessages->pageSize)->all();
        $messages = array_reverse($messages);

        // Проверяем является ли страница последней
        $lastPage = false;
        /** @var MessageExpert $lastMessage */
        $lastMessage = MessageExpert::find()->andWhere(['conversation_id' => $id])->orderBy(['id' => SORT_ASC])->one();
        /** @var MessageExpert[] $messages */
        foreach ($messages as $message) {
            if ($message->getId() === $lastMessage->getId()) {
                $lastPage = true;
            }
        }

        if(Yii::$app->request->isAjax) {

            $response = ['nextPageMessageAjax' => $this->renderAjax('message_expert_pagination_ajax', [
                'messages' => $messages, 'pagesMessages' => $pagesMessages,
                'user' => $user, 'expert' => $expert,
            ]), 'lastPage' => $lastPage];
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
    public function actionCheckNewMessagesExpert(int $id, int $idLastMessageOnPage)
    {
        $conversation = ConversationExpert::findOne($id);
        $user = $conversation->user;
        $expert = $conversation->expert;
        $lastMessageOnPage = MessageExpert::findOne($idLastMessageOnPage);
        $messages = MessageExpert::find()->andWhere(['conversation_id' => $conversation->getId()])->andWhere(['>', 'id', $idLastMessageOnPage])->all();

        if(Yii::$app->request->isAjax) {

            if ($messages) {

                $response = [
                    'checkNewMessages' => true,
                    'addNewMessagesAjax' => $this->renderAjax('check_new_messages_expert', [
                        'messages' => $messages, 'user' => $user, 'expert' => $expert, 'lastMessageOnPage' => $lastMessageOnPage,
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
}
