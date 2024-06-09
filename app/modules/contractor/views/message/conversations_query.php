<?php

use app\modules\contractor\models\ConversationContractor;
use yii\helpers\Html;
use app\models\User;

/**
 * @var ConversationContractor[] $conversations_query
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

        <?php endif; ?>

    <?php endforeach; ?>

<?php endif; ?>
