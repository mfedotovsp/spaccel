<?php

use app\models\User;
use yii\helpers\Html;

/**
 * @var User $user
 */

echo 'Добрый день, '.Html::encode($user->getUsername()).'.';
echo 'Для подтверждения регистрации на сайте ' . Yii::$app->params['siteName'] .' перейдите по этой ' .
    Html::a('ссылке.', Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/site/activate-account',
            'key' => $user->secret_key
        ]
    ));
