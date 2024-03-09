<?php

use app\modules\admin\models\ConversationMainAdmin;
use app\modules\admin\models\ConversationManager;
use app\modules\expert\models\ConversationExpert;
use yii\helpers\Html;

/**
 * @var ConversationMainAdmin[] $conversations_query
 * @var ConversationExpert[] $expert_conversations_query
 * @var ConversationManager[] $manager_conversations_query
 */

?>

<?php if (!$conversations_query && !$expert_conversations_query) : ?>

    <div class="block_no_search_result">По вашему запросу не найдено ни одного пользователя...</div>

<?php else : ?>

    <?php foreach ($manager_conversations_query as $conversation) : ?>

        <div class="conversation-link" id="managerConversation-<?= $conversation->getId() ?>">

            <?php if ($conversation->manager->getAvatarImage()) : ?>
                <?= Html::img('@web/upload/user-'.$conversation->getManagerId().'/avatar/'.$conversation->manager->getAvatarImage(), ['class' => 'user_picture_search']) ?>
            <?php else : ?>
                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
            <?php endif; ?>

            <?= $conversation->manager->getUsername() ?>
        </div>

    <?php endforeach; ?>

    <?php foreach ($conversations_query as $conversation) : ?>

        <div class="conversation-link" id="adminConversation-<?= $conversation->getId() ?>">

            <?php if ($conversation->admin->getAvatarImage()) : ?>
                <?= Html::img('@web/upload/user-'.$conversation->getAdminId().'/avatar/'.$conversation->admin->getAvatarImage(), ['class' => 'user_picture_search']) ?>
            <?php else : ?>
                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
            <?php endif; ?>

            <?= $conversation->admin->getUsername() ?>
        </div>

    <?php endforeach; ?>

    <?php foreach ($expert_conversations_query as $conversation) : ?>

        <div class="conversation-link" id="expertConversation-<?= $conversation->getId() ?>">

            <?php if ($conversation->expert->getAvatarImage()) : ?>
                <?= Html::img('@web/upload/user-'.$conversation->getExpertId().'/avatar/'.$conversation->expert->getAvatarImage(), ['class' => 'user_picture_search']) ?>
            <?php else : ?>
                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
            <?php endif; ?>

            <?= $conversation->expert->getUsername() ?>
        </div>

    <?php endforeach; ?>

<?php endif; ?>
