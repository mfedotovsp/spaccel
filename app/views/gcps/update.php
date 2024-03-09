<?php

use app\models\Gcps;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Gcps $model
 */

?>


<div class="form-update-gcp">

    <?php
    $form = ActiveForm::begin([
        'id' => 'hypothesisUpdateForm',
        'action' => Url::to(['/gcps/update', 'id' => $model->getId()]),
        'options' => ['class' => 'g-py-15 hypothesisUpdateForm'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]);
    ?>

        <div class="row" style="color: #4F4F4F;">
            <div class="col-md-12">

                <?= $form->field($model, 'description', ['template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>'])->label('Описание гипотезы ценностного предложения')->textarea([
                    'rows' => 8,
                    'maxlength' => true,
                    'required' => true,
                    'class' => 'style_form_field_respond form-control',
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
