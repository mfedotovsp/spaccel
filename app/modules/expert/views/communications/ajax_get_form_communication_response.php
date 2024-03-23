<?php

use app\models\ProjectCommunications;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\modules\expert\models\form\FormCreateCommunicationResponse;
use app\models\CommunicationTypes;
use kartik\select2\Select2;
use yii\helpers\Html;
use app\models\ExpertType;

/**
 * @var FormCreateCommunicationResponse $model
 * @var ProjectCommunications $communication
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
        border: 1px solid #828282;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 39px;
    }
    .select2-container--krajee-bs3 .select2-selection--multiple {
        height: 100%;
        padding-bottom: 2px;
        padding-top: 2px;
    }
</style>


<div class="row form-create-response-communication">

    <?php $form = ActiveForm::begin([
        'id' => 'formCreateResponseCommunication',
        'action' => Url::to([
            '/expert/communications/send',
            'adressee_id' => $communication->getSenderId(),
            'project_id' => $communication->getProjectId(),
            'type' => CommunicationTypes::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE,
            'triggered_communication_id' => $communication->getId()
        ]),
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

        <div class="col-md-12">
            <?= $form->field($model, 'answer', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => FormCreateCommunicationResponse::getAnswers(),
                'options' => [
                    'id' => 'communication_response_answer-' . $communication->getId(),
                    'class' => 'communication-response-answer'
                ],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>
        </div>

        <div class="col-md-12 communication-response-expert-types-block">
            <?= $form->field($model, 'expert_types', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => ExpertType::getListTypes($communication->expert),
                'options' => [
                    'id' => 'communication_response_expert_types-' . $communication->getId(),
                    'class' => 'communication-response-expert-types',
                    'multiple' => true
                ],
                'toggleAllSettings' => [
                    'selectLabel' => '<i class="fas fa-check-circle"></i> Выбрать все',
                    'unselectLabel' => '<i class="fas fa-times-circle"></i> Убрать все',
                    'selectOptions' => ['class' => 'text-success'],
                    'unselectOptions' => ['class' => 'text-danger'],
                ]
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'comment', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="col-md-12">

            <?= Html::submitButton('Сохранить', [
                'class' => 'btn btn-success pull-right',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#52BE7F',
                    'width' => '100px',
                    'font-size' => '16px',
                    'border-radius' => '8px',
                    'margin-left' => '20px',
                    'margin-bottom' => '10px'
                ]
            ]) ?>

            <?= Html::button('Отмена', [
                'id' => 'cancel_create_response_communication-'.$communication->getProjectId(),
                'class' => 'btn btn-default pull-right cancel-create-response-communication',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'width' => '100px',
                    'font-size' => '16px',
                    'border-radius' => '8px',
                    'margin-bottom' => '10px'
                ]
            ]) ?>

        </div>

    <?php ActiveForm::end(); ?>

</div>
