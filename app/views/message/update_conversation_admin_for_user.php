<?php

use app\models\ConversationAdmin;
use app\models\User;
use yii\helpers\Html;

/**
 * @var ConversationAdmin $conversation_admin
 * @var User $user
 * @var User $admin
 */

?>


<!--Проверка существования аватарки-->
<?php if ($admin->getAvatarImage()) : ?>
    <?= Html::img('@web/upload/user-'.$admin->getId().'/avatar/'.$admin->getAvatarImage(), ['class' => 'user_picture']) ?>
<?php else : ?>
    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
<?php endif; ?>

<!--Кол-во непрочитанных сообщений от Админа-->
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
