<?php

use yii\bootstrap\Modal;
use app\models\User;

/**
 * @var User $user
 */

?>


<?php if ($user->getStatus() === User::STATUS_NOT_ACTIVE) : ?>

    <?php
    Modal::begin([
        'options' => ['id' => 'user_status'],
        'size' => 'modal-md',
        'header' => '<h3 class="text-center" style="color: #F2F2F2; padding: 0 30px;">Ожидайте активации вашего стутуса администратором</h3>',
    ]);
    ?>

    <h4 class="text-center" style="color: #F2F2F2; padding: 0 30px;">
        Мы отправим Вам письмо на электронную почту, когда будет принято данное решение.
    </h4>

    <?php Modal::end(); ?>


<?php elseif ($user->getStatus() === User::STATUS_DELETED) : ?>

    <?php
    Modal::begin([
        'options' => [
            'id' => 'user_status',
        ],
        'size' => 'modal-md',
        'header' => '<h3 class="text-center" style="color: #F2F2F2; padding: 0 30px;">Ваша учетная запись заблокирована</h3>',
    ]);
    ?>

    <h4 class="text-center" style="color: #F2F2F2; padding: 0 30px;">
        Обратитесь по этому вопросу к администратору.
    </h4>

    <?php Modal::end(); ?>

<?php endif; ?>
