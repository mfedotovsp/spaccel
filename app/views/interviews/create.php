<?php

use app\models\InterviewConfirmGcp;
use app\models\InterviewConfirmMvp;
use app\models\InterviewConfirmProblem;
use app\models\InterviewConfirmSegment;
use app\models\RespondsGcp;
use app\models\RespondsMvp;
use app\models\RespondsProblem;
use app\models\RespondsSegment;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use app\models\StageConfirm;
use yii\helpers\Url;
use app\models\QuestionStatus;

/**
 * @var RespondsSegment|RespondsProblem|RespondsGcp|RespondsMvp $respond
 * @var InterviewConfirmSegment|InterviewConfirmProblem|InterviewConfirmGcp|InterviewConfirmMvp $model
 */

?>


<?php $form = ActiveForm::begin([
    'id' => 'formCreateDescInterview',
    'action' => Url::to(['/interviews/create', 'stage' => $respond->confirm->getStage(), 'id' => $respond->getId()]),
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]); ?>

<?php if ($respond->answers) : ?>
    <?php foreach ($respond->answers as $index => $answer) : ?>

        <?php if ($answer->question->getStatus() === QuestionStatus::STATUS_ONE_STAR) : ?>

            <?= $form->field($answer, "[$index]answer", ['template' => '<div style="padding-left: 5px; color: #52be7f;">{label}</div><div>{input}</div>'])->label($answer->question->getTitle())
            ->textarea([
                'row' => 2,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
            ]) ?>

        <?php elseif($answer->question->getStatus() === QuestionStatus::STATUS_NOT_STAR) : ?>

            <?= $form->field($answer, "[$index]answer", ['template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>'])->label($answer->question->getTitle())
                ->textarea([
                    'row' => 2,
                    'maxlength' => true,
                    'required' => true,
                    'class' => 'style_form_field_respond form-control',
                ]) ?>

        <?php endif; ?>

        <?= $form->field($answer, "[$index]question_id")->label(false)->hiddenInput() ?>

    <?php endforeach; ?>
<?php endif; ?>

<div class="row" style="margin-bottom: 15px;">

    <div class="col-md-12">

        <p style="padding-left: 5px;"><b>Приложить файл</b> <span style="color: #BDBDBD; padding-left: 20px;">png, jpg, jpeg, pdf, txt, doc, docx, xls</span></p>

        <div style="display:flex; margin-top: -5px;">

            <?= $form->field($model, 'loadFile')
                ->fileInput([
                    'id' => 'descInterviewCreateFile', 'class' => 'sr-only'
                ])->label('Выберите файл',[
                    'class'=>'btn btn-success',
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

            <div class='title_file' style="padding-left: 20px; padding-top: 5px;">Файл не выбран</div>

        </div>

    </div>

    <?php if ($respond->confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) : ?>

        <div class="col-md-12" style="margin-top: -10px;">

            <?= $form->field($model, 'result',['template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 2,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Опишите краткий вывод по интервью',
            ]) ?>

        </div>

        <div class="col-xs-12">

            <?php
            $selection_list = ['1' => 'Респондент является представителем сегмента', '0' => 'Респондент не является представителем сегмента'];
            ?>

            <?= $form->field($model, 'status', [
                'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
            ])->label('Этот респондент является представителем сегмента?')->widget(Select2::class, [
                'data' => $selection_list,
                'options' => ['id' => 'descInterview_status'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        </div>

    <?php elseif ($respond->confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) : ?>

        <div class="col-md-12">

            <?php
            $selection_list = ['1' => 'Проблема значимая', '0' => 'Проблемы не существует или она малозначимая'];
            ?>

            <?= $form->field($model, 'status', [
                'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
            ])->label('По результатам интервью сделайте вывод о текущей проблеме')->widget(Select2::class, [
                'data' => $selection_list,
                'options' => ['id' => 'descInterview_status'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        </div>

    <?php elseif ($respond->confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) : ?>

        <div class="col-md-12">

            <?php
            $selection_list = ['1' => 'Предложение привлекательно', '0' => 'Предложение не интересно'];
            ?>

            <?= $form->field($model, 'status', [
                'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
            ])->label('По результатам интервью сделайте вывод о текущем ценностном предложении')->widget(Select2::class, [
                'data' => $selection_list,
                'options' => ['id' => 'descInterview_status'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        </div>

    <?php elseif ($respond->confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) : ?>

        <div class="col-md-12">

            <?php
            $selection_list = ['1' => 'Хочу приобрести данный продукт (MVP)', '0' => 'Не хочу приобретать данный продукт (MVP)'];
            ?>

            <?= $form->field($model, 'status', [
                'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
            ])->label('По результатам интервью сделайте вывод о текущем продукте (MVP)')->widget(Select2::class, [
                'data' => $selection_list,
                'options' => ['id' => 'descInterview_status'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        </div>

    <?php endif; ?>

    <div class="form-group col-xs-12" style="display: flex; justify-content: center;">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-default',
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

</div>

<?php ActiveForm::end(); ?>
