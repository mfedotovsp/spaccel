<?php

use app\models\ExpectedResultsInterviewConfirmProblem;
use app\models\forms\FormUpdateProblem;
use app\models\RespondsSegment;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use app\models\Problems;

/**
 * @var Problems $model
 * @var RespondsSegment[] $responds
 * @var FormUpdateProblem $formUpdate
 * @var ExpectedResultsInterviewConfirmProblem $expectedResult
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


<div class="form-update-problem">

    <div class="form-problem">

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
                            $descInterview_result = $respond->interview->result;
                            if (mb_strlen($descInterview_result) > 70) {
                                $descInterview_result = mb_substr($descInterview_result, 0, 70) . '...';
                            }
                            ?>
                            <?= '<div title="'.$respond->interview->result.'">' . $descInterview_result . '</div>' ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>

        <div class="generation-problem-form" style="margin-top: 20px;">

            <?php $form = ActiveForm::begin([
                'id' => 'hypothesisUpdateForm',
                'action' => Url::to(['/contractor/problems/update', 'id' => $formUpdate->getId()]),
                'options' => ['class' => 'g-py-15 hypothesisUpdateForm'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

            <div class="row">

                <div class="col-xs-12">

                    <?php $placeholder = 'Напишите описание гипотезы проблемы сегмента. Примеры: 
- отсутствие путеводителя по комерциализации результатов интеллектуальной деятельности, 
- отсутствие необходимой информации по патентованию...' ?>

                    <?= $form->field($formUpdate, 'description', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textarea([
                        'rows' => 3,
                        'maxlength' => true,
                        'required' => true,
                        'placeholder' => $placeholder,
                        'class' => 'style_form_field_respond form-control',
                    ]) ?>

                </div>

                <div class="col-xs-12">

                    <?= $form->field($formUpdate, 'indicator_positive_passage', [
                        'template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>',
                    ])->widget(Select2::class, [
                        'data' => Problems::getValuesForSelectIndicatorPositivePassage(),
                        'options' => ['id' => 'indicator_positive_passage-'.$formUpdate->getId()],
                        'disabled' => false,  //Сделать поле неактивным
                        'hideSearch' => true, //Скрытие поиска
                    ]) ?>

                </div>

                <h4 class="col-xs-12 text-center bolder" style="margin-bottom: 20px;">Вопросы для проверки гипотезы проблемы и ответы на них:</h4>

                <div class="container-expectedResults">
                    <div class="row container-fluid item-expectedResults item-expectedResults-<?= $formUpdate->getId() ?>">

                        <?php if ($formUpdate->getExpectedResultsInterview()) : ?>

                            <?php foreach ($formUpdate->getExpectedResultsInterview() as $i => $expectedResult): ?>

                                <div class="rowExpectedResults row-expectedResults-<?= $formUpdate->getId() . '_' . $i ?>">

                                    <div class="col-xs-6 field-EXR">

                                        <?= $form->field($formUpdate, "_expectedResultsInterview[$i][question]", ['template' => '{input}'])->textarea([
                                            'rows' => 3,
                                            'maxlength' => true,
                                            'required' => true,
                                            'placeholder' => 'Вопрос',
                                            'id' => '_expectedResults_question-' . $i,
                                            'class' => 'style_form_field_respond form-control',
                                        ]) ?>

                                    </div>

                                    <div class="col-xs-6 field-EXR">

                                        <?= $form->field($formUpdate, "_expectedResultsInterview[$i][answer]", ['template' => '{input}'])->textarea([
                                            'rows' => 3,
                                            'maxlength' => true,
                                            'required' => true,
                                            'placeholder' => 'Ответ',
                                            'id' => '_expectedResults_answer-' . $i,
                                            'class' => 'style_form_field_respond form-control',
                                        ]) ?>

                                    </div>

                                    <?php if ($i !== 0) : ?>

                                        <div class="col-xs-12" style="margin-bottom: 15px;">

                                            <?= Html::button('Удалить вопрос', [
                                                'id' => 'remove-expectedResults-' . $formUpdate->getId() . '_' . $i . '-' . $expectedResult->getId(),
                                                'class' => "remove-expectedResults btn btn-default",
                                                'style' => [
                                                    'display' => 'flex',
                                                    'align-items' => 'center',
                                                    'justify-content' => 'center',
                                                    'width' => '180px',
                                                    'height' => '40px',
                                                    'font-size' => '16px',
                                                    'border-radius' => '8px',
                                                    'text-transform' => 'uppercase',
                                                    'font-weight' => '700',
                                                    'padding-top' => '9px'
                                                ]
                                            ]) ?>
                                        </div>

                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        <?php else : ?>

                            <?php $i = 0; ?>

                            <div class="rowExpectedResults row-expectedResults-<?= $formUpdate->getId() . '_' . $i ?>">

                                <div class="col-xs-6 field-EXR">

                                    <?= $form->field($formUpdate, "_expectedResultsInterview[$i][question]", ['template' => '{input}'])->textarea([
                                        'rows' => 3,
                                        'maxlength' => true,
                                        'required' => true,
                                        'placeholder' => 'Вопрос',
                                        'id' => '_expectedResults_question-' . $i,
                                        'class' => 'style_form_field_respond form-control',
                                    ]) ?>

                                </div>

                                <div class="col-xs-6 field-EXR">

                                    <?= $form->field($formUpdate, "_expectedResultsInterview[$i][answer]", ['template' => '{input}'])->textarea([
                                        'rows' => 3,
                                        'maxlength' => true,
                                        'required' => true,
                                        'placeholder' => 'Ответ',
                                        'id' => '_expectedResults_answer-' . $i,
                                        'class' => 'style_form_field_respond form-control',
                                    ]) ?>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>

                <div class="col-xs-12">
                    <?= Html::button('Добавить вопрос', [
                        'id' => 'add_expectedResults-' . $formUpdate->getId(),
                        'class' => "btn btn-success add_expectedResults",
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
                        ]
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
