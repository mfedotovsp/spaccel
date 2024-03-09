<?php

use app\models\forms\AvatarForm;
use app\models\forms\PasswordChangeForm;
use app\models\forms\ProfileForm;
use app\models\User;

$this->title = 'Админка | Профиль';
$this->registerCssFile('@web/css/profile-style.css');

/**
 * @var User $user
 * @var int $count_users
 * @var int $countProjects
 * @var ProfileForm $profile
 * @var PasswordChangeForm $passwordChangeForm
 * @var AvatarForm $avatarForm
 */

?>

<div class="user-index">

    <div class="row profile_menu" style="height: 51px;">

    </div>


    <div class="data_user_content">

        <?= $this->render('ajax_data_profile', [
            'user' => $user,
            'count_users' => $count_users,
            'countProjects' => $countProjects,
            'profile' => $profile,
            'passwordChangeForm' => $passwordChangeForm,
            'avatarForm' => $avatarForm,
        ]) ?>

    </div>

    <!--Модальные окна-->
    <?= $this->render('modal') ?>

</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/profile_admin_index.js'); ?>