<?php

use app\models\forms\FormCreateTaskHypothesis;
use app\models\StageExpertise;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var $formTask FormCreateTaskHypothesis
 * @var $contractorOptions array
 * @var $activityOptions array
 */

?>

<style>
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 20px;
        height: 45px;
        padding-left: 15px;
        padding-top: 7px;
        padding-bottom: 15px;
        border: 1px solid #4F4F4F;
        border-radius: 8px;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
    .select2-container--krajee-bs3 .select2-selection__clear {
        top: 0.9rem;
    }
</style>

<div class="task-form-create-hypothesis">

    <?php $form = ActiveForm::begin([
        'id' => 'taskCreateForm',
        'action' => Url::to(['/tasks/create']),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

        <?= $form->field($formTask, 'projectId')->hiddenInput()->label(false) ?>
        <?= $form->field($formTask, 'type')->hiddenInput()->label(false) ?>
        <?= $form->field($formTask, 'hypothesisId')->hiddenInput()->label(false) ?>

        <div class="row desktop-mb-15">

            <?= $form->field($formTask, 'contractorId', [
                'template' => '<div class="col-md-3 pl-20">{label}</div><div class="col-md-9">{input}</div>',
            ])->label('Исполнитель')
                ->widget(Select2::class, [
                    'data' => $contractorOptions,
                    'options' => ['id' => 'taskCreateForm_contractorId'],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ])
            ?>

        </div>

        <div class="row desktop-mb-15">

            <?= $form->field($formTask, 'activityId', [
                'template' => '<div class="col-md-3 pl-20">{label}</div><div class="col-md-9">{input}</div>'
            ])->label('Вид деятельности')
                ->widget(Select2::class, [
                    'data' => $activityOptions,
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ])
            ?>

        </div>

        <?php if (in_array($formTask->getType(), [StageExpertise::CONFIRM_PROBLEM, StageExpertise::CONFIRM_GCP, StageExpertise::CONFIRM_MVP], true)): ?>

            <div class="row desktop-mb-15">

                <?= $form->field($formTask, 'useRespond', [
                    'template' => '<div class="col-md-3 pl-20">{label}</div><div class="col-md-9">{input}</div>'
                ])->label('Добавить респондентов')
                    ->widget(Select2::class, [
                        'data' => [
                            true => 'Да - добавить респондентов исполнителя, подтвердивших предыдущий этап',
                            false => 'Нет - исполнитель будет добавлять новых респондентов'
                        ],
                        'disabled' => false,  //Сделать поле неактивным
                        'hideSearch' => true, //Скрытие поиска
                    ])
                ?>

            </div>

        <?php endif; ?>

        <div class="row desktop-mb-15">

            <?= $form->field($formTask, 'description', [
                'template' => '<div class="col-md-3 pl-20">{label}</div><div class="col-md-9">{input}</div>'
            ])->textarea([
                'rows' => 1,
                'class' => 'style_form_field_respond form-control',
            ])->label('Описание задания') ?>

        </div>

        <div class="form-group row">
            <div class="col-md-12" style="display:flex;justify-content: center;">
                <?= Html::submitButton('Сохранить', [
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
