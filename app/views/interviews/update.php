<?php

use app\models\AnswersQuestionsConfirmGcp;
use app\models\AnswersQuestionsConfirmMvp;
use app\models\AnswersQuestionsConfirmProblem;
use app\models\AnswersQuestionsConfirmSegment;
use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\Gcps;
use app\models\InterviewConfirmGcp;
use app\models\InterviewConfirmMvp;
use app\models\InterviewConfirmProblem;
use app\models\InterviewConfirmSegment;
use app\models\Mvps;
use app\models\Problems;
use app\models\RespondsGcp;
use app\models\RespondsMvp;
use app\models\RespondsProblem;
use app\models\RespondsSegment;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use app\models\StageConfirm;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\Url;
use app\models\QuestionStatus;

/**
 * @var RespondsSegment|RespondsProblem|RespondsGcp|RespondsMvp $respond
 * @var InterviewConfirmSegment|InterviewConfirmProblem|InterviewConfirmGcp|InterviewConfirmMvp $model
 * @var ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp $confirm
 * @var Segments|Problems|Gcps|Mvps $hypothesis
 * @var AnswersQuestionsConfirmSegment|AnswersQuestionsConfirmProblem|AnswersQuestionsConfirmGcp|AnswersQuestionsConfirmMvp $answers
 * @var bool $isOnlyNotDelete
 * @var array $questionsConfirm
 */

?>


