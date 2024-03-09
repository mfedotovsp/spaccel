<?php

use app\modules\admin\models\form\FormCreateRatesPlan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var FormCreateRatesPlan $model
 */

?>

<div class="row container-fluid block-form-create-rates-plan">

    <h3 class="text-center bolder">Форма создания нового тарифного плана</h3>

    <?php $form = ActiveForm::begin([
        'id' => 'ratesPlanCreateForm',
        'action' => Url::to(['/admin/rates-plans/create']),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

        <div class="row" style="margin-bottom: 10px;">
            <?= $form->field($model, 'name', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12">{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="row" style="margin-bottom: 15px;">
            <?= $form->field($model, 'description', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12">{input}</div>'
            ])->textarea([
                'rows' => 2,
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>
        </div>

        <div class="row" style="margin-bottom: 10px;">
            <?= $form->field($model, 'max_count_project_user', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-2">{input}</div>'
            ])->textInput([
                'type' => 'number',
                'class' => 'style_form_field_respond form-control',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="row" style="margin-bottom: 10px;">
            <?= $form->field($model, 'max_count_tracker', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-2">{input}</div>'
            ])->textInput([
                'type' => 'number',
                'class' => 'style_form_field_respond form-control',
                'autocomplete' => 'off'
            ]) ?>
        </div>

        <div class="form-group row container-fluid">
            <?= Html::submitButton('Сохранить', [
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

    <hr>

</div>
