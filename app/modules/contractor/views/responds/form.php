<?php

use app\models\forms\UpdateFormRespond;
use app\models\interfaces\ConfirmationInterface;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var ConfirmationInterface $confirm
 * @var UpdateFormRespond $model
 * @var bool $isOnlyNotDelete
 * @var string $typeForm
 * @var int|null $taskId
 * @var bool $disabled
*/

?>


<?php
$model->name = $typeForm === 'create' ? '' : $model->getName();
if ($isOnlyNotDelete && $taskId && User::isUserContractor(Yii::$app->user->identity['username']) &&
    $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) :?>


    <?php $form = ActiveForm::begin([
        'id' => 'formUpdateRespond',
        'action' => Url::to(['/contractor/responds/update', 'stage' => $confirm->getStage(), 'id' => $model->getId(), 'taskId' => $taskId]),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

    <div class="row">
        <div class="col-md-6">

            <?= $form->field($model, 'name', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Иванов Иван Иванович',
                'autocomplete' => 'off',
                'disabled' => $disabled
            ]) ?>

        </div>

        <div class="col-md-6">

            <?= $form->field($model, 'email', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
                'type' => 'email',
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'ivanov@gmail.com',
                'autocomplete' => 'off',
                'disabled' => $disabled
            ]) ?>

        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'info_respond', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textarea([
                'rows' => 1,
                'required' => true,
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Кто? Откуда? Чем занимается?',
                'disabled' => $disabled
            ]) ?>

            <?= $form->field($model, 'place_interview', ['template' => '<div style="padding-left: 15px;">{label}</div><div>{input}</div>'])->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => 'Организация, адрес',
                'autocomplete' => 'off',
                'disabled' => $disabled
            ]) ?>

        </div>

        <div class="col-xs-12 col-sm-6 col-md-4">

            <?= '<label class="control-label" style="padding-left: 15px;">Плановая дата интервью</label>' ?>
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => explode('\\', get_class($model))[3].'[date_plan]',
                'value' => $model->date_plan === null ? date('d.m.Y') : date('d.m.Y', $model->date_plan),
                'readonly' => true,
                'disabled' => $disabled,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ],
                'options' => [
                    'id' => 'datePlan',
                    'class' => 'style_form_field_respond form-control'
                ],
                'pluginEvents' => [
                    "hide" => "function(e) {e.preventDefault(); e.stopPropagation();}",
                ],
            ]) ?>

        </div>

        <?php if ($typeForm === 'create'): ?>

            <div class="form-group col-xs-12 col-sm-6 col-md-8" style="display: flex; justify-content: center; margin-top: 30px;">
                <?= Html::button('Отмена', [
                    'class' => 'btn btn-default pull-right link_cancel_search_field_mobile show_add_respond',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'width' => '100px',
                        'height' => '40px',
                        'border-radius' => '8px',
                        'text-transform' => 'uppercase',
                        'font-size' => '16px',
                        'font-weight' => '700',
                        'margin-right' => '10px'
                    ]
                ]) ?>
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

        <?php else: ?>

            <div class="form-group col-xs-12 col-sm-6 col-md-8" style="display: flex; justify-content: center; margin-top: 30px;">

                <?php if (!$disabled): ?>
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
                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

    <?php ActiveForm::end(); ?>


<?php else : ?>


    <div class="row" style="margin-top: -20px;">
        <div class="col-md-8">
            <div style="font-size: 24px;">
                <?= $model->getName() ?>
            </div>
            <?= $model->info_respond ?>
        </div>
        <div class="col-md-4" style="padding-top: 5px;">
            <div class="bolder">E-mail:</div>
            <?= $model->getEmail() ?>
            <div class="bolder">Место проведения интервью</div>
            <?= $model->getPlaceInterview() ?>
            <div class="bolder">Плановая дата интервью:</div>
            <?php if ($model->getDatePlan()) : ?>
                <?= date('d.m.Y', $model->getDatePlan()) ?>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>
