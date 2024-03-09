<?php

use app\models\forms\FormClientCodeRegistration;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация';
$this->registerCssFile('@web/css/registration.css');

/**
 * @var FormClientCodeRegistration $formClientCode
 */

?>

<div class="row page-registration">

    <div class="col-md-3"></div>

    <div class="col-md-6 result-registration">

        <h2 class="text-center">Регистрация</h2>

        <div class="block-form-user-role">

            <?php $form = ActiveForm::begin([
                'id' => 'formClientCode',
                'options' => ['class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

                <?= $form->field($formClientCode, 'code', [
                    'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Введите код для регистрации *</div><div>{input}</div>'
                ])->textInput([
                    'id' => 'clientCodeInput',
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'autocomplete' => 'off'])
                    ->label(false) ?>

                <div class="block-submit-registration">

                    <?= Html::submitButton('Отправить', [
                        'class' => 'btn btn-default',
                        'id' => 'button-formClientCode',
                        'disabled' => true,
                        'style' => [
                            'margin-top' => '10px',
                            'background' => '#E0E0E0',
                            'color' => '4F4F4F',
                            'border-radius' => '8px',
                            'width' => '220px',
                            'height' => '40px',
                            'font-size' => '16px',
                            'font-weight' => '700'
                        ]
                    ]) ?>

                </div>

            <?php ActiveForm::end(); ?>

        </div>

        <div class="block-form-registration"></div>

    </div>

    <div class="col-md-3"></div>
    
</div>

<!--Модальные окна-->
<?= $this->render('registration_modal') ?>
<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/registration.js'); ?>
