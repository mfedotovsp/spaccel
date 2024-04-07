<?php

use app\models\User;

/**
 * @var User $user
 */


echo 'На сайте ' . Yii::$app->params['siteName'] . ' был зарегистрирован новый пользователь: '. $user->getUsername() . '(' . $user->getTextRole() . ')';

