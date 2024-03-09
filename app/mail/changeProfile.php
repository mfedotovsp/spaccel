<?php

use app\models\User;

/**
 * @var User $user
 */

?>

<div>Добрый день, <?= $user->getUsername() ?>, данные вашего профиля на сайте Spaccel.ru были обновлены.</div>

