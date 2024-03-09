<?php

use app\models\ConfirmProblem;
use app\models\forms\FormUpdateConfirmProblem;
use app\models\Problems;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use app\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/**
 * @var ConfirmProblem $model
 * @var Problems $problem
 * @var FormUpdateConfirmProblem $formUpdateConfirmProblem
 * @var int $countContractorResponds
 */

?>


<?php if (!User::isUserSimple(Yii::$app->user->identity['username']) || $model->problem->getExistConfirm() !== StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

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
                <div class="col-md-12"><?= $problem->project->getPurposeProject() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Приветствие в начале встречи</div>
                <div class="col-md-12"><?= $problem->segment->confirm->getGreetingInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Информация о вас для респондентов</div>
                <div class="col-md-12"><?= $problem->segment->confirm->getViewInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
                <div class="col-md-12"><?= $problem->segment->confirm->getReasonInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Формулировка проблемы, которую проверяем</div>
                <div class="col-md-12"><?= $problem->getDescription() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Показатель прохождения теста</div>
                <div class="col-md-12">К = <?= $model->problem->getIndicatorPositivePassage() ?> %</div>
            </div>

            <div class="row">
                <div class="col-md-12">Вопросы для проверки гипотезы проблемы и ответы на них:</div>
                <div class="col-md-12"><?= $model->problem->getListExpectedResultsInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Потребность потребителя сегмента, которую проверяем</div>
                <div class="col-md-12"><?= $model->getNeedConsumer() ?></div>
            </div>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])): ?>
                <div class="row">
                    <div class="col-md-12">Количество респондентов, занятых исполнителями:
                        <span><?= $countContractorResponds ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">Количество респондентов (представителей сегмента):
                    <span><?= $model->getCountRespond() ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">Необходимое количество респондентов, подтверждающих проблему:
                    <span><?= $model->getCountPositive() ?></span>
                </div>
            </div>

        </div>

    </div>

<?php else: ?>

    <div class="container-fluid form-update-data-confirm">

        <div class="row row_header_data">

            <div class="col-sm-12 col-md-9" style="padding: 5px 0 0 0;">
                <?= Html::a('Исходные данные подтверждения' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-segment/get-instruction-step-one'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

        </div>

        <?php
        $form = ActiveForm::begin([
            'id' => 'update_data_confirm',
            'action' => Url::to(['/confirm-problem/update', 'id' => $model->getId()]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]);
        ?>

        <div class="container-fluid">

            <div class="content-view-data-confirm">

                <div class="row">
                    <div class="col-md-12">Цель проекта</div>
                    <div class="col-md-12"><?= $problem->project->getPurposeProject() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Приветствие в начале встречи</div>
                    <div class="col-md-12"><?= $problem->segment->confirm->getGreetingInterview() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Информация о вас для респондентов</div>
                    <div class="col-md-12"><?= $problem->segment->confirm->getViewInterview() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
                    <div class="col-md-12"><?= $problem->segment->confirm->getReasonInterview() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Формулировка проблемы, которую проверяем</div>
                    <div class="col-md-12"><?= $problem->getDescription() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Показатель прохождения теста</div>
                    <div class="col-md-12">К = <?= $model->problem->getIndicatorPositivePassage() ?> %</div>
                </div>

                <div class="row">
                    <div class="col-md-12">Вопросы для проверки гипотезы проблемы и ответы на них:</div>
                    <div class="col-md-12"><?= $model->problem->getListExpectedResultsInterview() ?></div>
                </div>

            </div>

            <div class="row desktop-pt-5 desktop-pb-5 mobile-mt-15">

                <?= $form->field($formUpdateConfirmProblem, 'need_consumer', [
                    'template' => '<div class="col-md-12 pl-20">{label}</div><div class="col-md-12">{input}</div>'
                ])->label('Какую потребность потребителя сегмента проверяем')
                    ->textarea([
                        'rows' => 1,
                        'maxlength' => true,
                        'placeholder' => '',
                        'required' => true,
                        'class' => 'style_form_field_respond form-control',
                    ])
                ?>

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

            <div class="row desktop-pt-5 desktop-pb-5">

                <?= $form->field($formUpdateConfirmProblem, 'count_respond', [
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

            <div class="row mobile-mt-15">

                <?= $form->field($formUpdateConfirmProblem, 'count_positive', [
                    'template' => '<div class="col-xs-12 col-sm-9 col-md-10 pl-20">{label}</div><div class="col-xs-12 col-sm-3 col-md-2">{input}</div>'
                ])->label('Необходимое количество респондентов, подтверждающих проблему')
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