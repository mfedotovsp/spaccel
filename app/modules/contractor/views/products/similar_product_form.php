<?php

use app\modules\contractor\models\form\ContractorTaskSimilarProductForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var ContractorTaskSimilarProductForm $model
 * @var string $action
 * @var int $taskId
 * @var int|null $productId
 * @var array $productParams
 */

?>

<style>
    .select2-container--krajee .select2-selection {
        font-size: 20px;
        height: 45px;
        padding-left: 15px;
        padding-top: 7px;
        padding-bottom: 15px;
        border: 1px solid #4F4F4F;
        border-radius: 8px;
    }
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
    .select2-container--krajee .select2-selection__clear {
        top: 0.9rem;
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => $action === 'create' ? 'formCreateContractorTaskSimilarProduct' : 'formUpdateContractorTaskSimilarProduct',
    'action' => $action === 'create' ? Url::to(['/contractor/products/create-similar-product', 'taskId' => $taskId]) : Url::to(['/contractor/products/update-similar-product', 'id' => $productId]),
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

    <?php if ($model->params): ?>
        <?php foreach ($model->params as $keyParam => $param): ?>

            <div class="col-md-12 mb-15">

                <label class="pl-15" for="contractortasksimilarproductform-params-<?= $keyParam ?>">
                    <?= $productParams[$keyParam] ?>
                </label>

                <input
                    type="text"
                    id="contractortasksimilarproductform-params-<?= $keyParam ?>"
                    class="style_form_field_respond form-control"
                    name="ContractorTaskSimilarProductForm[params][<?= $keyParam ?>]"
                    value="<?= $param ?>"
                    required=""
                    placeholder=""
                    autocomplete="off"
                >

            </div>

        <?php endforeach; ?>
    <?php endif; ?>

    <div class="col-md-6">

        <?= $form->field($model, 'ownership_cost', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
            'type' => 'number',
            'required' => true,
            'max' => 100000000,
            'min' => 1,
            'class' => 'style_form_field_respond form-control',
            'autocomplete' => 'off',
        ]) ?>

    </div>

    <div class="col-md-6">

        <?= $form->field($model, 'price', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
            'type' => 'number',
            'required' => true,
            'max' => 100000000,
            'min' => 1,
            'class' => 'style_form_field_respond form-control',
            'autocomplete' => 'off',
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
