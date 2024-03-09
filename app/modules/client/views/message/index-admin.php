<?php

use app\models\ConversationAdmin;
use app\models\ConversationDevelopment;
use app\models\User;
use app\modules\admin\models\ConversationMainAdmin;
use app\modules\admin\models\form\SearchForm;
use app\modules\expert\models\ConversationExpert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Сообщения';
$this->registerCssFile('@web/css/admin-message-index.css');

/**
 * @var User $admin
 * @var SearchForm $searchForm
 * @var ConversationMainAdmin $conversationAdminMain
 * @var ConversationDevelopment $conversation_development
 * @var ConversationAdmin[] $allConversations
 * @var ConversationExpert[] $conversationsExpert
 */

?>

<div class="admin-message-index">

    <!--Preloader begin-->
    <div id="preloader">
        <div id="cont">
            <div class="round"></div>
            <div class="round"></div>
            <div class="round"></div>
            <div class="round"></div>
        </div>
        <div id="loading">Loading</div>
    </div>
    <!--Preloader end-->

    <div class="row message_menu">

        <div class="col-sm-6 col-lg-4 search-block">

            <?php $form = ActiveForm::begin([
                'id' => 'search_user_conversation',
                'action' => Url::to(['/client/message/get-conversation-query', 'id' => $admin->getId()]),
                'options' => ['class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

                <?= $form->field($searchForm, 'search', ['template' => '{input}'])
                    ->textInput([
                        'id' => 'search_conversation',
                        'placeholder' => 'Поиск',
                        'class' => 'style_form_field_respond',
                        'autocomplete' => 'off'])
                    ->label(false) ?>

            <?php ActiveForm::end(); ?>

            <!--Беседы полученные в запросе поиска (по умолчанию это все доступные пользователи)-->
            <div class="conversations_query" id="conversations_query">
                <!--Сюда добавляем результат поиска-->
            </div>

        </div>

        <div class="col-sm-6 col-lg-8">

        </div>

    </div>

    <div class="row all_content_messages">

        <div class="col-sm-6 col-lg-4 conversation-list-menu">

            <div id="conversation-list-menu">

                <!--Блок беседы с главным админом и техподдержкой-->
                <div class="containerAdminMainConversation">

                    <div class="container-user_messages" id="adminMainConversation-<?= $conversationAdminMain->getId() ?>">

                        <!--Проверка существования аватарки-->
                        <?php if ($conversationAdminMain->mainAdmin->getAvatarImage()) : ?>
                            <?= Html::img('@web/upload/user-'.$conversationAdminMain->getMainAdminId().'/avatar/'.$conversationAdminMain->mainAdmin->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Кол-во непрочитанных сообщений от главного админа-->
                        <?php if ($admin->countUnreadMessagesFromMainAdmin) : ?>
                            <div class="countUnreadMessagesSender active"><?= $admin->countUnreadMessagesFromMainAdmin ?></div>
                        <?php else : ?>
                            <div class="countUnreadMessagesSender"></div>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($admin->mainAdmin->checkOnline === true) : ?>
                            <div class="checkStatusOnlineUser active"></div>
                        <?php else : ?>
                            <div class="checkStatusOnlineUser"></div>
                        <?php endif; ?>

                        <div class="container_user_messages_text_content">

                            <div class="row block_top">

                                <div class="col-xs-8">Главный администратор</div>

                                <div class="col-xs-4 text-right">
                                    <?php if ($conversationAdminMain->lastMessage) : ?>
                                        <?= date('d.m.y H:i', $conversationAdminMain->lastMessage->getCreatedAt()) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($conversationAdminMain->lastMessage) : ?>
                                <div class="block_bottom_exist_message">

                                    <?php if ($conversationAdminMain->lastMessage->sender->getAvatarImage()) : ?>
                                        <?= Html::img('@web/upload/user-'.$conversationAdminMain->lastMessage->getSenderId().'/avatar/'.$conversationAdminMain->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                    <?php else : ?>
                                        <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                    <?php endif; ?>

                                    <div>
                                        <?php if ($conversationAdminMain->lastMessage->getDescription()) : ?>
                                            <?= $conversationAdminMain->lastMessage->getDescription() ?>
                                        <?php else : ?>
                                            ...
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="block_bottom_not_exist_message">Нет сообщений</div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="container-user_messages" id="conversationTechnicalSupport-<?= $conversation_development->getId() ?>">

                        <!--Проверка существования аватарки-->
                        <?php if ($conversation_development->development->getAvatarImage()) : ?>
                            <?= Html::img('@web/upload/user-'.$conversation_development->getDevId().'/avatar/'.$conversation_development->development->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Кол-во непрочитанных сообщений от техподдержки-->
                        <?php if ($admin->countUnreadMessagesFromDev) : ?>
                            <div class="countUnreadMessagesSender active"><?= $admin->countUnreadMessagesFromDev ?></div>
                        <?php else : ?>
                            <div class="countUnreadMessagesSender"></div>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($admin->development->checkOnline === true) : ?>
                            <div class="checkStatusOnlineUser active"></div>
                        <?php else : ?>
                            <div class="checkStatusOnlineUser"></div>
                        <?php endif; ?>

                        <div class="container_user_messages_text_content">

                            <div class="row block_top">

                                <div class="col-xs-8">Техническая поддержка</div>

                                <div class="col-xs-4 text-right">
                                    <?php if ($conversation_development->lastMessage) : ?>
                                        <?= date('d.m.y H:i', $conversation_development->lastMessage->getCreatedAt()) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($conversation_development->lastMessage) : ?>
                                <div class="block_bottom_exist_message">

                                    <?php if ($conversation_development->lastMessage->sender->getAvatarImage()) : ?>
                                        <?= Html::img('@web/upload/user-'.$conversation_development->lastMessage->getSenderId().'/avatar/'.$conversation_development->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                    <?php else : ?>
                                        <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                    <?php endif; ?>

                                    <div>
                                        <?php if ($conversation_development->lastMessage->getDescription()) : ?>
                                            <?= $conversation_development->lastMessage->getDescription() ?>
                                        <?php else : ?>
                                            ...
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="block_bottom_not_exist_message">Нет сообщений</div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!--Блок для загрузки бесед с экспертами-->
                <div class="containerForAllExpertConversations">

                    <div class="title_block_conversation">
                        <div class="title">Эксперты</div>
                    </div>

                    <?php if ($conversationsExpert) : ?>

                        <?php foreach ($conversationsExpert as $conversation) : ?>

                            <div class="container-user_messages" id="expertConversation-<?= $conversation->getId() ?>">

                                <!--Проверка существования аватарки-->
                                <?php if ($conversation->expert->getAvatarImage()) : ?>
                                    <?= Html::img('@web/upload/user-'.$conversation->getExpertId().'/avatar/'.$conversation->expert->getAvatarImage(), ['class' => 'user_picture']) ?>
                                <?php else : ?>
                                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                                <?php endif; ?>

                                <!--Кол-во непрочитанных сообщений от эксперта-->
                                <?php if ($conversation->user->getCountUnreadMessagesExpertFromUser($conversation->getExpertId())) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $conversation->user->getCountUnreadMessagesExpertFromUser($conversation->getExpertId()) ?></div>
                                <?php else : ?>
                                    <div class="countUnreadMessagesSender"></div>
                                <?php endif; ?>

                                <!--Проверка онлайн статуса-->
                                <?php if ($conversation->expert->checkOnline === true) : ?>
                                    <div class="checkStatusOnlineUser active"></div>
                                <?php else : ?>
                                    <div class="checkStatusOnlineUser"></div>
                                <?php endif; ?>

                                <div class="container_user_messages_text_content">

                                    <div class="row block_top">

                                        <div class="col-xs-8"><?= $conversation->expert->getUsername() ?></div>

                                        <div class="col-xs-4 text-right">
                                            <?php if ($conversation->lastMessage) : ?>
                                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($conversation->lastMessage) : ?>
                                        <div class="block_bottom_exist_message">

                                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                            <?php else : ?>
                                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                            <?php endif; ?>

                                            <div>
                                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                                    <?= $conversation->lastMessage->getDescription() ?>
                                                <?php else : ?>
                                                    ...
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div class="text-center block_not_conversations">Нет экспертов</div>

                    <?php endif; ?>

                </div>

                <!--Блок для загрузки бесед с проектантами-->
                <div class="containerForAllUserConversations">

                    <div class="title_block_conversation">
                        <div class="title">Проектанты</div>
                    </div>

                    <?php if ($allConversations) : ?>

                        <?php foreach ($allConversations as $conversation) : ?>

                            <div class="container-user_messages" id="conversation-<?= $conversation->getId() ?>">

                                <!--Проверка существования аватарки-->
                                <?php if ($conversation->user->getAvatarImage()) : ?>
                                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture']) ?>
                                <?php else : ?>
                                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                                <?php endif; ?>

                                <!--Кол-во непрочитанных сообщений от пользователя-->
                                <?php if ($conversation->user->countUnreadMessagesFromUser) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $conversation->user->countUnreadMessagesFromUser ?></div>
                                <?php else : ?>
                                    <div class="countUnreadMessagesSender"></div>
                                <?php endif; ?>

                                <!--Проверка онлайн статуса-->
                                <?php if ($conversation->user->checkOnline === true) : ?>
                                    <div class="checkStatusOnlineUser active"></div>
                                <?php else : ?>
                                    <div class="checkStatusOnlineUser"></div>
                                <?php endif; ?>

                                <div class="container_user_messages_text_content">

                                    <div class="row block_top">

                                        <div class="col-xs-8"><?= $conversation->user->getUsername() ?></div>

                                        <div class="col-xs-4 text-right">
                                            <?php if ($conversation->lastMessage) : ?>
                                                <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($conversation->lastMessage) : ?>
                                        <div class="block_bottom_exist_message">

                                            <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                                <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                            <?php else : ?>
                                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                            <?php endif; ?>

                                            <div>
                                                <?php if ($conversation->lastMessage->getDescription()) : ?>
                                                    <?= $conversation->lastMessage->getDescription() ?>
                                                <?php else : ?>
                                                    ...
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div class="text-center block_not_conversations">Нет проектантов</div>

                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-8">
            <div class="message_index_block_right_info">
                Выберите пользователя (перейдите к беседе с пользователем)
            </div>
        </div>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/admin_message_index.js'); ?>
