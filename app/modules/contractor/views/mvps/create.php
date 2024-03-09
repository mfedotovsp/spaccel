<?php

use app\models\ConfirmGcp;
use app\models\ContractorTasks;
use app\models\forms\FormCreateMvp;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var ConfirmGcp $confirmGcp
 * @var FormCreateMvp $model
 * @var ContractorTasks $task
 */

?>

<div class="form-create-mvp">

    <?php
    $form = ActiveForm::begin([
        'id' => 'hypothesisCreateForm',
        'action' => Url::to(['/contractor/mvps/create', 'id' => $task->getId()]),
        'options' => ['class' => 'g-py-15 hypothesisCreateForm'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]);
    ?>

    <div class="row" style="color: #4F4F4F;">

        <div class="col-md-12" style="margin-top: 10px; padding-left: 20px; padding-right: 20px;">
            Minimum Viable Product(MVP) — минимально жизнеспособный продукт, концепция минимализма программной комплектации выводимого на рынок устройства.
            Минимально жизнеспособный продукт - продукт, обладающий минимальными, но достаточными для удовлетворения первых потребителей функциями.
            Основная задача — получение обратной связи для формирования гипотез дальнейшего развития продукта.
        </div>

        <div class="col-md-12" style="margin-top: 10px;">

            <?= $form->field($model, 'description', ['template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>'])->label('Описание минимально жизнеспособного продукта')->textarea([
                'rows' => 4,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Примеры: презентация, макет, программное обеспечение, опытный образец, видео и т.д.',
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
