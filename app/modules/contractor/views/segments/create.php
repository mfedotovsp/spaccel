<?php

use app\models\ContractorTasks;
use app\models\forms\FormCreateSegment;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use app\models\Segments;

/**
 * @var ContractorTasks $task
 * @var FormCreateSegment $model
 */

?>

<style>
    .select2-container--krajee .select2-selection {
        font-size: 20px;
        height: 45px;
        padding-left: 15px;
        padding-top: 7px;
        padding-bottom: 15px;
        border: 1px solid #4F4F4F;
        border-radius: 8px;
    }
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
    .select2-container--krajee .select2-selection__clear {
        top: 0.9rem;
    }
</style>


<div class="segment-form-create">

    <?php $form = ActiveForm::begin([
        'id' => 'hypothesisCreateForm',
        'action' => Url::to(['/contractor/segments/create', 'id' => $task->getId()]),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'name', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-5">{input}</div>'
        ])->label('Наименование сегмента')->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>

    </div>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'description', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->label('Краткое описание сегмента')->textarea([
            'rows' => 1,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>

    </div>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'type_of_interaction_between_subjects', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12 type_of_interaction">{input}</div>'
        ])->label('Тип взаимодействия с потребителями')->widget(Select2::class, [
            'data' => [Segments::TYPE_B2C => 'B2C', Segments::TYPE_B2B => 'B2B'],
            'options' => ['id' => 'type-interaction'],
            'hideSearch' => true, //Скрытие поиска
        ]) ?>

    </div>


    <div class="form-template-b2c">

        <div class="row desktop-mb-15">

            <?= $form->field($model, 'field_of_activity_b2c', [
                'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
            ])->label('Сфера деятельности потребителя')->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>

        </div>

        <div class="row mb-15">

            <?= $form->field($model, 'sort_of_activity_b2c', [
                'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
            ])->label('Вид / специализация деятельности потребителя')->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>

        </div>

        <script>

            $( function() {

                $("input#age_from").change(function () {
                    var value1 = $("input#age_from").val();
                    var value2 = $("input#age_to").val();
                    var valueMax = 100;
                    var valueMin = 0;

                    if (parseInt(value1) > parseInt(value2)){
                        value1 = value2;
                        $("input#age_from").val(value1);
                    }

                    if (parseInt(value1) > parseInt(valueMax)){
                        value1 = valueMax;
                        $("input#age_from").val(value1);
                    }

                    if (parseInt(value1) < parseInt(valueMin)){
                        value1 = valueMin;
                        $("input#age_from").val(value1);
                    }
                });

                $("input#age_to").change(function () {
                    var value1 = $("input#age_from").val();
                    var value2 = $("input#age_to").val();
                    var valueMax = 100;
                    var valueMin = 0;

                    if (parseInt(value1) > parseInt(value2)){
                        value2 = value1;
                        $("input#age_to").val(value2);
                    }

                    if (parseInt(value2) > parseInt(valueMax)){
                        value2 = valueMax;
                        $("input#age_to").val(value2);
                    }

                    if (parseInt(value2) < parseInt(valueMin)){
                        value2 = valueMin;
                        $("input#age_to").val(value2);
                    }
                });

            } );
        </script>

        <div class="row mb-15">

            <?= $form->field($model, 'age_from', [
                'template' => '<div class="col-xs-6 col-md-4 pl-20" style="">{label}</div>
                <div class="col-xs-3 col-md-4">{input}</div>'
            ])->label('<div>Возраст потребителя</div><div style="font-weight: 400;font-size: 13px;">(укажите значения от и до)</div>')
                ->textInput([
                    'type' => 'number',
                    'id' => 'age_from',
                    'class' => 'style_form_field_respond form-control',
                    'autocomplete' => 'off'
                ])
            ?>

            <?= $form->field($model, 'age_to', [
                'template' => '<div class="col-xs-3 col-md-4" style="margin-top: -15px;">{input}</div>'
            ])->label(false)->textInput([
                'type' => 'number',
                'id' => 'age_to',
                'class' => 'style_form_field_respond form-control',
                'autocomplete' => 'off'
            ]) ?>

        </div>

        <div class="row desktop-mb-15">

            <?php
            $list_gender = [
                Segments::GENDER_ANY => 'Не важно',
                Segments::GENDER_MAN => 'Мужской',
                Segments::GENDER_WOMAN => 'Женский',
            ];
            ?>

            <?= $form->field($model, 'gender_consumer', [
                'template' => '<div class="col-md-4 pl-20">{label}</div><div class="col-md-8">{input}</div>'
            ])->label('Пол потребителя')
                ->widget(Select2::class, [
                    'data' => $list_gender,
                    'pluginOptions' => ['allowClear' => true],
                    'options' => ['placeholder' => 'Выберите пол потребителя'],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ])
            ?>

        </div>

        <div class="row mb-15">

            <?php
            $list_education = [
                Segments::SECONDARY_EDUCATION => 'Среднее образование',
                Segments::SECONDARY_SPECIAL_EDUCATION => 'Среднее образование (специальное)',
                Segments::HIGHER_INCOMPLETE_EDUCATION => 'Высшее образование (незаконченное)',
                Segments::HIGHER_EDUCATION => 'Высшее образование'
            ];
            ?>

            <?= $form->field($model, 'education_of_consumer', [
                'template' => '<div class="col-md-4 pl-20">{label}</div><div class="col-md-8">{input}</div>'
            ])->label('Образование потребителя')
                ->widget(Select2::class, [
                    'data' => $list_education,
                    'pluginOptions' => ['allowClear' => true],
                    'options' => ['placeholder' => 'Выберите уровень образования потребителя'],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ])
            ?>

        </div>

        <script>

            $( function() {

                $("input#income_from").change(function () {
                    var value1 = $("input#income_from").val();
                    var value2 = $("input#income_to").val();
                    var valueMax = 1000000;
                    var valueMin = 0;

                    if (parseInt(value1) > parseInt(value2)){
                        value1 = value2;
                        $("input#income_from").val(value1);
                    }

                    if (parseInt(value1) > parseInt(valueMax)){
                        value1 = valueMax;
                        $("input#income_from").val(value1);
                    }

                    if (parseInt(value1) < parseInt(valueMin)){
                        value1 = valueMin;
                        $("input#income_from").val(value1);
                    }

                    // Расчет платежеспособности
                    var incomeFrom = parseInt($("input#income_from").val());
                    var incomeTo = parseInt($("input#income_to").val());
                    var quantity = parseInt($("input#quantity").val());
                    if (incomeFrom > 0 && incomeTo > 0 && quantity > 0) {
                        var res = ((incomeFrom + incomeTo) * 6) * quantity / 1000000;
                        $("input#market_volume_b2c").val(Math.round(res));
                    }
                });

                $("input#income_to").change(function () {
                    var value1 = $("input#income_from").val();
                    var value2 = $("input#income_to").val();
                    var valueMax = 1000000;
                    var valueMin = 0;

                    if (parseInt(value1) > parseInt(value2)){
                        value2 = value1;
                        $("input#income_to").val(value2);
                    }

                    if (parseInt(value2) > parseInt(valueMax)){
                        value2 = valueMax;
                        $("input#income_to").val(value2);
                    }

                    if (parseInt(value2) < parseInt(valueMin)){
                        value2 = valueMin;
                        $("input#income_to").val(value2);
                    }

                    // Расчет платежеспособности
                    var incomeFrom = parseInt($("input#income_from").val());
                    var incomeTo = parseInt($("input#income_to").val());
                    var quantity = parseInt($("input#quantity").val());
                    if (incomeFrom > 0 && incomeTo > 0 && quantity > 0) {
                        var res = ((incomeFrom + incomeTo) * 6) * quantity / 1000000;
                        $("input#market_volume_b2c").val(Math.round(res));
                    }
                });

            } );
        </script>

        <div class="row">

            <?= $form->field($model, 'income_from', [
                'template' => '<div class="col-xs-12 col-md-4 pl-20">{label}</div>
                <div class="col-xs-6 col-md-4" style="margin-bottom: 30px;">{input}</div>'
            ])->label('<div>Доход потребителя, руб./мес.</div><div style="font-weight: 400;font-size: 13px;">(укажите значения от и до)</div>')
                ->textInput([
                    'type' => 'number',
                    'id' => 'income_from',
                    'class' => 'style_form_field_respond form-control',
                    'autocomplete' => 'off'
                ])
            ?>

            <?= $form->field($model, 'income_to', [
                'template' => '<div class="col-xs-6 col-md-4 desktop-mt--15">{input}</div>'
            ])->label(false)->textInput([
                'type' => 'number',
                'id' => 'income_to',
                'class' => 'style_form_field_respond form-control',
                'autocomplete' => 'off'
            ]) ?>

        </div>

        <script>

            $( function() {

                $("input#quantity").change(function () {
                    var value = $("input#quantity").val();
                    var valueMax = 1000000;
                    var valueMin = 1;

                    if (parseInt(value) > parseInt(valueMax)){
                        value = valueMax;
                        $("input#quantity").val(value);
                    }

                    if (parseInt(value) < parseInt(valueMin)){
                        value = valueMin;
                        $("input#quantity").val(value);
                    }

                    // Расчет платежеспособности
                    var incomeFrom = parseInt($("input#income_from").val());
                    var incomeTo = parseInt($("input#income_to").val());
                    var quantity = parseInt($("input#quantity").val());
                    if (incomeFrom > 0 && incomeTo > 0 && quantity > 0) {
                        var res = ((incomeFrom + incomeTo) * 6) * quantity / 1000000;
                        $("input#market_volume_b2c").val(Math.round(res));
                    }
                });
            } );
        </script>

        <div class="row desktop-mb-15">

            <?= $form->field($model, 'quantity', [
                'template' => '<div class="col-md-4 pl-20">{label}</div>
                <div class="col-md-8">{input}</div>'
            ])->label('Потенциальное количество потребителей, чел.')
                ->textInput([
                    'type' => 'number',
                    'id' => 'quantity',
                    'class' => 'style_form_field_respond form-control',
                    'autocomplete' => 'off'
                ])
            ?>

        </div>

        <div class="row desktop-mb-15">

            <?= $form->field($model, 'market_volume_b2c', [
                'template' => '<div class="col-md-4 pl-20">{label}</div>
                <div class="col-md-8">{input}</div>'
            ])->label('Платежеспособность, млн руб. в год')
                ->textInput([
                    'disabled' => true,
                    'type' => 'number',
                    'id' => 'market_volume_b2c',
                    'class' => 'style_form_field_respond form-control',
                    'autocomplete' => 'off'
                ])
            ?>

        </div>

        <div class="row mb-15">

            <?= $form->field($model, 'add_info_b2c', [
                'template' => '<div class="col-xs-12 pl-20">{label}</div><div class="col-xs-12">{input}</div>'
            ])->textarea([
                'rows' => 1,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>

        </div>

        <div class="form-group row">
            <div class="col-md-12" style="display:flex;justify-content: center;">
                <?= Html::submitButton('Сохранить', [
                    'id' => 'submit_segment_b2c',
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

    </div>

    <div class="form-template-b2b" style="display: none;">

        <?= $this->render('create_b2b_ajax', [
            'form' => $form,
            'model' => $model,
            'task' => $task,
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

    <div class="row mb-15 list-requirements"></div>

</div>
