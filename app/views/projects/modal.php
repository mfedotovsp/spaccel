<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

?>


<?php
// Модальное окно - создание проекта
Modal::begin([
    'options' => ['class' => 'hypothesis_create_modal'],
    'size' => 'modal-lg',
    'header' => '<div>Новый проект</div>',
    'headerOptions' => ['class' => 'header_hypothesis_modal']
]);
?>

<!--контент загружается через Ajax-->
<?php
Modal::end();
?>


<?php
// Модальное окно - редактирование проекта
Modal::begin([
    'options' => ['class' => 'hypothesis_update_modal'],
    'size' => 'modal-lg',
    'header' => '<div>Редактирование проекта</div>',
    'headerOptions' => ['class' => 'header_hypothesis_modal']
]);
?>

<!--контент загружается через Ajax-->
<?php
Modal::end();
?>


<?php
// Модальное окно - Проект с таким именем уже существует
Modal::begin([
    'options' => ['id' => 'project_already_exists',],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center" style="color: #F2F2F2; padding: 0 30px;">Информация</h3>',
]);
?>

<h4 class="text-center" style="color: #F2F2F2; padding: 0 30px;">
    Проект с таким наименованием уже существует. Отредактируйте данное поле и сохраните форму.
</h4>

<?php
Modal::end();
?>


<?php
// Подтверждение закрытия окна редактирования проекта
Modal::begin([
    'options' => [
        'id' => 'confirm_closing_update_modal',
        'class' => 'confirm_closing_modal',
    ],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center header-update-modal">Выберите действие</h3>',
    'footer' => '<div class="text-center">'.

        Html::a('Отмена', ['#'],[
            'class' => 'btn btn-default',
            'style' => ['width' => '120px'],
            'onclick' => "$('#confirm_closing_update_modal').modal('hide'); return false;"
        ]).

        Html::a('Ок', ['#'],[
            'class' => 'btn btn-default',
            'style' => ['width' => '120px'],
            'id' => 'button_confirm_closing_modal',
        ]).

        '</div>'
]); ?>
<h4 class="text-center">Изменения не будут сохранены. Вы действительно хотите закрыть окно?</h4>
<!--Контент добавляется через Ajax-->
<?php Modal::end(); ?>
