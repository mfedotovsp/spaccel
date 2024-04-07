<?php

use app\models\User;
use yii\helpers\Html;

/**
 * @var User $user
 */

echo '<p>Добрый день! '.Html::encode($user->getUsername()).', Вы зарегистрировались на сайте ' . Yii::$app->params['siteName'] . ' </p>';
echo '<p>Ожидайте активации Вашего профиля. Мы известим Вас об этом в новом письме.</p>';
