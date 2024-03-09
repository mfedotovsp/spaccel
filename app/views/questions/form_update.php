<?php

use app\models\forms\FormUpdateQuestion;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var FormUpdateQuestion $model
 */

?>


<div class="col-md-12 form_update_question" style="padding: 0;">

    <?php $form = ActiveForm::begin([
        'id' => 'updateQuestionForm',
        'action' => Url::to(['/questions/update', 'stage' => $model->confirm->getStage(), 'id' => $model->getId()]),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

    <div class="col-md-12">

        <?= $form->field($model, 'title', ['template' => '{input}'])
            ->textInput([
                'maxlength' => true,
                'required' => true,
                'placeholder' => 'Отредактируйте вопрос',
                'id' => 'update_text_question_confirm',
                'class' => 'style_form_field_respond',
                'autocomplete' => 'off'])
            ->label(false) ?>

    </div>

    <div class="col-xs-12" style="display: flex; justify-content: flex-end;">

        <?= Html::a('Отмена', ['/questions/get-query-questions', 'stage' => $model->confirm->getStage(), 'id' => $model->confirm->getId()],[
            'class' => 'btn btn-lg btn-default col-xs-6 col-sm-2 col-lg-1 submit_update_question_cancel',
            'style' => [
                'margin-bottom' => '15px',
                'margin-right' => '5px',
                'height' => '40px',
                'border-radius' => '8px',
                'text-transform' => 'uppercase',
                'font-size' => '16px',
                'font-weight' => '700',
            ]
        ]) ?>

        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-lg btn-default col-xs-6 col-sm-2 col-lg-1',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'margin-bottom' => '15px',
                'background' => '#7F9FC5',
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
