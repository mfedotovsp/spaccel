<?php

use app\models\ConfirmMvp;
use app\models\forms\FormCreateBusinessModel;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var ConfirmMvp $confirmMvp
 * @var FormCreateBusinessModel $model
 */

?>


<div class="form-create-business_model">

    <?php
    $form = ActiveForm::begin([
        'id' => 'hypothesisCreateForm',
        'action' => Url::to(['/business-model/create', 'id' => $confirmMvp->getId()]),
        'options' => ['class' => 'g-py-15 hypothesisCreateForm'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]);
    ?>

    <div class="row">

        <div class="col-md-12 mt-10">

            <?= $form->field($model, 'partners', ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>

        </div>

        <div class="col-md-12 mt-10">

            <?= $form->field($model, 'resources', ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>

        </div>

        <div class="col-md-12 mt-10">

            <?= $form->field($model, 'relations', ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>

        </div>

        <div class="col-md-12 mt-10">

            <?= $form->field($model, 'distribution_of_sales', ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>

        </div>

        <div class="col-md-12 mt-10">

            <?= $form->field($model, 'cost', ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>

        </div>

        <div class="col-md-12 mt-10">

            <?= $form->field($model, 'revenue', ['template' => '<div class="pl-5">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>

        </div>

    </div>


    <div class="form-group row container-fluid" style="display: flex; justify-content: center; margin-top: 20px;">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-default pull-right',
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

    <?php ActiveForm::end(); ?>

</div>