<?php if ($isOnlyNotDelete && $hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE &&
    ((User::isUserSimple(Yii::$app->user->identity['username']) && !$respond->getContractorId()) ||
        (User::isUserContractor(Yii::$app->user->identity['username']) && $respond->getContractorId() === Yii::$app->user->getId() && ($task = ContractorTasks::findOne($respond->getTaskId())) &&
            in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)))) : ?>

    <?php $form = ActiveForm::begin([
        'id' => 'formUpdateDescInterview',
        'action' => Url::to(['/interviews/update', 'stage' => $confirm->getStage(), 'id' => $model->getId()]),
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

        <?php endforeach; ?>
    <?php endif; ?>

    <div class="row" style="margin-bottom: 15px;">

        <div class="col-md-12">

            <p style="padding-left: 5px;"><b>Приложить файл</b> <span style="color: #BDBDBD; padding-left: 20px;">png, jpg, jpeg, pdf, txt, doc, docx, xls</span></p>


            <?php if (!empty($model->getInterviewFile())) : ?>


                <div class="feed-exp">

                    <div style="display:flex; margin-top: -5px;margin-bottom: -30px;">

                        <?= $form->field($model, 'loadFile')
                            ->fileInput([
                                'id' => 'descInterviewUpdateFile', 'class' => 'sr-only'
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

                        <div class="file_name_update_form" style="padding-left: 20px; padding-top: 5px;">Файл не выбран</div>

                    </div>

                </div>


                <div style="margin-top: -5px; margin-bottom: 30px;">

                    <div style="display: flex; align-items: center;">

                        <?= Html::a('Скачать файл', ['/interviews/download', 'stage' => $confirm->getStage(), 'id' => $model->getId()], [
                            'target' => '_blank',
                            'class' => 'btn btn-success interview_file_update',
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

                        ]) . ' update.php' . Html::a('Удалить файл', ['/interviews/delete-file', 'stage' => $confirm->getStage(), 'id' => $model->getId()], [
                            'id' => 'link_delete_file',
                            'class' => "btn btn-default link-delete",
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'color' => '#FFFFFF',
                                'width' => '170px',
                                'height' => '40px',
                                'font-size' => '16px',
                                'border-radius' => '8px',
                                'text-transform' => 'uppercase',
                                'font-weight' => '700',
                                'padding-top' => '9px'
                            ]
                        ]) ?>

                    </div>

                    <div class="title_name_update_form" style="padding-left: 5px; padding-top: 5px; margin-bottom: -10px;"><?= $model->getInterviewFile() ?></div>

                </div>


            <?php endif;?>


            <?php if (empty($model->getInterviewFile())) : ?>

                <div style="display:flex; margin-top: -5px;">

                    <?= $form->field($model, 'loadFile')
                        ->fileInput([
                            'id' => 'descInterviewUpdateFile', 'class' => 'sr-only'
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

                    <div class="file_name_update_form" style="padding-left: 20px; padding-top: 5px;">Файл не выбран</div>

                </div>

            <?php endif;?>


        </div>

        <?php if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) : ?>

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
                $selection_list = [ '0' => 'Респондент не является представителем сегмента', '1' => 'Респондент является представителем сегмента', ];
                ?>

                <?= $form->field($model, 'status', [
                    'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
                ])->label('Этот респондент является представителем сегмента?')->widget(Select2::class, [
                    'data' => $selection_list,
                    'options' => ['id' => 'descInterview_status_update'],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>

            </div>

        <?php elseif ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) : ?>

            <div class="col-md-12">

                <?php
                $selection_list = [ '0' => 'Проблемы не существует или она малозначимая', '1' => 'Проблема значимая', ];
                ?>

                <?= $form->field($model, 'status', [
                    'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
                ])->label('По результатам интервью сделайте вывод о текущей проблеме')->widget(Select2::class, [
                    'data' => $selection_list,
                    'options' => ['id' => 'descInterview_status_update'],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>

            </div>

        <?php elseif ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) : ?>

            <div class="col-md-12">

                <?php
                $selection_list = [ '0' => 'Предложение не интересно', '1' => 'Предложение привлекательно', ];
                ?>

                <?= $form->field($model, 'status', [
                    'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
                ])->label('По результатам интервью сделайте вывод о текущем ценностном предложении')->widget(Select2::class, [
                    'data' => $selection_list,
                    'options' => ['id' => 'descInterview_status_update'],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => true, //Скрытие поиска
                ]) ?>

            </div>

        <?php elseif ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) : ?>

            <div class="col-md-12">

                <?php
                $selection_list = [ '0' => 'Не хочу приобретать данный продукт (MVP)', '1' => 'Хочу приобрести данный продукт (MVP)', ];
                ?>

                <?= $form->field($model, 'status', [
                    'template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>',
                ])->label('По результатам интервью сделайте вывод о текущем продукте (MVP)')->widget(Select2::class, [
                    'data' => $selection_list,
                    'options' => ['id' => 'descInterview_status_update'],
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


<?php else : ?>


    <div class="row" style="margin-bottom: 15px; color: #4F4F4F;">

        <div class="col-md-12" style="padding: 0 20px;">
            <div style="font-weight: 700;">Респондент</div>
            <div><?= $respond->getName() ?></div>
        </div>

        <div class="col-md-12" style="padding: 0 20px; font-size: 24px; margin-top: 5px;">
            <div style="border-bottom: 1px solid #ccc;">Ответы на вопросы интервью</div>
        </div>

        <?php foreach ($answers as $index => $answer) : ?>

            <div class="col-md-12" style="padding: 0 20px; margin-top: 10px;">

                <?php
                $question = $isOnlyNotDelete ? $answer->question : $questionsConfirm[$answer->getId()];
                if ($question->getStatus() === QuestionStatus::STATUS_ONE_STAR) : ?>
                    <div style="font-weight: 700; color: #52be7f;"><?= $question->getTitle() ?></div>
                <?php elseif($question->getStatus() === QuestionStatus::STATUS_NOT_STAR) : ?>
                    <div style="font-weight: 700;"><?= $question->getTitle() ?></div>
                <?php endif; ?>

                <div><?= $answer->getAnswer() ?></div>

            </div>

        <?php endforeach; ?>

        <?php if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) : ?>

            <div class="col-md-12" style="padding: 0 20px; margin-bottom: 10px; margin-top: 10px;">
                <div style="font-weight: 700; border-top: 1px solid #ccc; padding-top: 10px;">Варианты проблем</div>
                <div><?= $model->getResult() ?></div>
            </div>

        <?php endif; ?>

        <div class="col-md-12">

            <p style="padding-left: 5px; font-weight: 700;">Приложенный файл</p>

            <?php if (!empty($model->getInterviewFile())) : ?>

                <div style="margin-top: -5px; margin-bottom: 20px;">

                    <div style="display: flex; align-items: center;">

                        <?= Html::a('Скачать файл', ['/interviews/download', 'stage' => $confirm->getStage(), 'id' => $model->getId()], [
                            'target' => '_blank',
                            'class' => 'btn btn-success interview_file_update',
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

                    <div class="title_name_update_form" style="padding-left: 5px; padding-top: 5px; margin-bottom: -10px;"><?= $model->getInterviewFile() ?></div>

                </div>

            <?php endif;?>

            <?php if (empty($model->getInterviewFile())) : ?>

                <div class="col-md-12" style="padding-left: 5px; margin-bottom: 20px;">Файл не выбран</div>

            <?php endif;?>

        </div>

        <?php if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) : ?>

            <div class="col-md-12" style="padding: 0 20px; margin-bottom: 15px;">
                <div style="font-weight: 700;">Этот респондент является представителем сегмента?</div>
                <div>
                    <?php
                    if ($model->getStatus() === 1) {
                        echo 'Респондент является представителем сегмента';
                    } else {
                        echo 'Респондент не является представителем сегмента';
                    }
                    ?>
                </div>
            </div>

        <?php elseif ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) : ?>

            <div class="col-md-12" style="padding: 0 20px; margin-bottom: 5px;">
                <div style="font-weight: 700;">Вывод по результам интервью о текущей проблеме</div>
                <div>
                    <?php
                    if ($model->getStatus() === 1) {
                        echo 'Проблема значимая';
                    } else {
                        echo 'Проблемы не существует или она малозначимая';
                    }
                    ?>
                </div>
            </div>

        <?php elseif ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) : ?>

            <div class="col-md-12" style="padding: 0 20px; margin-bottom: 5px;">
                <div style="font-weight: 700;">Вывод по результам интервью о текущем предложении</div>
                <div>
                    <?php
                    if ($model->getStatus() === 1) {
                        echo 'Предложение привлекательно';
                    } else {
                        echo 'Предложение не интересно';
                    }
                    ?>
                </div>
            </div>

        <?php elseif ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) : ?>

            <div class="col-md-12" style="padding: 0 20px; margin-bottom: 5px;">
                <div style="font-weight: 700;">Вывод по результам интервью о текущем продукте (MVP)</div>
                <div>
                    <?php
                    if ($model->getStatus() === 1) {
                        echo 'Хочу приобрести данный продукт (MVP)';
                    } else {
                        echo 'Не хочу приобретать данный продукт (MVP)';
                    }
                    ?>
                </div>
            </div>

        <?php endif; ?>

    </div>

<?php endif; ?>
