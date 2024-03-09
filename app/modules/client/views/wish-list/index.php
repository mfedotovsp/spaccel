<?php

use app\models\Client;
use app\models\forms\FormFilterRequirement;
use app\models\LocationWishList;
use app\models\SizesWishList;
use app\models\TypesCompanyWishList;
use app\models\TypesProductionWishList;
use app\models\WishList;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Списки запросов B2B компаний';
$this->registerCssFile('@web/css/wish-list-style.css');

/**
 * @var WishList[] $models
 * @var Pagination $pages
 * @var integer $clientId
 * @var FormFilterRequirement $filters
 * @var Client[] $listClient
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
        <div class="col-md-2 pull-right">
            <?=  Html::a( 'Новые списки', ['/client/wish-list/new'], [
                    'class' => 'btn btn-success',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'color' => '#FFFFFF',
                        'justify-content' => 'center',
                        'background' => '#52BE7F',
                        'width' => '180px',
                        'height' => '40px',
                        'font-size' => '24px',
                        'border-radius' => '8px',
                        'margin-top' => '35px',
                    ]
                ]
            ) ?>
        </div>
        <div class="col-md-3 " style="margin-top: 30px;">
            <?=  Html::a( '<div class="new_client_request_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый список</div></div>',
                ['/client/wish-list/create'], ['class' => 'new_client_request_link_plus pull-right']
            ) ?>
        </div>
    </div>
</div>

<div class="container-fluid">

    <div class="container-filters-requirement mt-15">

        <?php $form = ActiveForm::begin([
            'id' => 'adminFiltersRequirement',
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <?php if ($_POST['FormFilterRequirement']): ?>
            <div class="row container-fluid addFiltersForListRequirements disabled mb-5">
            <?php else: ?>
            <div class="row container-fluid addFiltersForListRequirements mb-5">
        <?php endif; ?>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
                <?= Html::button('Добавить фильтры', [
                    'id' => 'addFiltersForListRequirementsAdmin',
                    'class' => 'btn btn-success',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'color' => '#FFFFFF',
                        'background' => '#52BE7F',
                        'width' => '100%',
                        'height' => '40px',
                        'font-size' => '16px',
                        'text-transform' => 'uppercase',
                        'font-weight' => '700',
                        'padding-top' => '9px',
                        'border-radius' => '8px',
                        'margin-bottom' => '5px',
                    ]
                ]) ?>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-9 col-lg-10"></div>
            </div>

        <?php if ($_POST['FormFilterRequirement']): ?>
            <div class="row container-fluid buttonsFiltersForListRequirements active mb-5">
            <?php else: ?>
            <div class="row container-fluid buttonsFiltersForListRequirements mb-5">
        <?php endif; ?>
            <div class="col-sm-6 col-md-3 col-lg-2">
                <?= Html::a('Сбросить фильтры', Url::to(['/client/wish-list/index']), [
                    'class' => 'btn btn-danger',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'color' => '#FFFFFF',
                        'background' => '#FF5C5C',
                        'width' => '100%',
                        'height' => '40px',
                        'font-size' => '16px',
                        'text-transform' => 'uppercase',
                        'font-weight' => '700',
                        'padding-top' => '9px',
                        'border-radius' => '8px',
                        'margin-bottom' => '5px',
                    ]
                ]) ?>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-2">
                <?= Html::submitButton('Применить фильтры', [
                    'class' => 'btn btn-success',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'color' => '#FFFFFF',
                        'background' => '#52BE7F',
                        'width' => '100%',
                        'height' => '40px',
                        'font-size' => '16px',
                        'text-transform' => 'uppercase',
                        'font-weight' => '700',
                        'padding-top' => '9px',
                        'border-radius' => '8px',
                        'margin-bottom' => '5px',
                    ]
                ]) ?>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-8"></div>
            </div>

        <?php if ($_POST['FormFilterRequirement']): ?>
            <div class="row container-fluid requirement-filters active mb-5">
            <?php else: ?>
            <div class="row container-fluid requirement-filters mb-5">
        <?php endif; ?>

            <div class="col-md-12">
                <?= $form->field($filters, 'requirement', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'reason', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'expectedResult', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'fieldOfActivity', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'sortOfActivity', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'size', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
                ])->widget(Select2::class, [
                    'data' => SizesWishList::getList(),
                    'options' => ['placeholder' => ''],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'locationId', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
                ])->widget(Select2::class, [
                    'data' => LocationWishList::getList(),
                    'options' => ['placeholder' => ''],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => false, //Скрытие поиска
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'typeCompany', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
                ])->widget(Select2::class, [
                    'data' => TypesCompanyWishList::getList(),
                    'options' => ['placeholder' => ''],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'typeProduction', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
                ])->widget(Select2::class, [
                    'data' => TypesProductionWishList::getList(),
                    'options' => ['placeholder' => ''],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($filters, 'clientId', [
                    'template' => '<div class="pl-5">{label}</div><div>{input}</div>',
                ])->widget(Select2::class, [
                    'data' => ArrayHelper::map($listClient, 'id', 'name'),
                    'options' => ['placeholder' => ''],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= '<label class="control-label pl-5">Начало периода</label>' ?>
                <?= DatePicker::widget([
                    'type' => 2,
                    'removeButton' => false,
                    'name' => 'FormFilterRequirement[startDate]',
                    'readonly' => true,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd.mm.yyyy',
                    ],
                    'options' => [
                        'id' => "FormFilterRequirement_startDate",
                        'class' => 'text-center style_form_field_respond form-control',
                        'style' => ['padding-right' => '20px'],
                        'placeholder' => 'Выберите дату',
                    ]
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= '<label class="control-label pl-5">Конец периода</label>' ?>
                <?= DatePicker::widget([
                    'type' => 2,
                    'removeButton' => false,
                    'name' => 'FormFilterRequirement[endDate]',
                    'readonly' => true,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd.mm.yyyy',
                    ],
                    'options' => [
                        'id' => "FormFilterRequirement_endDate",
                        'class' => 'text-center style_form_field_respond form-control',
                        'style' => ['padding-right' => '20px'],
                        'placeholder' => 'Выберите дату',
                    ]
                ]) ?>
            </div>

        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="row headers_wish_lists_new">

        <div class="col-md-4">
            Наименование предприятия
        </div>

        <div class="col-md-3">
            Тип предприятия
        </div>

        <div class="col-md-3">
            Тип производства
        </div>

        <div class="col-md-2">
            Акселератор
        </div>

    </div>

    <div class="block_all_wish_lists_new">

        <?= $this->render('index_ajax', [
            'models' => $models,
            'pages' => $pages,
            'clientId' => $clientId
        ]) ?>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/wish_list_index.js'); ?>