<?php

use app\models\forms\AvatarForm;
use app\models\forms\PasswordChangeForm;
use app\models\forms\ProfileForm;
use app\models\User;

if (User::isUserSimple(Yii::$app->user->identity['username'])) {
    $this->title = 'Мой профиль';
} else {
    $this->title = 'Профиль проектанта';
}

$this->registerCssFile('@web/css/profile-style.css');

/**
 * @var User $user
 * @var ProfileForm $profile
 * @var PasswordChangeForm $passwordChangeForm
 * @var AvatarForm $avatarForm
 */
?>

<div class="user-index">

    <div class="row">
        <div class="col-xs-12 header-title-mobile"><?= $this->title ?></div>
    </div>

    <div class="data_user_content">
        <?= $this->render('ajax_data_profile', [
            'user' => $user,
            'profile' => $profile,
            'passwordChangeForm' => $passwordChangeForm,
            'avatarForm' => $avatarForm
        ]) ?>
    </div>

    <!--Модальные окна-->
    <?= $this->render('modal') ?>

</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/profile_index.js'); ?>