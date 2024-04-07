<?php

use app\models\User;

/**
 * @var User $user
 */

?>

<div>Добрый день, <?= $user->getUsername() ?>, данные вашего профиля на сайте <?= Yii::$app->params['siteName'] ?> были обновлены.</div>

