<?php

use app\models\ConfirmGcp;
use app\models\forms\FormUpdateConfirmGcp;
use app\models\Gcps;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\User;

/**
 * @var ConfirmGcp $model
 * @var Gcps $gcp
 * @var FormUpdateConfirmGcp $formUpdateConfirmGcp
 * @var int $countContractorResponds
 */

?>


<?php if (!User::isUserSimple(Yii::$app->user->identity['username']) || $model->gcp->getExistConfirm() !== StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

    <div class="container-fluid form-view-data-confirm">

        <div class="row row_header_data">

            <div class="col-md-12" style="padding: 5px 0 0 0;">
                <?= Html::a('Исходные данные подтверждения' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-gcp/get-instruction-step-one'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

        </div>

        <div class="container-fluid content-view-data-confirm">

            <div class="row">
                <div class="col-md-12">Цель проекта</div>
                <div class="col-md-12"><?= $gcp->project->getPurposeProject() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Приветствие в начале встречи</div>
                <div class="col-md-12"><?= $gcp->segment->confirm->getGreetingInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Информация о вас для респондентов</div>
                <div class="col-md-12"><?= $gcp->segment->confirm->getViewInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
                <div class="col-md-12"><?= $gcp->segment->confirm->getReasonInterview() ?></div>
            </div>

            <div class="row">
                <div class="col-md-12">Формулировка ценностного предложения, которое проверяем</div>
                <div class="col-md-12"><?= $gcp->getDescription() ?></div>
            </div>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])): ?>
                <div class="row">
                    <div class="col-md-12">Количество респондентов, занятых исполнителями:
                        <span><?= $countContractorResponds ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">Количество респондентов, подтвердивших проблему:
                    <span><?= $model->getCountRespond() ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">Необходимое количество респондентов, подтверждающих ценностное предложение:
                    <span><?= $model->getCountPositive() ?></span>
                </div>
            </div>

        </div>

    </div>

<?php else: ?>

    <div class="container-fluid form-update-data-confirm">

        <?php
        $form = ActiveForm::begin([
            'id' => 'update_data_confirm',
            'action' => Url::to(['/confirm-gcp/update', 'id' => $formUpdateConfirmGcp->getId()]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]);
        ?>

        <div class="row row_header_data">

            <div class="col-md-12" style="padding: 5px 0 0 0;">
                <?= Html::a('Исходные данные подтверждения' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-gcp/get-instruction-step-one'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

        </div>

        <div class="container-fluid">

            <div class="content-view-data-confirm">

                <div class="row">
                    <div class="col-md-12">Цель проекта</div>
                    <div class="col-md-12"><?= $gcp->project->getPurposeProject() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Приветствие в начале встречи</div>
                    <div class="col-md-12"><?= $gcp->segment->confirm->getGreetingInterview() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Информация о вас для респондентов</div>
                    <div class="col-md-12"><?= $gcp->segment->confirm->getViewInterview() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
                    <div class="col-md-12"><?= $gcp->segment->confirm->getReasonInterview() ?></div>
                </div>

                <div class="row">
                    <div class="col-md-12">Формулировка ценностного предложения, которое проверяем</div>
                    <div class="col-md-12"><?= $gcp->getDescription() ?></div>
                </div>

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

            <div class="row desktop-pt-5 desktop-pb-5 mt-15">

                <?= $form->field($formUpdateConfirmGcp, 'count_respond', [
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

                <?= $form->field($formUpdateConfirmGcp, 'count_positive', [
                    'template' => '<div class="col-xs-12 col-sm-9 col-md-10 pl-20">{label}</div><div class="col-xs-12 col-sm-3 col-md-2">{input}</div>'
                ])->label('Необходимое количество респондентов, подтверждающих ценностное предложение')
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