<?php

use app\modules\admin\models\ConversationMainAdmin;
use yii\helpers\Html;

/**
 * @var ConversationMainAdmin[] $allConversations
 */

?>

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
