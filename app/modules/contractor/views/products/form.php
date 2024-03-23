<?php

use app\models\ContractorTaskProducts;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var ContractorTaskProducts $model
 * @var string $action
 * @var int $taskId
 * @var int|null $productId
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

<?php $form = ActiveForm::begin([
    'id' => $action === 'create' ? 'formCreateContractorTaskProduct' : 'formUpdateContractorTaskProduct',
    'action' => $action === 'create' ? Url::to(['/contractor/products/create', 'taskId' => $taskId]) : Url::to(['/contractor/products/update', 'id' => $productId]),
    'options' => ['class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]); ?>

<div class="row">
    <div class="col-md-12">

        <?= $form->field($model, 'name', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]) ?>

    </div>

    <div class="col-md-12">

        <?= $form->field($model, 'price', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
            'type' => 'number',
            'required' => true,
            'max' => 100000000,
            'min' => 1,
            'class' => 'style_form_field_respond form-control',
            'autocomplete' => 'off',
        ]) ?>

    </div>

    <div class="col-md-12">

        <?= $form->field($model, 'satisfaction', [
            'template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>',
        ])->widget(Select2::class, [
            'data' => [
                ContractorTaskProducts::SATISFACTION_LOW => 'Низкая',
                ContractorTaskProducts::SATISFACTION_MIDDLE => 'Средняя',
                ContractorTaskProducts::SATISFACTION_HIGH => 'Высокая'
            ],
            'options' => ['id' => $action === 'create' ? 'create_product_satisfaction' : 'update_product_satisfaction'],
            'disabled' => false,  //Сделать поле неактивным
            'hideSearch' => true, //Скрытие поиска
        ]) ?>

    </div>

    <div class="col-md-12">

        <?= $form->field($model, 'flaws', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textarea([
            'rows' => 1,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>

    </div>

    <div class="col-md-12">

        <?= $form->field($model, 'advantages', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textarea([
            'rows' => 1,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>

    </div>

    <div class="col-md-12">

        <?= $form->field($model, 'suppliers', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textarea([
            'rows' => 1,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>

    </div>

    <div class="form-group col-xs-12" style="display: flex; justify-content: center; margin-top: 30px;">

        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-default pull-right',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'background' => '#7F9FC5',
                'width' => '180px',
                'height' => '40px',
                'border-radius' => '8px',
                'text-transform' => 'uppercase',
                'font-size' => '16px',
                'color' => '#FFFFFF',
                'font-weight' => '700',
            ]

        ]) ?>

    </div>

</div>

<?php ActiveForm::end(); ?>
