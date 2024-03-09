<?php

use app\models\Authors;
use app\models\Projects;
use app\models\User;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var User $user
 * @var Projects $model
 * @var Authors $author
*/
?>


<div class="form-create-project">

    <?php $form = ActiveForm::begin([
        'id' => 'project_create_form',
        'action' => Url::to(['projects/create', 'id' => $user->getId()]),
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'project_name', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-5">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'project_fullname', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'description', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'purpose_project', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Примеры: разработать продукт, найти целевой сегмент, найти рекламный слоган, разработать упаковку и т.д.',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'rid', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'core_rid', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'patent_name', [
            'template' => '<div class="col-md-12  pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'patent_number', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-3">
            <?= '<label class="control-label pl-5">Дата получения патента</label>' ?>
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[patent_date]',
                'value' => empty($model->patent_date) ? null : $model->patent_date,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy'
                ],
                'options' => [
                    'id' => "patent_date",
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ]
            ]) ?>
        </div>
    </div>

    <div class="section_title">Авторы</div>

    <div class="container-authors">
        <div class="item-authors item-authors-form-create panel-body" style="padding: 0;">
            <div class="row row-author row-author-form-create-0" style="margin-bottom: 15px;">

                <?= $form->field($author, "[0]fio", [
                    'template' => '<div class="col-md-12" style="padding-left: 20px; margin-top: 15px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'required' => true,
                    'id' => 'author_fio-0',
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>

                <?= $form->field($author, "[0]role", [
                    'template' => '<div class="col-md-12" style="padding-left: 20px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textInput([
                    'maxlength' => true,
                    'required' => true,
                    'id' => 'author_role-0',
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>

                <?= $form->field($author, "[0]experience", [
                    'template' => '<div class="col-md-12" style="padding-left: 20px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textarea([
                    'rows' => 2,
                    'maxlength' => true,
                    'id' => 'author_experience-0',
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

            </div>
        </div>
    </div>

    <?= Html::button('Добавить автора', [
        'id' => 'add_author_create_form',
        'class' => "btn btn-default add_author_create_form",
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'color' => '#FFFFFF',
            'justify-content' => 'center',
            'background' => '#52BE7F',
            'width' => '200px',
            'height' => '40px',
            'text-align' => 'left',
            'font-size' => '16px',
            'text-transform' => 'uppercase',
            'font-weight' => '700',
            'padding-top' => '9px',
            'border-radius' => '8px',
            'margin-right' => '5px',
        ]
    ]) ?>

    <div class="row desktop-mb-15 mt-30">
        <?= $form->field($model, 'technology', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'layout_technology', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'register_name', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-3">

            <?= '<label class="control-label pl-5">Дата регистрации</label>' ?>
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[register_date]',
                'value' => empty($model->register_date) ? null : $model->register_date,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'id' => "register_date",
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ]
            ]) ?>

        </div>
    </div>

    <div class="row desktop-mb-15 mobile-mt-15">
        <?= $form->field($model, 'site', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <?= $form->field($model, 'invest_name', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>


    <script>
        $( function() {
            var invest_amount_create = 'input#invest_amount_create';
            $(invest_amount_create).change(function () {
                var value = $(invest_amount_create).val();
                var valueMax = 100000000;
                var valueMin = 50000;

                if (parseInt(value) > parseInt(valueMax)){
                    value = valueMax;
                    $(invest_amount_create).val(value);
                }

                if (parseInt(value) < parseInt(valueMin)){
                    value = valueMin;
                    $(invest_amount_create).val(value);
                }
            });
        } );
    </script>


    <div class="row desktop-mb-15">
        <?= $form->field($model, 'invest_amount', [
            'template' => '<div class="col-md-12" style="padding-top: 7px; padding-left: 20px;">{label}<div style="font-weight: 400;font-size: 13px; margin-top: -5px; margin-bottom: 5px;">(укажите значение от 50 000 до 100 млн.)</div></div><div class="col-md-3">{input}</div><div class="col-md-5"></div>'
        ])->textInput([
            'type' => 'number',
            'id' => 'invest_amount_create',
            'class' => 'style_form_field_respond form-control',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-12">
            <?= '<label class="control-label pl-5">Дата получения инвестиций</label>' ?>
        </div>
        <div class="col-md-3">

            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[invest_date]',
                'value' => empty($model->invest_date) ? null : $model->invest_date,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'id' => "invest_date",
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ]
            ]) ?>

        </div>
        <div class="col-md-5"></div>
    </div>

    <div class="row desktop-mb-15 mobile-mt-15">
        <?= $form->field($model, 'announcement_event', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>
    </div>

    <div class="row desktop-mb-15">
        <div class="col-md-12">
            <?= '<label class="control-label pl-5">Дата анонсирования проекта</label>' ?>
        </div>
        <div class="col-md-3">
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'Projects[date_of_announcement]',
                'value' => empty($model->date_of_announcement) ? null : $model->date_of_announcement,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'id' => "date_of_announcement",
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату',
                ]
            ]) ?>
        </div>
        <div class="col-md-5"></div>
    </div>

    <div class="container row mobile-mt-20">
        <div class="pull-left">
            <div class="add_files">
                <div style="margin-top: -5px; padding-left: 5px;">
                    <label>Презентационные файлы</label>
                    <p style="margin-top: -5px; color: #BDBDBD;">
                        (максимум 5 файлов - png, jpg, jpeg, pdf, txt, doc, docx, xls)
                    </p>
                </div>
                <div class="error_files_count text-danger" style="display: none; margin-top: -5px; padding-left: 5px;">
                    Превышено максимальное количество файлов для загрузки.
                </div>
                <div class="pl-5">
                    <?= $form->field($model, 'present_files[]', ['template' => "{label}\n{input}"])->fileInput(['multiple' => true])->label(false) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12" style="display:flex;justify-content: center;">
            <?= Html::submitButton('Сохранить', [
                'id' => 'save_create_form',
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#7F9FC5',
                    'color' => '#ffffff',
                    'width' => '180px',
                    'height' => '40px',
                    'font-size' => '16px',
                    'text-transform' => 'uppercase',
                    'font-weight' => '700',
                    'padding-top' => '9px',
                    'border-radius' => '8px',
                    'margin-top' => '28px'
                ]
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
