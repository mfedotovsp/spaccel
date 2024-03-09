<?php

use app\models\ClientSettings;
use app\models\ConversationDevelopment;
use yii\helpers\Html;
use app\models\User;

/**
 * @var ConversationDevelopment[] $conversations_query
 */

?>

<?php if (!$conversations_query) : ?>

    <div class="block_no_search_result">По вашему запросу не найдено ни одного пользователя...</div>

<?php else : ?>

    <?php foreach ($conversations_query as $conversation) : ?>

        <?php if (User::isUserSimple($conversation->user->getUsername())) : ?>

            <div class="conversation-link" id="conversation-<?= $conversation->getId() ?>">

                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture_search']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
                <?php endif; ?>

                <?= $conversation->user->getUsername() ?>
            </div>

        <?php elseif (User::isUserExpert($conversation->user->getUsername())) : ?>

            <div class="conversation-link" id="expertConversation-<?= $conversation->getId() ?>">

                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture_search']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
                <?php endif; ?>

                <?= $conversation->user->getUsername() ?>
            </div>

        <?php elseif (User::isUserMainAdmin($conversation->user->getUsername()) || User::isUserManager($conversation->user->getUsername())) : ?>

            <div class="conversation-link" id="adminConversation-<?= $conversation->getId() ?>">

                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture_search']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
                <?php endif; ?>

                <?= $conversation->user->getUsername() ?>
            </div>

        <?php elseif (User::isUserAdmin($conversation->user->getUsername())) : ?>

            <?php
            $clientUser = $conversation->user->clientUser;
            $clientSettings = ClientSettings::findOne(['client_id' => $clientUser->getClientId()]);
            $adminCompany = User::findOne(['id' => $clientSettings->getAdminId()]);
            ?>

            <?php if (User::isUserMainAdmin($adminCompany->getUsername())) : ?>

                <div class="conversation-link" id="adminConversation-<?= $conversation->getId() ?>">

            <?php else : ?>

                <div class="conversation-link" id="clientAdminConversation-<?= $conversation->getId() ?>">

            <?php endif; ?>

                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture_search']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
                <?php endif; ?>

                <?= $conversation->user->getUsername() ?>
            </div>

        <?php elseif (User::isUserAdminCompany($conversation->user->getUsername())) : ?>

            <div class="conversation-link" id="clientAdminConversation-<?= $conversation->getId() ?>">

                <?php if ($conversation->user->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture_search']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
                <?php endif; ?>

                <?= $conversation->user->getUsername() ?>
            </div>

        <?php endif; ?>

    <?php endforeach; ?>

<?php endif;
