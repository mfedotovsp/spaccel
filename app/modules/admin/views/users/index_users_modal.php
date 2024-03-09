<?php

use yii\bootstrap\Modal;

?>


<?php // Модальное окно добавления трекера
Modal::begin([
    'options' => ['id' => 'add_admin_modal'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center">Назначение трекера</h3>',
]); ?>
<!--Контент добавляется через Ajax-->
<?php Modal::end(); ?>


<?php // Модальное окно изменение статуса пользователя
Modal::begin([
    'options' => ['id' => 'change_status_modal'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center">Изменение статуса</h3>',
]); ?>
<!--Контент добавляется через Ajax-->
<?php Modal::end(); ?>