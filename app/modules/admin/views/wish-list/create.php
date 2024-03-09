<?php

use app\models\LocationWishList;
use app\models\SizesWishList;
use app\models\TypesCompanyWishList;
use app\models\TypesProductionWishList;
use app\modules\admin\models\form\FormCreateWishList;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Новый список запросов B2B компании';
$this->registerCssFile('@web/css/wish-list-style.css');

/**
 * @var FormCreateWishList $model
 */

?>

<div class="container-fluid">
    <div class="row hi-line-page">
        <div class="col-md-7" style="margin-top: 35px; padding-left: 25px;">
            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>
        <div class="col-md-5"></div>
    </div>
</div>

<div class="container-fluid">

    <div class="row" style="padding: 5px;">

        <?php $form = ActiveForm::begin([
            'id' => 'wishListCreateForm',
            'action' => Url::to(['/admin/wish-list/create']),
            'options' => ['class' => 'g-py-15 wishListCreateForm'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <div class="col-md-12">
            <?= $form->field($model, 'company_name', [
                'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
            ])->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'company_field_of_activity', [
                'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
            ])->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'company_sort_of_activity', [
                'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
            ])->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="col-md-12">

            <?= $form->field($model, "company_products", ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'
            ])->textarea([
                'rows' => 1,
                'maxlength' => true,
                'required' => true,
                'placeholder' => '',
                'id' => 'wishListCreateForm_add_info',
                'class' => 'style_form_field_respond form-control',
            ]) ?>

        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'size', [
                'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => SizesWishList::getList(),
                'options' => ['id' => 'wishListCreateForm_size', 'placeholder' => 'Выберите нужное значение'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'location_id', [
                'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => LocationWishList::getList(),
                'options' => ['id' => 'wishListCreateForm_location_id', 'placeholder' => 'Выберите нужное значение'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => false, //Скрытие поиска
            ]) ?>

        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'type_company', [
                'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => TypesCompanyWishList::getList(),
                'options' => ['id' => 'wishListCreateForm_type_company', 'placeholder' => 'Выберите нужное значение'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'type_production', [
                'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => TypesProductionWishList::getList(),
                'options' => ['id' => 'wishListCreateForm_type_production', 'placeholder' => 'Выберите нужное значение'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        </div>

        <div class="col-md-12">

            <?= $form->field($model, "add_info", ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'
            ])->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'placeholder' => '',
                'id' => 'wishListCreateForm_add_info',
                'class' => 'style_form_field_respond form-control',
            ]) ?>

        </div>

        <div class="form-group col-md-12" style="display: flex; justify-content: center; margin-top: 20px;">
            <?= Html::submitButton('Сохранить', [
                'class' => 'btn btn-default pull-right',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'margin-bottom' => '15px',
                    'background' => '#7F9FC5',
                    'width' => '180px',
                    'height' => '40px',
                    'border-radius' => '8px',
                    'text-transform' => 'uppercase',
                    'font-size' => '16px',
                    'color' => '#FFFFFF',
                    'font-weight' => '700',
                    'padding-top' => '9px'
                ]
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/wish_list_index.js'); ?>