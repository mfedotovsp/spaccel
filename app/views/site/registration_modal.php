<?php

use yii\bootstrap\Modal;

?>


<?php // Модальное окно - валидация данных при регистрации
Modal::begin([
    'options' => ['id' => 'error_user_singup'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center" style="color: #F2F2F2; padding: 0 30px;">Измените данные согласно этой информации</h3>',
]);
?>

<?php Modal::end(); ?>


<?php // Модальное окно - результате при регистрации и отправке письма на почту
Modal::begin([
    'options' => ['id' => 'result_singup'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center" style="color: #F2F2F2; padding: 0 30px;">Информация</h3>',
]);
?>

<h4 class="text-center" style="color: #F2F2F2; padding: 0 30px;"></h4>

<?php Modal::end(); ?>
