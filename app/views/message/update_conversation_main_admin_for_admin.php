<?php

use app\models\User;
use app\modules\admin\models\ConversationMainAdmin;
use yii\helpers\Html;

/**
 * @var ConversationMainAdmin $conversationAdminMain
 * @var User $admin
 */

?>

<!--Проверка существования аватарки-->
<?php if ($conversationAdminMain->mainAdmin->getAvatarImage()) : ?>
    <?= Html::img('@web/upload/user-'.$conversationAdminMain->mainAdmin->getId().'/avatar/'.$conversationAdminMain->mainAdmin->getAvatarImage(), ['class' => 'user_picture']) ?>
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
