<?php

use app\models\ConfirmSegment;
use app\models\forms\FormCreateProblem;
use app\models\RespondsSegment;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use app\models\Problems;

/**
 * @var ConfirmSegment $confirmSegment
 * @var FormCreateProblem $model
 * @var RespondsSegment[] $responds
 */

?>


<style>
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 16px;
        height: 40px;
        padding-left: 15px;
        padding-top: 8px;
        padding-bottom: 15px;
        border-radius: 12px;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 39px;
    }
</style>


<div class="form-create-problem">

    <div class="form-problem">

        <?php if (!$confirmSegment->isExistDesc()): ?>
            <div class="table_responds_to_confirm_segment_desktop">
                <div class="row" style="color: #4F4F4F; margin-top: 20px; margin-bottom: 15px;">

                    <div class="col-md-12">
                        <div class="pull-left" style="padding: 0 10px; border-bottom: 1px solid;">Варианты проблем, полученные от респондентов (представителей сегмента)</div>
                    </div>

                </div>

                <div class="row" style="color: #4F4F4F; padding-left: 10px; margin-bottom: 5px; font-weight: 700;">

                    <div class="col-md-4">
                        Респонденты
                    </div>

                    <div class="col-md-8">
                        Варианты проблем
                    </div>

                </div>


                <!--Список респондентов(представителей сегмента) и их вариантов проблем-->
                <div class="all_responds_problems row container-fluid" style="margin: 0;">

                    <?php foreach ($responds as $respond) : ?>

                        <div class="block_respond_problem row">

                            <div class="col-md-4 block_respond_problem_column">

                                <?php
                                $respond_name = $respond->getName();
                                if (mb_strlen($respond_name) > 30) {
                                    $respond_name = mb_substr($respond_name, 0, 30) . '...';
                                }
                                ?>
                                <?= Html::a('<div title="'.$respond->getName().'">' . $respond_name . '</div>', ['/problems/get-interview-respond', 'id' => $respond->getId()], [
                                    'class' => 'get_interview_respond',
                                ]) ?>

                            </div>

                            <div class="col-md-8 block_respond_problem_column">

                                <?php
                                $interview_result = $respond->interview->getResult();
                                if (mb_strlen($interview_result) > 70) {
                                    $interview_result = mb_substr($interview_result, 0, 70) . '...';
                                }
                                ?>
                                <?= '<div title="'.$respond->interview->getResult().'">' . $interview_result . '</div>' ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>
            </div>

        <?php else: ?>

            <div class="row mt-30 mb-15">
                <div class="col-md-12">
                    <div class="bolder">Проблемы, выявленные на этапе подтв. сегмента</div>
                </div>

                <div class="col-md-12 mt-15">
                    <ul>
                        <?php foreach ($confirmSegment->confirmDescription->problemVariants as $problemVariant): ?>
                            <li><?= $problemVariant->getDescription() ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        <?php endif; ?>

        <div class="generation-problem-form" style="margin-top: 20px;">

            <?php $form = ActiveForm::begin([
                'id' => 'hypothesisCreateForm',
                'action' => Url::to(['/problems/create', 'id' => $confirmSegment->getId()]),
                'options' => ['class' => 'g-py-15 hypothesisCreateForm'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

            <div class="row">

                <div class="col-xs-12">

                    <?php $placeholder = 'Напишите описание гипотезы проблемы сегмента. Примеры: 
- отсутствие путеводителя по комерциализации результатов интеллектуальной деятельности, 
- отсутствие необходимой информации по патентованию...' ?>

                    <?= $form->field($model, 'description', ['template' => '{label}{input}'])->textarea([
                        'rows' => 3,
                        'maxlength' => true,
                        'required' => true,
                        'placeholder' => $placeholder,
                        'class' => 'style_form_field_respond form-control',
                    ]) ?>

                </div>

                <div class="col-xs-12">

                    <?= $form->field($model, 'indicator_positive_passage', [
                        'template' => '{input}',
                    ])->widget(Select2::class, [
                        'data' => Problems::getValuesForSelectIndicatorPositivePassage(),
                        'options' => ['id' => 'indicator_positive_passage'],
                        'disabled' => false,  //Сделать поле неактивным
                        'hideSearch' => true, //Скрытие поиска
                    ]) ?>

                </div>

                <div class="col-xs-12 bolder" style="margin-bottom: 5px;">Вопросы для проверки гипотезы проблемы и ответы на них:</div>

                <div class="container-expectedResults">
                    <div class="row container-fluid item-expectedResults item-expectedResults-form-create">
                        <div class="rowExpectedResults row-expectedResults-form-create-0">

                            <div class="col-xs-6 field-EXR">

                                <?= $form->field($model, "_expectedResultsInterview[0][question]", ['template' => '{input}'])->textarea([
                                    'rows' => 3,
                                    'maxlength' => true,
                                    'required' => true,
                                    'placeholder' => 'Вопрос',
                                    'id' => '_expectedResults_question-0',
                                    'class' => 'style_form_field_respond form-control',
                                ]) ?>

                            </div>

                            <div class="col-xs-6 field-EXR">

                                <?= $form->field($model, "_expectedResultsInterview[0][answer]", ['template' => '{input}'])->textarea([
                                    'rows' => 3,
                                    'maxlength' => true,
                                    'required' => true,
                                    'placeholder' => 'Ответ',
                                    'id' => '_expectedResults_answer-0',
                                    'class' => 'style_form_field_respond form-control',
                                ]) ?>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <?= Html::button('Добавить вопрос', [
                        'id' => 'add_expectedResults_create_form',
                        'class' => "btn btn-success add_expectedResults_create_form",
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'color' => '#FFFFFF',
                            'justify-content' => 'center',
                            'background' => '#52BE7F',
                            'width' => '180px',
                            'height' => '40px',
                            'font-size' => '16px',
                            'border-radius' => '8px',
                            'text-transform' => 'uppercase',
                            'font-weight' => '700',
                            'padding-top' => '9px'
                        ],
                    ]) ?>
                </div>

            </div>

            <div class="form-group row container-fluid" style="display: flex; justify-content: center; margin-top: 20px;">
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

</div>
