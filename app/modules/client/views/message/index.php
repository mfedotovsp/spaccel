<?php

use app\models\ConversationDevelopment;
use app\models\User;
use app\modules\admin\models\ConversationMainAdmin;
use app\modules\admin\models\ConversationManager;
use app\modules\admin\models\form\SearchForm;
use app\modules\expert\models\ConversationExpert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Сообщения';
$this->registerCssFile('@web/css/admin-message-index.css');

/**
 * @var User $main_admin
 * @var SearchForm $searchForm
 * @var ConversationDevelopment $conversation_development
 * @var ConversationExpert[] $expertConversations
 * @var ConversationManager[] $managerConversations
 * @var ConversationMainAdmin[] $allConversations
 */

?>

<div class="message-index">

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
                'action' => Url::to(['/client/message/get-admin-conversation-query', 'id' => $main_admin->getId()]),
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

                <!--Блок беседы с техподдержкой-->
                <div class="containerForTechnicalSupportConversation">

                    <div class="container-user_messages" id="conversationTechnicalSupport-<?= $conversation_development->getId() ?>">

                        <!--Проверка существования аватарки-->
                        <?php if ($conversation_development->development->getAvatarImage()) : ?>
                            <?= Html::img('@web/upload/user-'.$conversation_development->getDevId().'/avatar/'.$conversation_development->development->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Кол-во непрочитанных сообщений от техподдержки-->
                        <?php if ($main_admin->countUnreadMessagesFromDev) : ?>
                            <div class="countUnreadMessagesSender active"><?= $main_admin->countUnreadMessagesFromDev ?></div>
                        <?php else : ?>
                            <div class="countUnreadMessagesSender"></div>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($main_admin->development->checkOnline === true) : ?>
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

                <!--Блок для бесед с менеджерами-->
                <div class="containerForManagerConversations">

                    <div class="title_block_conversation">
                        <div class="title">Менеджеры</div>
                    </div>

                    <?php if ($managerConversations) : ?>

                        <?php foreach ($managerConversations as $conversation) : ?>

                            <div class="container-user_messages" id="managerConversation-<?= $conversation->getId() ?>">

                                <!--Проверка существования аватарки-->
                                <?php if ($conversation->manager->getAvatarImage()) : ?>
                                    <?= Html::img('@web/upload/user-'.$conversation->getManagerId().'/avatar/'.$conversation->manager->getAvatarImage(), ['class' => 'user_picture']) ?>
                                <?php else : ?>
                                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                                <?php endif; ?>

                                <!--Кол-во непрочитанных сообщений от менеджера-->
                                <?php if ($conversation->manager->getCountUnreadMessagesFromManager($conversation->getUserId())) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $conversation->manager->getCountUnreadMessagesFromManager($conversation->getUserId()) ?></div>
                                <?php else : ?>
                                    <div class="countUnreadMessagesSender"></div>
                                <?php endif; ?>

                                <!--Проверка онлайн статуса-->
                                <?php if ($conversation->manager->checkOnline === true) : ?>
                                    <div class="checkStatusOnlineUser active"></div>
                                <?php else : ?>
                                    <div class="checkStatusOnlineUser"></div>
                                <?php endif; ?>

                                <div class="container_user_messages_text_content">

                                    <div class="row block_top">

                                        <div class="col-xs-8"><?= $conversation->manager->getUsername() ?></div>

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

                        <div class="text-center block_not_conversations">Нет менеджеров</div>

                    <?php endif; ?>

                </div>

                <!--Блок для бесед с трекерами-->
                <div class="containerForAllConversations">

                    <div class="title_block_conversation">
                        <div class="title">Трекеры</div>
                    </div>

                    <?php if ($allConversations) : ?>

                        <?php foreach ($allConversations as $conversation) : ?>

                            <div class="container-user_messages" id="adminConversation-<?= $conversation->getId() ?>">

                                <!--Проверка существования аватарки-->
                                <?php if ($conversation->admin->getAvatarImage()) : ?>
                                    <?= Html::img('@web/upload/user-'.$conversation->getAdminId().'/avatar/'.$conversation->admin->getAvatarImage(), ['class' => 'user_picture']) ?>
                                <?php else : ?>
                                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                                <?php endif; ?>

                                <!--Кол-во непрочитанных сообщений от трекера-->
                                <?php if ($conversation->admin->countUnreadMessagesMainAdminFromAdmin) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $conversation->admin->countUnreadMessagesMainAdminFromAdmin ?></div>
                                <?php else : ?>
                                    <div class="countUnreadMessagesSender"></div>
                                <?php endif; ?>

                                <!--Проверка онлайн статуса-->
                                <?php if ($conversation->admin->checkOnline === true) : ?>
                                    <div class="checkStatusOnlineUser active"></div>
                                <?php else : ?>
                                    <div class="checkStatusOnlineUser"></div>
                                <?php endif; ?>

                                <div class="container_user_messages_text_content">

                                    <div class="row block_top">

                                        <div class="col-xs-8"><?= $conversation->admin->getUsername() ?></div>

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

                        <div class="text-center block_not_conversations">Нет трекеров</div>

                    <?php endif; ?>

                </div>


                <!--Блок для бесед с экспертами-->
                <div class="containerForExpertConversations">

                    <div class="title_block_conversation">
                        <div class="title">Эксперты</div>
                    </div>

                    <?php if ($expertConversations) : ?>

                        <?php foreach ($expertConversations as $conversation) : ?>

                            <div class="container-user_messages" id="expertConversation-<?= $conversation->getId() ?>">

                                <!--Проверка существования аватарки-->
                                <?php if ($conversation->expert->getAvatarImage()) : ?>
                                    <?= Html::img('@web/upload/user-'.$conversation->getExpertId().'/avatar/'.$conversation->expert->getAvatarImage(), ['class' => 'user_picture']) ?>
                                <?php else : ?>
                                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                                <?php endif; ?>

                                <!--Кол-во непрочитанных сообщений от эксперта-->
                                <?php if ($conversation->expert->countUnreadMessagesMainAdminFromExpert) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $conversation->expert->countUnreadMessagesMainAdminFromExpert ?></div>
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
<?php $this->registerJsFile('@web/js/main_admin_message_index.js'); ?>
