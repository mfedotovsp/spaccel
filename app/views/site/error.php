<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $message;
?>
<div class="site-error text-center">

    <!--<h1><?/*= Html::encode($this->title) */?></h1>-->

    <div class="alert alert-danger">
        <h3><?= nl2br(Html::encode($message)) ?></h3>
    </div>

    <p>
        Вышеуказанная ошибка произошла, когда веб-сервер обрабатывал ваш запрос.
    </p>
    <p>
        Пожалуйста, свяжитесь с нами, если вы считаете, что это ошибка сервера. Спасибо.
    </p>

</div>
