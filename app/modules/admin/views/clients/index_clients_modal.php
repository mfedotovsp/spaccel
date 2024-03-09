<?php

use yii\bootstrap\Modal;

?>


<?php // Модальное окно добавления менеджера
Modal::begin([
    'options' => ['id' => 'change_manager_modal'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center">Назначение менеджера</h3>',
]); ?>
<!--Контент добавляется через Ajax-->
<?php Modal::end(); ?>


<?php // Модальное окно добавления тарифного плана
Modal::begin([
    'options' => ['id' => 'change_rates_plan_modal'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center">Назначение тарифного плана</h3>',
]); ?>
<!--Контент добавляется через Ajax-->
<?php Modal::end(); ?>
