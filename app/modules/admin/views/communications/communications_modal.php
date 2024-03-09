<?php

use yii\bootstrap\Modal;

?>


<?php // Модальное окно "типы деятельности эксперта"
Modal::begin([
    'options' => ['id' => 'expert_types_modal'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center">Выберите типы деятельности эксперта</h3>',
]); ?>
<!--Контент добавляется через Ajax-->
<?php Modal::end(); ?>
