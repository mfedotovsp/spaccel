<?php

use yii\helpers\Html;
use app\models\User;

/**
 * @var User $user
 */

if ($user->getStatus() === User::STATUS_ACTIVE) {

    echo '<p>Добрый день! '.Html::encode($user->getUsername()).', Ваш профиль на сайте активирован. </p>';
    echo '<p>Теперь Вы можете приступить к работе на нашем сайте. Для этого перейдите по ссылке ' .
        Html::a('Spaccel.ru',
            Yii::$app->urlManager->createAbsoluteUrl(
                [
                    '/',
                ]
            )) . ' .</p>';

}else {
    echo '<p>Добрый день! '.Html::encode($user->getUsername()).', Ваш профиль на сайте заблокирован. </p>';
    echo '<p>Обратитесь по этому вопросу к администратору сайта. Для этого перейдите по ссылке ' .
        Html::a('Spaccel.ru',
            Yii::$app->urlManager->createAbsoluteUrl(
                [
                    '/',
                ]
            )) . ' .</p>';
}


