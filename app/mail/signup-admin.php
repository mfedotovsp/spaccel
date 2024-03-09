<?php

use app\models\User;

/**
 * @var User $user
 */


echo 'На сайте Spaccel.ru был зарегистрирован новый пользователь: '. $user->getUsername() . '(' . $user->getTextRole() . ')';

