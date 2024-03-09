<?php

use app\models\ConfirmProblem;
use app\models\ContractorTasks;
use app\models\forms\FormCreateGcp;
use app\models\Segments;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var ConfirmProblem $confirmProblem
 * @var FormCreateGcp $model
 * @var ContractorTasks $task
 * @var Segments $segment
 */

?>

<div class="form-create-gcp">

    <?php
    $form = ActiveForm::begin([
        'id' => 'hypothesisCreateForm',
        'action' => Url::to(['/contractor/gcps/create', 'id' => $task->getId()]),
        'options' => ['class' => 'g-py-15 hypothesisCreateForm'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]);
    ?>

    <div class="row" style="color: #4F4F4F;">


        <div class="col-md-12" style="margin-top: 10px;">

            <?= $form->field($model, 'good', ['template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>'])->label('Формулировка перспективного продукта (товара / услуги):')->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
                'autocomplete' => 'off'
            ]) ?>

        </div>


        <div class="col-md-12" style="padding-left: 20px; font-weight: 700;">

            Для какого сегмента предназначено:
            <span class="gcp_create_segment_link">
                <?= Html::a('Данные сегмента', ['/segments/show-all-information', 'id' => $segment->getId()], [
                    'class' => 'openAllInformationSegment',
                    'title' => 'Посмотреть описание',
                ]) ?>
            </span>

        </div>


        <div class="col-md-12" style="padding-left: 20px; margin-top: 10px;">

            <div style="font-weight: 700;">
                Для удовлетворения следующей потребности сегмента:
            </div>

            <div><?= $confirmProblem->getNeedConsumer() ?></div>

        </div>


        <div class="col-md-12" style="margin-top: 10px;">

            <?= $form->field($model, 'benefit', ['template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>'])->label('Какую выгоду дает использование данного продукта потребителю (представителю сегмента):')->textarea([
                'rows' => 6,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Все выгоды формулируются по трем критериям: временной, экономический и качественный факторы.
Первые два параметра выгоды должны быть исчисляемыми. Параметр качества(исчисляемый /лаконичный текст).',
            ]) ?>

        </div>


        <div class="col-md-12">

            <?= $form->field($model, 'contrast', ['template' => '<div style="padding-left: 5px;">{label}</div><div>{input}</div>'])->label('По сравнению с каким продуктом заявлена выгода (с чем сравнивается):')->textarea([
                'rows' => 3,
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Укажите параметры аналога, с которыми сравниваются параметры нового продукта',
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

