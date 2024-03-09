<?php

use app\models\ContractorTasks;
use app\models\forms\FormCreateSegment;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\Segments;

/**
 * @var ContractorTasks $task
 * @var ActiveForm $form
 * @var FormCreateSegment $model
 */

?>

<div class="row desktop-mb-15 desktop-mt-25">
    <?= $form->field($model, 'use_wish_list', [
        'template' => '<div class="col-md-12 pl-20">{label}{input}</div>'
    ])->checkbox(['value' => true, 'id' => 'use_wish_list'])
    ?>
</div>

<?php if ($model->getUseWishList() === Segments::NOT_USE_WISH_LIST): ?>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'field_of_activity_b2b', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->label('Сфера деятельности предприятия')->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>

    </div>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'sort_of_activity_b2b', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->label('Вид / специализация деятельности предприятия')->textInput([
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>

    </div>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'company_products', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->label('Продукция / услуги предприятия')->textarea([
            'rows' => 1,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>

    </div>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'company_partner', [
            'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
        ])->label('Партнеры предприятия')->textarea([
            'rows' => 1,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>

    </div>

    <script>

        $( function() {

            $("input#quantity_b2b").change(function () {
                var value = $("input#quantity_b2b").val();
                var valueMax = 10000;
                var valueMin = 1;

                if (parseInt(value) > parseInt(valueMax)){
                    value = valueMax;
                    $("input#quantity_b2b").val(value);
                }

                if (parseInt(value) < parseInt(valueMin)){
                    value = valueMin;
                    $("input#quantity_b2b").val(value);
                }

                // Расчет платежеспособности
                var incomeFromB2B = parseInt($("input#income_from_b2b").val());
                var incomeToB2B = parseInt($("input#income_to_b2b").val());
                var quantityB2B = parseInt($("input#quantity_b2b").val());
                if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                    var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                    $("input#market_volume_b2b").val(Math.round(resB2B));
                }
            });
        } );
    </script>

    <div class="row mb-15">

        <?= $form->field($model, 'quantity_b2b', [
            'template' => '<div class="col-xs-8 pl-20">{label}</div><div class="col-xs-4">{input}</div>'
        ])->label('Потенциальное количество представителей сегмента, ед.')
            ->textInput([
                'type' => 'number',
                'id' => 'quantity_b2b',
                'class' => 'style_form_field_respond form-control',
                'autocomplete' => 'off'
            ])
        ?>

    </div>

    <script>

        $( function() {

            $("input#income_from_b2b").change(function () {
                var value1 = $("input#income_from_b2b").val();
                var value2 = $("input#income_to_b2b").val();
                var valueMax = 10000;
                var valueMin = 1;

                if (parseInt(value1) > parseInt(value2)){
                    value1 = value2;
                    $("input#income_from_b2b").val(value1);
                }

                if (parseInt(value1) > parseInt(valueMax)){
                    value1 = valueMax;
                    $("input#income_from_b2b").val(value1);
                }

                if (parseInt(value1) < parseInt(valueMin)){
                    value1 = valueMin;
                    $("input#income_from_b2b").val(value1);
                }

                // Расчет платежеспособности
                var incomeFromB2B = parseInt($("input#income_from_b2b").val());
                var incomeToB2B = parseInt($("input#income_to_b2b").val());
                var quantityB2B = parseInt($("input#quantity_b2b").val());
                if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                    var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                    $("input#market_volume_b2b").val(Math.round(resB2B));
                }
            });

            $("input#income_to_b2b").change(function () {
                var value1 = $("input#income_from_b2b").val();
                var value2 = $("input#income_to_b2b").val();
                var valueMax = 10000;
                var valueMin = 1;

                if (parseInt(value1) > parseInt(value2)){
                    value2 = value1;
                    $("input#income_to_b2b").val(value2);
                }

                if (parseInt(value2) > parseInt(valueMax)){
                    value2 = valueMax;
                    $("input#income_to_b2b").val(value2);
                }

                if (parseInt(value2) < parseInt(valueMin)){
                    value2 = valueMin;
                    $("input#income_to_b2b").val(value2);
                }

                // Расчет платежеспособности
                var incomeFromB2B = parseInt($("input#income_from_b2b").val());
                var incomeToB2B = parseInt($("input#income_to_b2b").val());
                var quantityB2B = parseInt($("input#quantity_b2b").val());
                if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                    var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                    $("input#market_volume_b2b").val(Math.round(resB2B));
                }
            });

        } );
    </script>

    <div class="row">

        <?= $form->field($model, 'income_company_from', [
            'template' => '<div class="col-xs-12 col-md-4 pl-20">{label}</div>
                    <div class="col-xs-6 col-md-4 mb-15">{input}</div>'
        ])->label('<div>Доход предприятия, млн руб. в год</div><div style="font-weight: 400;font-size: 13px;">(укажите значения от и до)</div>')
            ->textInput([
                'type' => 'number',
                'id' => 'income_from_b2b',
                'class' => 'style_form_field_respond form-control',
                'autocomplete' => 'off'
            ])
        ?>

        <?= $form->field($model, 'income_company_to', [
            'template' => '<div class="col-xs-6 col-md-4 desktop-mt--15">{input}</div>'
        ])->label(false)->textInput([
            'type' => 'number',
            'id' => 'income_to_b2b',
            'class' => 'style_form_field_respond form-control',
            'autocomplete' => 'off'
        ]) ?>

    </div>

    <div class="row desktop-mb-15">

        <?= $form->field($model, 'market_volume_b2b', [
            'template' => '<div class="col-md-4 pl-20">{label}</div>
                    <div class="col-md-8">{input}</div>'
        ])->label('<div>Платежеспособность, млн. руб. в год</div>')
            ->textInput([
                'disabled' => true,
                'type' => 'number',
                'id' => 'market_volume_b2b',
                'class' => 'style_form_field_respond form-control',
                'autocomplete' => 'off'
            ])
        ?>

    </div>

    <div class="row mb-15">

        <?= $form->field($model, 'add_info_b2b', [
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
                'id' => 'submit_segment_b2b',
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

<?php else: ?>

    <div class="row mb-15">
        <div class="col-md-12">
            <?= Html::a('Показать список запросов', ['/contractor/segments/get-list-requirements', 'taskId' => $task->getId()], [
                'id' => 'showListRequirements',
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#7F9FC5',
                    'color' => '#ffffff',
                    'width' => '240px',
                    'height' => '40px',
                    'font-size' => '16px',
                    'text-transform' => 'uppercase',
                    'font-weight' => '700',
                    'padding-top' => '9px',
                    'border-radius' => '8px',
                ]
            ]) ?>
        </div>
    </div>

    <div class="form-create-segment-b2b-with-requirement">

        <?php if ($model->getRequirementId()): ?>

            <?= $form->field($model, 'requirement_id')->hiddenInput()->label(false) ?>

            <?php
            $requirement = $model->findRequirement();
            $wishList = $requirement->wishList;
            ?>

            <div class="row mb-15">
                <div class="col-md-12 pl-20">
                    <div class="bolder">Выбранный запрос</div>
                    <div><?= $requirement->getRequirement() ?></div>
                </div>
                <div class="col-md-12 pl-20">
                    <div class="bolder">Причины запроса</div>
                    <?php foreach ($requirement->reasons as $reason): ?>
                        <div>- <?= $reason->getReason() ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-12 pl-20">
                    <div class="bolder">Ожидаемое решение</div>
                    <div><?= $requirement->getExpectedResult() ?></div>
                </div>

                <div class="col-md-12 pl-20">
                    <?= Html::a('Подробнее о запросе', ['#'], [
                        'class' => 'show-details-select-requirement'
                    ])?>
                </div>

                <div class="col-md-12">
                    <div class="row details-select-requirement">
                        <?php if ($requirement->getAddInfo() !== ''): ?>
                            <div class="col-md-12">
                                <span class="bolder">Дополнительная информация о запросе:</span>
                                <span><?= $requirement->getAddInfo() ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-12">
                            <span class="bolder">Наименование предприятия:</span>
                            <span><?= $wishList->getCompanyName() ?></span>
                        </div>
                        <div class="col-md-12">
                            <span class="bolder">Тип предприятия:</span>
                            <span><?= $wishList->getTypeCompanyName() ?></span>
                        </div>
                        <div class="col-md-12">
                            <span class="bolder">Тип производства:</span>
                            <span><?= $wishList->getTypeProductionName() ?></span>
                        </div>
                        <div class="col-md-12">
                            <span class="bolder">Размер предприятия по количеству персонала:</span>
                            <span><?= $wishList->getSizeName() ?></span>
                        </div>
                        <div class="col-md-12">
                            <span class="bolder">Локация предприятия:</span>
                            <span><?= $wishList->location->getName() ?></span>
                        </div>
                        <?php if ($wishList->getAddInfo() !== ''): ?>
                            <div class="col-md-12">
                                <span class="bolder">Дополнительная информация о предприятии:</span>
                                <span><?= $wishList->getAddInfo() ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="row desktop-mb-15">

                <?= $form->field($model, 'field_of_activity_b2b', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->label('Сфера деятельности предприятия')->textInput([
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>

            </div>

            <div class="row desktop-mb-15">

                <?= $form->field($model, 'sort_of_activity_b2b', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->label('Вид / специализация деятельности предприятия')->textInput([
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                    'autocomplete' => 'off'
                ]) ?>

            </div>

            <div class="row desktop-mb-15">

                <?= $form->field($model, 'company_products', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->label('Продукция / услуги предприятия')->textarea([
                    'rows' => 1,
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

            </div>

            <div class="row desktop-mb-15">

                <?= $form->field($model, 'company_partner', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->label('Партнеры предприятия')->textarea([
                    'rows' => 1,
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

            </div>

            <script>

                $( function() {

                    $("input#quantity_b2b").change(function () {
                        var value = $("input#quantity_b2b").val();
                        var valueMax = 10000;
                        var valueMin = 1;

                        if (parseInt(value) > parseInt(valueMax)){
                            value = valueMax;
                            $("input#quantity_b2b").val(value);
                        }

                        if (parseInt(value) < parseInt(valueMin)){
                            value = valueMin;
                            $("input#quantity_b2b").val(value);
                        }

                        // Расчет платежеспособности
                        var incomeFromB2B = parseInt($("input#income_from_b2b").val());
                        var incomeToB2B = parseInt($("input#income_to_b2b").val());
                        var quantityB2B = parseInt($("input#quantity_b2b").val());
                        if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                            var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                            $("input#market_volume_b2b").val(Math.round(resB2B));
                        }
                    });
                } );
            </script>

            <div class="row mb-15">

                <?= $form->field($model, 'quantity_b2b', [
                    'template' => '<div class="col-xs-8 pl-20">{label}</div><div class="col-xs-4">{input}</div>'
                ])->label('Потенциальное количество представителей сегмента, ед.')
                    ->textInput([
                        'type' => 'number',
                        'id' => 'quantity_b2b',
                        'class' => 'style_form_field_respond form-control',
                        'autocomplete' => 'off'
                    ])
                ?>

            </div>

            <script>

                $( function() {

                    $("input#income_from_b2b").change(function () {
                        var value1 = $("input#income_from_b2b").val();
                        var value2 = $("input#income_to_b2b").val();
                        var valueMax = 10000;
                        var valueMin = 1;

                        if (parseInt(value1) > parseInt(value2)){
                            value1 = value2;
                            $("input#income_from_b2b").val(value1);
                        }

                        if (parseInt(value1) > parseInt(valueMax)){
                            value1 = valueMax;
                            $("input#income_from_b2b").val(value1);
                        }

                        if (parseInt(value1) < parseInt(valueMin)){
                            value1 = valueMin;
                            $("input#income_from_b2b").val(value1);
                        }

                        // Расчет платежеспособности
                        var incomeFromB2B = parseInt($("input#income_from_b2b").val());
                        var incomeToB2B = parseInt($("input#income_to_b2b").val());
                        var quantityB2B = parseInt($("input#quantity_b2b").val());
                        if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                            var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                            $("input#market_volume_b2b").val(Math.round(resB2B));
                        }
                    });

                    $("input#income_to_b2b").change(function () {
                        var value1 = $("input#income_from_b2b").val();
                        var value2 = $("input#income_to_b2b").val();
                        var valueMax = 10000;
                        var valueMin = 1;

                        if (parseInt(value1) > parseInt(value2)){
                            value2 = value1;
                            $("input#income_to_b2b").val(value2);
                        }

                        if (parseInt(value2) > parseInt(valueMax)){
                            value2 = valueMax;
                            $("input#income_to_b2b").val(value2);
                        }

                        if (parseInt(value2) < parseInt(valueMin)){
                            value2 = valueMin;
                            $("input#income_to_b2b").val(value2);
                        }

                        // Расчет платежеспособности
                        var incomeFromB2B = parseInt($("input#income_from_b2b").val());
                        var incomeToB2B = parseInt($("input#income_to_b2b").val());
                        var quantityB2B = parseInt($("input#quantity_b2b").val());
                        if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                            var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                            $("input#market_volume_b2b").val(Math.round(resB2B));
                        }
                    });

                } );
            </script>

            <div class="row">

                <?= $form->field($model, 'income_company_from', [
                    'template' => '<div class="col-xs-12 col-md-4 pl-20">{label}</div>
                    <div class="col-xs-6 col-md-4 mb-15">{input}</div>'
                ])->label('<div>Доход предприятия, млн руб. в год</div><div style="font-weight: 400;font-size: 13px;">(укажите значения от и до)</div>')
                    ->textInput([
                        'type' => 'number',
                        'id' => 'income_from_b2b',
                        'class' => 'style_form_field_respond form-control',
                        'autocomplete' => 'off'
                    ])
                ?>

                <?= $form->field($model, 'income_company_to', [
                    'template' => '<div class="col-xs-6 col-md-4 desktop-mt--15">{input}</div>'
                ])->label(false)->textInput([
                    'type' => 'number',
                    'id' => 'income_to_b2b',
                    'class' => 'style_form_field_respond form-control',
                    'autocomplete' => 'off'
                ]) ?>

            </div>

            <div class="row desktop-mb-15">

                <?= $form->field($model, 'market_volume_b2b', [
                    'template' => '<div class="col-md-4 pl-20">{label}</div>
                    <div class="col-md-8">{input}</div>'
                ])->label('<div>Платежеспособность, млн. руб. в год</div>')
                    ->textInput([
                        'disabled' => true,
                        'type' => 'number',
                        'id' => 'market_volume_b2b',
                        'class' => 'style_form_field_respond form-control',
                        'autocomplete' => 'off'
                    ])
                ?>

            </div>

            <div class="row mb-15">

                <?= $form->field($model, 'add_info_b2b', [
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
                        'id' => 'submit_segment_b2b',
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

        <?php endif; ?>

    </div>

<?php endif; ?>
