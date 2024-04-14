<?php

use app\models\User;

/**
 * @var User $user
 * @var string $unsubscribeLink
 */

?>

<div>Добрый день, <?= $user->getUsername() ?>, данные вашего профиля на сайте <?= Yii::$app->params['siteName'] ?> были обновлены.</div>
<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>

