<?php

use app\models\User;
use yii\helpers\Html;

/**
 * @var User $user
 */

?>

<!--Проверка существования аватарки-->
<?php if ($user->getAvatarImage()) : ?>
    <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'user_picture']) ?>
<?php else : ?>
    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
<?php endif; ?>

<!--Проверка онлайн статуса-->
<?php if ($user->checkOnline === true) : ?>
    <div class="checkStatusOnlineUser active"></div>
<?php else : ?>
    <div class="checkStatusOnlineUser"></div>
<?php endif; ?>

<div class="block-fio-and-date-last-visit">
    <div class="block-fio"><?= $user->getUsername() ?></div>
    <div class="block-date-last-visit">
        <?php if(is_string($user->checkOnline)) : ?>
            Пользователь был в сети <?= $user->checkOnline ?>
        <?php endif; ?>
    </div>
</div>
