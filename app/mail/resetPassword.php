<?php

use app\models\User;
use yii\helpers\Html;

/**
 * @var User $user
 */

echo 'Добрый день! '.Html::encode($user->getUsername()).', Вами был отправлен запрос для восстановления пароля на сайте ' . Yii::$app->params['siteName'] . ' <br>';
echo Html::a('Для смены пароля перейдите по этой ссылке.',
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/site/reset-password',
            'key' => $user->secret_key
        ]
    ));
