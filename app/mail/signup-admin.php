<?php

use app\models\User;

/**
 * @var User $user
 * @var string $unsubscribeLink
 */


echo 'На сайте ' . Yii::$app->params['siteName'] . ' был зарегистрирован новый пользователь: '. $user->getUsername() . '(' . $user->getTextRole() . ')';

?>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>

