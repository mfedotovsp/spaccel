<?php

use app\models\ConversationAdmin;
use app\models\ConversationDevelopment;
use app\models\User;
use app\modules\expert\models\ConversationExpert;
use yii\helpers\Html;

$this->title = 'Сообщения';
$this->registerCssFile('@web/css/message-index.css');

/**
 * @var User $user
 * @var User $admin
 * @var ConversationAdmin $conversation_admin
 * @var User $development
 * @var ConversationDevelopment $conversation_development
 * @var ConversationExpert[] $conversationsExpert
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

    <div class="row profile_menu">

<!--        <div class="link_open_and_close_menu_profile">Открыть меню профиля</div>-->
<!---->
<!--        --><?php //echo Html::a('Данные пользователя', ['/profile/index', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Сводные таблицы', ['/profile/result', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Трэкшн карты', ['/profile/roadmap', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Протоколы', ['/profile/report', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Презентации', ['/profile/presentation', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>

    </div>
    
    <div class="row">
        <div class="col-sm-6 col-lg-4 hide_block_menu_profile">

<!--            --><?php //echo Html::a('Данные пользователя', ['/profile/index', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Сводные таблицы', ['/profile/result', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Трэкшн карты', ['/profile/roadmap', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Протоколы', ['/profile/report', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Презентации', ['/profile/presentation', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>

        </div>
    </div>

    <div class="row all_content_messages">

        <div class="col-sm-6 col-lg-4 conversation-list-menu">

            <div id="conversation-list-menu">

                <!--Блок беседы с трекером и техподдержкой-->
                <div class="containerAdminConversation">

                    <div class="container-user_messages" id="adminConversation-<?= $conversation_admin->getId() ?>">

                        <!--Проверка существования аватарки-->
                        <?php if ($admin->getAvatarImage() ) : ?>
                            <?= Html::img('@web/upload/user-'.$admin->getId().'/avatar/'.$admin->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Кол-во непрочитанных сообщений от Трекера-->
                        <?php if ($user->countUnreadMessagesFromAdmin) : ?>
                            <div class="countUnreadMessagesSender active"><?= $user->countUnreadMessagesFromAdmin ?></div>
                        <?php else : ?>
                            <div class="countUnreadMessagesSender"></div>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($admin->checkOnline === true) : ?>
                            <div class="checkStatusOnlineUser active"></div>
                        <?php else : ?>
                            <div class="checkStatusOnlineUser"></div>
                        <?php endif; ?>

                        <div class="container_user_messages_text_content">

                            <div class="row block_top">

                                <div class="col-xs-8">Трекер</div>

                                <div class="col-xs-4 text-right">
                                    <?php if ($conversation_admin->lastMessage) : ?>
                                        <?= date('d.m.y H:i', $conversation_admin->lastMessage->getCreatedAt()) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($conversation_admin->lastMessage) : ?>
                                <div class="block_bottom_exist_message">

                                    <?php if ($conversation_admin->lastMessage->sender->getAvatarImage()) : ?>
                                        <?= Html::img('@web/upload/user-'.$conversation_admin->lastMessage->getSenderId().'/avatar/'.$conversation_admin->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                    <?php else : ?>
                                        <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                    <?php endif; ?>

                                    <div>
                                        <?php if ($conversation_admin->lastMessage->getDescription()) : ?>
                                            <?= $conversation_admin->lastMessage->getDescription() ?>
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
                        <?php if ($development->getAvatarImage()) : ?>
                            <?= Html::img('@web/upload/user-'.$development->getId().'/avatar/'.$development->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Кол-во непрочитанных сообщений от Техподдержки-->
                        <?php if ($user->countUnreadMessagesFromDev) : ?>
                            <div class="countUnreadMessagesSender active"><?= $user->countUnreadMessagesFromDev ?></div>
                        <?php else : ?>
                            <div class="countUnreadMessagesSender"></div>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($development->checkOnline === true) : ?>
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

                <!--Блок бесед с экспертами-->
                <div class="containerExpertConversations">

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
                                <?php if ($user->getCountUnreadMessagesExpertFromUser($conversation->getExpertId())) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $user->getCountUnreadMessagesExpertFromUser($conversation->getExpertId()) ?></div>
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
<?php $this->registerJsFile('@web/js/message_index.js'); ?>
