<?php

use app\models\ConfirmSegment;
use app\models\forms\FormUpdateConfirmSegment;
use app\models\Projects;
use app\models\StatusConfirmHypothesis;
use yii\widgets\ActiveForm;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var FormUpdateConfirmSegment $formUpdateConfirmSegment
 * @var ConfirmSegment $model
 * @var Projects $project
 * @var int $countContractorResponds
*/

?>

<?php if (!User::isUserSimple(Yii::$app->user->identity['username']) || $model->segment->getExistConfirm() !== StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

    <div class="container-fluid form-view-data-confirm">

        <div class="row row_header_data">

            <div class="col-sm-12 col-md-9" style="padding: 5px 0 0 0;">
                <?= Html::a('Исходные данные подтверждения' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-segment/get-instruction-step-one'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

        </div>


        <div class="container-fluid content-view-data-confirm">

            <div class="row">
                <div class="col-md-12">Цель проекта</div>
                <div class="col-md-12"><?= $project->getPurposeProject() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Приветствие в начале встречи</div>
                <div class="col-md-12"><?= $model->getGreetingInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Информация о вас для респондентов</div>
                <div class="col-md-12"><?= $model->getViewInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
                <div class="col-md-12"><?= $model->getReasonInterview() ?></div>
            </div>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])): ?>
                <div class="row">
                    <div class="col-md-12">Количество респондентов, занятых исполнителями:
                        <span><?= $countContractorResponds ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">Планируемое количество респондентов:
                    <span><?= $model->getCountRespond() ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">Необходимое количество респондентов, соотв. сегменту:
                    <span><?= $model->getCountPositive() ?></span>
                </div>
            </div>

        </div>

    </div>

<?php else: ?>

    <div class="container-fluid form-update-data-confirm">

        <?php
        $form = ActiveForm::begin([
            'id' => 'update_data_interview',
            'class' => 'update_data_interview',
            'action' => Url::to(['/confirm-segment/update', 'id' => $model->getId()]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>


        <div class="row row_header_data">

            <div class="col-sm-12 col-md-6" style="padding: 5px 0 0 0;">
                <?= Html::a('Исходные данные подтверждения' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-segment/get-instruction-step-one'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

        </div>


        <div class="container-fluid">


            <div class="row pt-20 pb-5 pl-5">

                <div class="col-md-12 bolder">
                    Цель проекта
                </div>

                <div class="col-md-12">
                    <?= $project->getPurposeProject() ?>
                </div>

            </div>


            <div class="row pt-5 pb-5">

                <?php $placeholder = 'Написать разумное обоснование, почему вы проводите это интервью, чтобы респондент поверил вам и начал говорить с вами открыто, не зажато.' ?>

                <?= $form->field($formUpdateConfirmSegment, 'greeting_interview', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->textarea([
                    'rows' => 3,
                    'placeholder' => $placeholder,
                    'maxlength' => true,
                    'required' => true,
                    'class' => 'style_form_field_respond form-control',
                ]) ?>

            </div>

            <div class="row pt-5 pb-5">

                <?php $placeholder = 'Фраза, которая соответствует статусу респондента и настраивает на нужную волну сотрудничества.' ?>

                <?= $form->field($formUpdateConfirmSegment, 'view_interview', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->textarea([
                    'rows' => 3,
                    'placeholder' => $placeholder,
                    'maxlength' => true,
                    'required' => true,
                    'class' => 'style_form_field_respond form-control',
                ]) ?>

            </div>

            <div class="row pt-5 pb-5">

                <?php $placeholder = 'Фраза, которая описывает, чем занимается интервьюер' ?>

                <?= $form->field($formUpdateConfirmSegment, 'reason_interview', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->textarea([
                    'rows' => 3,
                    'placeholder' => $placeholder,
                    'maxlength' => true,
                    'required' => true,
                    'class' => 'style_form_field_respond form-control',
                ]) ?>

            </div>

            <div class="row pt-10">
                <div class="col-xs-12 pl-20">
                    <span class="bolder">
                        Количество респондентов, занятых исполнителями:
                    </span>
                    <span>
                        <?= $countContractorResponds ?>
                    </span>
                </div>
            </div>

            <div class="row pt-5 pb-5">

                <?= $form->field($formUpdateConfirmSegment, 'count_respond', [
                    'template' => '<div class="col-xs-12 col-sm-9 col-md-10 pl-20">{label}</div><div class="col-xs-12 col-sm-3 col-md-2">{input}</div>'
                ])->label('<div>Количество респондентов</div><div style="font-weight: 400;font-size: 13px;">(укажите значение в диапазоне от 1 до 100)</div>')
                    ->textInput([
                        'type' => 'number',
                        'required' => true,
                        'class' => 'style_form_field_respond form-control',
                        'id' => 'confirm_count_respond',
                        'autocomplete' => 'off',
                        'min' => $countContractorResponds
                    ])
                ?>

            </div>

            <div class="row">

                <?= $form->field($formUpdateConfirmSegment, 'count_positive', [
                    'template' => '<div class="col-xs-12 col-sm-9 col-md-10 pl-20">{label}</div><div class="col-xs-12 col-sm-3 col-md-2">{input}</div>'
                ])->label('Необходимое количество респондентов, соотв. сегменту')
                    ->textInput([
                        'type' => 'number',
                        'required' => true,
                        'class' => 'style_form_field_respond form-control',
                        'id' => 'confirm_count_positive',
                        'autocomplete' => 'off'
                    ])
                ?>

            </div>

            <div class="row">
                <div class="col-md-12" style="display:flex;justify-content: center;">
                    <?= Html::submitButton('Сохранить', [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'background' => '#7F9FC5',
                            'color' => '#ffffff',
                            'width' => '180px',
                            'height' => '40px',
                            'font-size' => '16px',
                            'text-transform' => 'uppercase',
                            'font-weight' => '700',
                            'padding-top' => '9px',
                            'border-radius' => '8px',
                            'margin-top' => '28px'
                        ]
                    ]) ?>
                </div>
            </div>

        </div>

        <?php
        ActiveForm::end();
        ?>

    </div>

<?php endif; ?>