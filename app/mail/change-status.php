<?php

use yii\helpers\Html;
use app\models\User;

/**
 * @var User $user
 * @var string $unsubscribeLink
 */

if ($user->getStatus() === User::STATUS_ACTIVE) {

    echo '<p>Добрый день! '.Html::encode($user->getUsername()).', Ваш профиль на сайте активирован. </p>';
    echo '<p>Теперь Вы можете приступить к работе на нашем сайте. Для этого перейдите по ссылке ' .
        Html::a(Yii::$app->params['siteName'],
            Yii::$app->urlManager->createAbsoluteUrl(
                [
                    '/',
                ]
            )) . ' .</p>';

}else {
    echo '<p>Добрый день! '.Html::encode($user->getUsername()).', Ваш профиль на сайте заблокирован. </p>';
    echo '<p>Обратитесь по этому вопросу к администратору сайта. Для этого перейдите по ссылке ' .
        Html::a(Yii::$app->params['siteName'],
            Yii::$app->urlManager->createAbsoluteUrl(
                [
                    '/',
                ]
            )) . ' .</p>';
}

?>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>



