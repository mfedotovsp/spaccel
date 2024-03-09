<?php

use app\models\Client;
use app\models\ClientCodes;
use app\models\ClientCodeTypes;
use app\modules\admin\models\form\EmptyForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Настройки доступа';
$this->registerCssFile('@web/css/setting-codes-style.css');

/**
 * @var Client $client
 * @var ClientCodes[] $clientCodes
 * @var ClientCodes|null $codeRegistrationForSimpleUser
 * @var ClientCodes|null $codeRegistrationForTracker
 * @var ClientCodes|null $codeRegistrationForManager
 * @var ClientCodes|null $codeRegistrationForExpert
 * @var ClientCodes|null $codeRegistrationForContractor
*/
?>

<div class="admin-setting-codes-index">

    <div class="row row-header">
        <div class="col-md-12">
            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>
    </div>

    <div class="row container-fluid">

        <div class="col-md-12 headerContainerClientCodes">Настройка доступа для регистрации пользователей по ролям:</div>

        <div class="col-md-12 containerClientCodes">

            <?php $form = ActiveForm::begin([
                'id' => 'formCodeRegistration',
                'options' => ['class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

            <div class="row rowClientCode">
                <div class="col-md-4">
                    Код для регистрации с ролью "Проектант"
                </div>
                <div class="col-md-4">

                    <?php if (!$codeRegistrationForSimpleUser): ?>
                        <?= $form->field(new EmptyForm(), 'value', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php else: ?>
                        <?= $form->field($codeRegistrationForSimpleUser, 'code', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-4">
                    <?= Html::a( 'Сгенерировать новый код доступа',
                        Url::to(['/admin/setting-codes/generation', 'type' => ClientCodeTypes::REGISTRATION_CODE_FOR_SIMPLE_USER]),[
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'border-radius' => '8px',
                            ],
                        ]) ?>
                </div>
            </div>

            <div class="row rowClientCode">
                <div class="col-md-4">
                    Код для регистрации с ролью "Трекер"
                </div>
                <div class="col-md-4">

                    <?php if (!$codeRegistrationForTracker): ?>
                        <?= $form->field(new EmptyForm(), 'value', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php else: ?>
                        <?= $form->field($codeRegistrationForTracker, 'code', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-4">
                    <?= Html::a( 'Сгенерировать новый код доступа',
                        Url::to(['/admin/setting-codes/generation', 'type' => ClientCodeTypes::REGISTRATION_CODE_FOR_TRACKER]),[
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'border-radius' => '8px',
                            ],
                        ]) ?>
                </div>
            </div>

            <div class="row rowClientCode">
                <div class="col-md-4">
                    Код для регистрации с ролью "Менеджер организации"
                </div>
                <div class="col-md-4">

                    <?php if (!$codeRegistrationForManager): ?>
                        <?= $form->field(new EmptyForm(), 'value', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php else: ?>
                        <?= $form->field($codeRegistrationForManager, 'code', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-4">
                    <?= Html::a( 'Сгенерировать новый код доступа',
                        Url::to(['/admin/setting-codes/generation', 'type' => ClientCodeTypes::REGISTRATION_CODE_FOR_MANAGER]),[
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'border-radius' => '8px',
                            ],
                        ]) ?>
                </div>
            </div>

            <div class="row rowClientCode">
                <div class="col-md-4">
                    Код для регистрации с ролью "Эксперт"
                </div>
                <div class="col-md-4">

                    <?php if (!$codeRegistrationForExpert): ?>
                        <?= $form->field(new EmptyForm(), 'value', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php else: ?>
                        <?= $form->field($codeRegistrationForExpert, 'code', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-4">
                    <?= Html::a( 'Сгенерировать новый код доступа',
                        Url::to(['/admin/setting-codes/generation', 'type' => ClientCodeTypes::REGISTRATION_CODE_FOR_EXPERT]),[
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'border-radius' => '8px',
                            ],
                        ]) ?>
                </div>
            </div>

            <div class="row rowClientCode">
                <div class="col-md-4">
                    Код для регистрации с ролью "Исполнитель"
                </div>
                <div class="col-md-4">

                    <?php if (!$codeRegistrationForContractor): ?>
                        <?= $form->field(new EmptyForm(), 'value', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php else: ?>
                        <?= $form->field($codeRegistrationForContractor, 'code', ['template' => '{input}'])->textInput(['disabled' => true, 'class' => 'form-control field-setting-code']) ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-4">
                    <?= Html::a( 'Сгенерировать новый код доступа',
                        Url::to(['/admin/setting-codes/generation', 'type' => ClientCodeTypes::REGISTRATION_CODE_FOR_CONTRACTOR]),[
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'border-radius' => '8px',
                            ],
                        ]) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
