<?php

use app\models\ConversationAdmin;
use app\modules\expert\models\ConversationExpert;
use yii\helpers\Html;

/**
 * @var ConversationAdmin[] $conversations_query
 * @var ConversationExpert[] $expert_conversations_query
 */

?>

<?php if (!$conversations_query && !$expert_conversations_query) : ?>

    <div class="block_no_search_result">По вашему запросу не найдено ни одного пользователя...</div>

<?php else : ?>

    <?php foreach ($conversations_query as $conversation) : ?>

        <div class="conversation-link" id="conversation-<?= $conversation->getId() ?>">

            <?php if ($conversation->user->getAvatarImage()) : ?>
                <?= Html::img('@web/upload/user-'.$conversation->getUserId().'/avatar/'.$conversation->user->getAvatarImage(), ['class' => 'user_picture_search']) ?>
            <?php else : ?>
                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_search_default']) ?>
            <?php endif; ?>

            <?= $conversation->user->getUsername() ?>
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

<?php endif;
