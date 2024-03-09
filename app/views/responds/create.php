<?php

use app\models\forms\FormCreateRespondent;
use app\models\interfaces\ConfirmationInterface;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var ConfirmationInterface $confirm
 * @var FormCreateRespondent $model
 * @var bool $isMobile
 */

?>

<?php
$form = ActiveForm::begin([
    'id' => 'new_respond_form',
    'action' => Url::to(['/responds/create', 'stage' => $confirm->getStage(), 'id' => $confirm->getId()]),
    'options' => ['class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]); ?>

<?php if (!$isMobile): ?>

    <div class="row">

        <div class="col-md-12">

            <?= $form->field($model, 'name', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Иванов Иван Иванович',
                'autocomplete' => 'off'
            ]) ?>

        </div>

        <div class="form-group col-md-12" style="display: flex; justify-content: center;">

            <?= Html::submitButton('Сохранить', [
                'class' => 'btn btn-default',
                'id' => 'save_respond',
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

<?php else: ?>

    <div class="col-xs-10">

        <?= $form->field($model, 'name', ['template' => '{input}'])->textInput([
            'maxlength' => true,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Фамилия, имя, отчество',
            'autocomplete' => 'off'
        ]) ?>

    </div>

    <div class="col-xs-2 pull-right">
        <?= Html::a(Html::img('@web/images/icons/cancel_danger.png'), ['#'], ['class' => 'link_cancel_search_field_mobile show_add_respond']) ?>
    </div>

    <div class="form-group col-xs-12">

        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-default',
            'id' => 'save_respond',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'margin-bottom' => '15px',
                'background' => '#7F9FC5',
                'width' => '100%',
                'height' => '40px',
                'border-radius' => '8px',
                'text-transform' => 'uppercase',
                'font-size' => '16px',
                'color' => '#FFFFFF',
                'font-weight' => '700',
            ]

        ]) ?>

    </div>

<?php endif; ?>

<?php ActiveForm::end(); ?>
