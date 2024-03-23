<?php

use app\models\ConfirmMvp;
use app\models\forms\FormCreateQuestion;
use app\models\Mvps;
use app\models\QuestionsConfirmMvp;
use app\models\QuestionStatus;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var QuestionsConfirmMvp[] $questions
 * @var Mvps $mvp
 * @var array $queryQuestions
 * @var ConfirmMvp $model
 * @var FormCreateQuestion $newQuestion
 */

?>

<!--Сюда помещаем форму для создания нового вопроса-->
<div class="form-newQuestion-panel" style="display: none;"></div>

<!--Список вопросов-->
<div id="QuestionsTable-container" class="row" style="padding-top: 30px; padding-bottom: 30px;">

    <?php foreach ($questions as $q => $question) : ?>

        <div class="col-xs-12 string_question string_question-<?= $question->getId() ?>">

            <div class="row style_form_field_questions">
                <div class="col-xs-8 col-sm-9 col-md-9 col-lg-10">
                    <div style="display:flex;">
                        <div class="number_question" style="padding-right: 15px;"><?= ($q+1) . '. ' ?></div>
                        <div class="title_question"><?= $question->getTitle() ?></div>
                    </div>
                </div>
                <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2 delete_question_link">

                    <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                        <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]), [
                            Url::to(['/questions/delete', 'stage' => $model->getStage(), 'id' => $question->getId()])],[
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'delete-question-confirm-hypothesis pull-right',
                            'id' => 'delete_question-'.$question->getId(),
                        ]) ?>

                        <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-top' => '3px', ]]), [
                            Url::to(['/questions/get-form-update', 'stage' => $model->getStage(), 'id' => $question->getId()])], [
                            'class' => 'showQuestionUpdateForm pull-right',
                            'style' => ['margin-right' => '20px'],
                            'title' => 'Редактировать вопрос',
                        ]) ?>

                        <?php if ($question->getStatus() === QuestionStatus::STATUS_NOT_STAR) : ?>
                            <?= Html::a('<div class="star"></div>', Url::to(['/questions/change-status', 'stage' => $model->getStage(), 'id' => $question->getId()]), [
                                'class' => 'star-link', 'title' => 'Значимость вопроса'
                            ]) ?>
                        <?php elseif ($question->getStatus() === QuestionStatus::STATUS_ONE_STAR) : ?>
                            <?= Html::a('<div class="star active"></div>', Url::to(['/questions/change-status', 'stage' => $model->getStage(), 'id' => $question->getId()]), [
                                'class' => 'star-link', 'title' => 'Значимость вопроса'
                            ]) ?>
                        <?php endif; ?>

                    <?php else : ?>

                        <?php if ($question->getStatus() === QuestionStatus::STATUS_NOT_STAR) : ?>
                            <div class="star-passive" title="Значимость вопроса">
                                <div class="star"></div>
                            </div>
                        <?php elseif ($question->getStatus() === QuestionStatus::STATUS_ONE_STAR) : ?>
                            <div class="star-passive" title="Значимость вопроса">
                                <div class="star active"></div>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>

        </div>

    <?php endforeach; ?>

</div>


<!--Форма для добаления нового вопроса-->
<div style="display: none;">
    <div class="col-md-12 form-newQuestion" style="margin-top: 20px; padding: 0;">

        <?php $form = ActiveForm::begin([
            'id' => 'addNewQuestion',
            'action' => Url::to(['/questions/create', 'stage' => $model->getStage(), 'id' => $model->getId()]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]);
        ?>

        <div class="col-xs-12 col-sm-9 col-lg-10">

            <?= $form->field($newQuestion, 'title', ['template' => '{input}', 'options' => ['style' => ['position' => 'absolute', 'top' => '0', 'z-index' => '20', 'left' => '15px', 'right' => '15px']]])
                ->textInput([
                    'maxlength' => true,
                    'required' => true,
                    'placeholder' => 'Введите свой вопрос или выберите готовый из выпадающего списка',
                    'id' => 'add_text_question_confirm',
                    'class' => 'style_form_field_respond',
                    'autocomplete' => 'off'])
                ->label(false)
            ?>

            <?= Html::a('<span class="triangle-bottom"></span>', ['#'], [
                'id' => 'button_add_text_question_confirm',
                'class' => 'btn'
            ]) ?>

            <?= $form->field($newQuestion, 'list_questions', ['template' => '{input}', 'options' => ['style' => ['position' => 'absolute', 'top' => '0', 'z-index' => '10', 'left' => '15px', 'right' => '15px']]])
                ->widget(Select2::class, [
                    'data' => $queryQuestions,
                    'options' => [
                        'id' => 'add_new_question_confirm',
                        'placeholder' => 'Выберите вариант из списка готовых вопросов',
                    ],
                    'pluginEvents' => [
                        "select2:open" => 'function() { 
                                        $(".select2-container--krajee-bs3 .select2-dropdown").css("border-color","#828282");
                                        $(".select2-container--krajee-bs3.select2-container--open .select2-selection, .select2-container--krajee-bs3 .select2-selection:focus").css("border-color","#828282");
                                        $(".select2-container--krajee-bs3.select2-container--open .select2-selection, .select2-container--krajee-bs3 .select2-selection:focus").css("box-shadow","none"); 
                                    }',
                    ],
                    'disabled' => false,  //Сделать поле неактивным
                    'hideSearch' => false, //Скрытие поиска
                ])
            ?>

        </div>

        <div class="col-xs-12 col-sm-3 col-lg-2">
            <?= Html::submitButton('Сохранить', [
                'class' => 'btn btn-lg btn-default',
                'id' => 'submit_addNewQuestion',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'margin-bottom' => '15px',
                    'background' => '#7F9FC5',
                    'width' => '100%',
                    'height' => '40px',
                    'border-radius' => '8px',
                    'text-transform' => 'uppercase',
                    'font-size' => '16px',
                    'color' => '#FFFFFF',
                    'font-weight' => '700',
                ]
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
