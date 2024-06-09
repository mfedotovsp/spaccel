<?php

namespace app\modules\contractor\controllers;

use app\models\ConversationDevelopment;
use app\models\forms\FormCreateMessageDevelopment;
use app\models\MessageDevelopment;
use app\models\MessageFiles;
use app\models\PatternHttpException;
use app\models\User;
use app\modules\contractor\models\ConversationContractor;
use app\modules\contractor\models\form\SearchForm;
use app\modules\contractor\models\MessageContractor;
use Exception;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MessageController extends AppContractorController
{
    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());

        if ($action->id === 'view'){

            $conversation = ConversationContractor::findOne((int)Yii::$app->request->get('id'));
            if (!$conversation) {
                PatternHttpException::noData();
            }

            $contractor = $conversation->contractor;
            $user = $conversation->user;

            if (in_array($currentUser->getId(), [$contractor->getId(), $user->getId()], true)) {
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

            $user = $conversation->user;
            $development = $conversation->development;

            // Ограничение доступа
            if (in_array($currentUser->getId(), [$development->getId(), $user->getId()], true)) {
                // ОТКЛЮЧАЕМ CSRF
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();
        }
        elseif ($action->id === 'index') {

            $contractor = User::findOne(['id' => (int)Yii::$app->request->get('id'), 'role' => User::ROLE_CONTRACTOR]);
            if (!$contractor) {
                PatternHttpException::noData();
            }

            // Ограничение доступа
            if ($contractor->getId() === $currentUser->getId()){
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
     * @param int $id
     * @return string
     */
    public function actionIndex(int $id): string
    {
        $contractor = User::findOne($id);
        // Форма поиска
        $searchForm = new SearchForm();
        // Беседа исполнителя с техподдержкой
        $conversation_development = ConversationDevelopment::findOne(['user_id' => $id]);
        // Беседы исполнителя и проектантов
        $userConversations = ConversationContractor::find()
            ->andWhere(['contractor_id' => $id, 'role' => User::ROLE_USER])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'contractor' => $contractor,
            'searchForm' => $searchForm,
            'conversation_development' => $conversation_development,
            'userConversations' => $userConversations,
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

            if (User::isUserContractor(Yii::$app->user->identity['username'])) {

                if ($pathname === 'index') {

                    $contractor = User::findOne($id);
                    // Беседа исполнителя с техподдержкой
                    $conversation_development = ConversationDevelopment::findOne(['user_id' => $id]);
                    // Беседы исполнителя и проектантов
                    $userConversations = ConversationContractor::find()
                        ->andWhere(['contractor_id' => $id, 'role' => User::ROLE_USER])
                        ->orderBy(['updated_at' => SORT_DESC])
                        ->all();

                } elseif ($pathname === 'technical-support') {

                    $conversation_development = ConversationDevelopment::findOne($id);
                    $contractor = $conversation_development->user;
                    // Беседы исполнителя и проектантов
                    $userConversations = ConversationContractor::find()
                        ->andWhere(['contractor_id' => $contractor->getId(), 'role' => User::ROLE_USER])
                        ->orderBy(['updated_at' => SORT_DESC])
                        ->all();

                } else {
                    return false;
                }


                $response = [
                    'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $conversation_development->getId(),
                    'conversationDevelopmentForContactorAjax' => $this->renderAjax('update_conversation_development_for_contractor', [
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

                return false;
            }
        }

        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetConversationQuery(int $id)
    {
        if (Yii::$app->request->isAjax){

            $query = trim($_POST['SearchForm']['search']);
            //Беседы с проектантами, которые попали в запрос
            $conversations_query = ConversationContractor::find()->joinWith('user')
                ->andWhere(['contractor_id' => $id])
                ->andWhere(['like', 'user.username', $query])
                ->all();

            $response = ['renderAjax' => $this->renderAjax('conversations_query', ['conversations_query' => $conversations_query])];
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
    public function actionSaveCacheMessageContractorForm(int $id): bool
    {
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $data = $_POST; //Массив, который будем записывать в кэш
        $conversation = ConversationContractor::findOne($id);
        $user = User::findOne(Yii::$app->user->getId());

        if(Yii::$app->request->isAjax) {
            if (in_array($user->getId(), [$conversation->getContractorId(), $conversation->getUserId()], true)) {
                $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_contractor/conversation-'.$conversation->getId().'/';
                $key = 'formCreateMessageContractorCache'; //Формируем ключ
                $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
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
        if(Yii::$app->request->isAjax) {

            $message = MessageContractor::findOne($id);

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
     * @param int $category
     * @param int $id
     * @return \yii\console\Response|Response
     * @throws NotFoundHttpException
     */
    public function actionDownload(int $category, int $id)
    {
        $model = MessageFiles::findOne(['category' => $category, 'id' => $id]);
        if ($category === MessageFiles::CATEGORY_CONTRACTOR) {
            $message = MessageContractor::findOne($model->getMessageId());
        }
        elseif ($category === MessageFiles::CATEGORY_TECHNICAL_SUPPORT) {
            $message = MessageDevelopment::findOne($model->getMessageId());
        } else {
            throw new NotFoundHttpException('Данный файл не найден');
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
     * @return false|string
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

        if (User::isUserContractor(Yii::$app->user->identity['username'])) {

            // Все беседы исполнителя с проектантами
            $userConversations = ConversationContractor::find()
                ->andWhere(['contractor_id' => $user->getId()])
                ->andWhere(['role' => User::ROLE_USER])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();

            // Если есть кэш, добавляем его в форму сообщения
            $cache->cachePath = '../runtime/cache/forms/user-'.$user->getId().'/messages/category_technical_support/conversation-'.$conversation->getId().'/';
            $cache_form_message = $cache->get('formCreateMessageDevelopmentCache');
            if ($cache_form_message) {
                $formMessage->setDescription($cache_form_message['FormCreateMessageDevelopment']['description']);
            }

            return $this->render('technical-support-contractor', [
                'conversation_development' => $conversation,
                'formMessage' => $formMessage,
                'contractor' => $user,
                'development' => $development,
                'searchForm' => $searchForm,
                'messages' => $messages,
                'countMessages' => $countMessages,
                'pagesMessages' => $pagesMessages,
                'userConversations' => $userConversations,
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

            return $this->render('technical-support-development', [
                'formMessage' => $formMessage,
                'contractor' => $user,
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
                if (User::isUserContractor($user->username)) {
                    $blockConversation = '#conversationTechnicalSupport-' . $model->getConversationId();
                }
                elseif (User::isUserDev($user->username)) {
                    $blockConversation = '#contractorConversation-' . $model->getConversationId();
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
     * @throws ErrorException
     * @throws Exception
     * @throws NotFoundHttpException
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

                if (User::isUserContractor(Yii::$app->user->identity['username'])) {

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
                            'sender' => 'contractor',
                            'countUnreadMessages' => $user->countUnreadMessages,
                            'blockConversationDevelopment' => '#conversationTechnicalSupport-' . $id,
                            'conversationDevelopmentForContractorAjax' => $this->renderAjax('update_conversation_development_for_contractor', [
                                'conversation_development' => ConversationDevelopment::findOne($id), 'contractor' => $user,
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_contractor', [
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

                        $response =  [
                            'sender' => 'development',
                            'countUnreadMessages' => $development->countUnreadMessages,
                            'conversationsForDevelopmentAjax' => $this->renderAjax('update_conversations_for_development', [
                                'allConversations' => ConversationDevelopment::find()->joinWith('user')->andWhere(['dev_id' => $development->getId()])->orderBy(['updated_at' => SORT_DESC])->all(),
                            ]),
                            'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_contractor', [
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

            /**
             * @var MessageDevelopment[] $messages
             * @var MessageDevelopment $lastMessage
             */
            // Проверяем является ли страница последней
            $lastPage = false;
            $lastMessage = MessageDevelopment::find()
                ->andWhere(['conversation_id' => $id])
                ->orderBy(['id' => SORT_ASC])
                ->one();

            foreach ($messages as $message) {
                if ($message->getId() === $lastMessage->getId()) {
                    $lastPage = true;
                }
            }

            $response = ['nextPageMessageAjax' => $this->renderAjax('message_development_and_contractor_pagination_ajax', [
                'messages' => $messages, 'pagesMessages' => $pagesMessages,
                'development' => $development, 'user' => $user,
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
        if(Yii::$app->request->isAjax) {

            $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
            $data = $_POST; //Массив, который будем записывать в кэш
            $conversation = ConversationDevelopment::findOne($id);
            $user = User::findOne(Yii::$app->user->getId());

            if (in_array($user->getId(), [$conversation->getDevId(), $conversation->getUserId()], true)) {
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
     */
    public function actionCheckNewMessagesDevelopment(int $id, int $idLastMessageOnPage)
    {
        if(Yii::$app->request->isAjax) {

            $conversation = ConversationDevelopment::findOne($id);
            $development = $conversation->development;
            $user = $conversation->user;
            $lastMessageOnPage = MessageDevelopment::findOne($idLastMessageOnPage);
            $messages = MessageDevelopment::find()
                ->andWhere(['conversation_id' => $conversation->getId()])
                ->andWhere(['>', 'id', $idLastMessageOnPage])
                ->all();

            if ($messages) {
                $response = [
                    'checkNewMessages' => true,
                    'addNewMessagesAjax' => $this->renderAjax('check_new_messages_development_and_contractor', [
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
     * Создание новой беседы
     *
     * @param int $user_id
     * @param int $contractor_id
     * @return array|bool
     */
    public function actionCreateContractorConversation(int $user_id, int $contractor_id)
    {
        if(Yii::$app->request->isAjax) {

            $user = User::findOne($user_id);
            $contractor = User::findOne($contractor_id);
            $conversation = User::createConversationContractor($user, $contractor);

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
