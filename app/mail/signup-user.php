<?php

use app\models\User;
use yii\helpers\Html;

/**
 * @var User $user
 * @var string $unsubscribeLink
 */

echo '<p>Добрый день! '.Html::encode($user->getUsername()).', Вы зарегистрировались на сайте ' . Yii::$app->params['siteName'] . ' </p>';
echo '<p>Ожидайте активации Вашего профиля. Мы известим Вас об этом в новом письме.</p>';

?>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>
