<?php

use app\modules\admin\models\form\FormCreateClient;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создание новой организации';

/**
 * @var FormCreateClient $formCreateClient
 */

?>

<div class="row container-fluid block-form-create-client">

    <div class="col-md-12" style="margin-top: 35px; margin-bottom: 35px; padding-left: 25px;">
        <?= Html::a('Шаг 2. Заполните форму создания организации' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page open_modal_instruction_page',
            'title' => 'Инструкция', 'onclick' => 'return false'
        ]) ?>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'adminCompanyCreateForm',
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

        <?= $form->field($formCreateClient, 'adminCompany')->hiddenInput()->label(false) ?>

        <div class="row" style="margin-bottom: 10px;">
            <?= $form->field($formCreateClient, 'name', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12">{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="row" style="margin-bottom: 10px;">
            <?= $form->field($formCreateClient, 'fullname', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12">{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="row" style="margin-bottom: 10px;">
            <?= $form->field($formCreateClient, 'city', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12">{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="row" style="margin-bottom: 15px;">
            <?= $form->field($formCreateClient, 'description', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12">{input}</div>'
            ])->textarea([
                'rows' => 2,
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>
        </div>

        <div class="form-group row container-fluid">
            <?= Html::submitButton('Далее', [
                'class' => 'btn btn-success pull-right',
                'style' => [
                    'color' => '#FFFFFF',
                    'background' => '#52BE7F',
                    'padding' => '0 7px',
                    'width' => '140px',
                    'height' => '40px',
                    'font-size' => '24px',
                    'border-radius' => '8px',
                ]
            ]) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>