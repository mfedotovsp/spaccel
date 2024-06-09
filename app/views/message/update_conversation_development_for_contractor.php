<?php

use app\models\ConversationDevelopment;
use app\models\User;
use yii\helpers\Html;

/**
 * @var ConversationDevelopment $conversation_development
 * @var User $contractor
 */

?>

<!--Проверка существования аватарки-->
<?php if ($conversation_development->development->getAvatarImage()) : ?>
    <?= Html::img('@web/upload/user-'.$conversation_development->getDevId().'/avatar/'.$conversation_development->development->getAvatarImage(), ['class' => 'user_picture']) ?>
<?php else : ?>
    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
<?php endif; ?>

<!--Кол-во непрочитанных сообщений от техподдержки-->
<?php if ($contractor->countUnreadMessagesFromDev) : ?>
    <div class="countUnreadMessagesSender active"><?= $contractor->countUnreadMessagesFromDev ?></div>
<?php else : ?>
    <div class="countUnreadMessagesSender"></div>
<?php endif; ?>

<!--Проверка онлайн статуса-->
<?php if ($contractor->development->checkOnline === true) : ?>
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
