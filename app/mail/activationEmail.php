<?php

use app\models\User;
use yii\helpers\Html;

/**
 * @var User $user
 * @var string $unsubscribeLink
 */

echo 'Добрый день, '.Html::encode($user->getUsername()).'.';
echo 'Для подтверждения регистрации на сайте ' . Yii::$app->params['siteName'] .' перейдите по этой ' .
    Html::a('ссылке.', Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/site/activate-account',
            'key' => $user->secret_key
        ]
    ));

?>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>
