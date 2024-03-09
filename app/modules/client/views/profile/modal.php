<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

?>

<?php
// Модальное окно сообщений об ошибках при работе с профилем пользователя
Modal::begin([
    'options' => ['class' => 'data_profile_error'],
    'size' => 'modal-md',
    'header' => '<h3>Невозможно сохранить данные формы</h3>',
    'footer' => '<h4>Исправьте данные и повторите попытку</h4>'
]);
?>

<?php Modal::end(); ?>


<?php
// Модальное окно для создания аватарки
Modal::begin([
    'options' => ['class' => 'profile-modal-photo'],
    'size' => 'modal-md',
    'headerOptions' => ['style' => ['border-bottom' => 'none']],
]); ?>

<div class="">
    <?= Html::img('', ['class' => 'profile_photo_i']) ?>
</div>

<div class="">
    <?= Html::button( 'Сохранить',[
        'id' => 'save_avatar_image',
        'class' => 'btn btn-success',
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'justify-content' => 'center',
            'background' => '#52BE7F',
            'width' => '100%',
            'height' => '40px',
            'font-size' => '24px',
            'border-radius' => '8px',
            'margin-top' => '35px',
        ],
    ]) ?>
</div>

<?php Modal::end(); ?>
