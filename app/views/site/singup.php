<?php

use app\models\forms\SingupForm;
use app\models\User;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var SingupForm $formRegistration
 * @var array $dataClients
 */

?>


<style>
    .select2-container--krajee .select2-selection {
        font-size: 20px;
        height: 45px;
        padding-left: 15px;
        padding-top: 7px;
        padding-bottom: 15px;
        border: 1px solid #4F4F4F;
        border-radius: 8px;
    }
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
    .select2-container--krajee .select2-selection__clear {
        top: 0.9rem;
    }
</style>


<?php $form = ActiveForm::begin([
    'id' => 'form_user_singup',
    'action' => Url::to(['/site/singup']),
    'options' => ['class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]);

    echo $form->field($formRegistration, 'clientId', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Организация, к которой будет привязан Ваш аккаунт *</div><div>{input}</div>'
    ])->widget(Select2::class, [
        'data' => $dataClients,
        'disabled' => true,  //Сделать поле неактивным
    ]);

    echo $form->field($formRegistration, 'clientId')->hiddenInput()->label(false);

    echo $form->field($formRegistration, 'role', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Проектная роль пользователя *</div><div>{input}</div>'
    ])->widget(Select2::class, [
        'data' => [User::ROLE_USER => 'Проектант', User::ROLE_ADMIN => 'Трекер', User::ROLE_MANAGER => 'Менеджер'],
        'disabled' => true,  //Сделать поле неактивным
    ]);

    echo $form->field($formRegistration, 'role')->hiddenInput()->label(false);

    echo $form->field($formRegistration, 'email', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Email *</div><div>{input}</div>'
    ])->textInput([
        'type' => 'email',
        'required' => true,
        'maxlength' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => '',
        'autocomplete' => 'off'
    ]);


    echo $form->field($formRegistration, 'password', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Пароль *</div><div>{input}</div>'
    ])->passwordInput([
        'maxlength' => 32,
        'minlength' => 6,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Введите от 6 до 32 символов',
        'autocomplete' => 'off'
    ]); ?>


    <div class="block-exist-agree">

        <?= $form->field($formRegistration, 'exist_agree', ['template' => '{input}{label}'])
            ->checkbox(['value' => 1, 'checked ' => true, 'class' => 'custom-checkbox'], false) ?>

        <?= Html::a('Я согласен с настоящей Политикой конфиденциальности и условиями обработки моих персональных данных',
            ['/site/confidentiality-policy'], [
                'target' => '_blank',
                'title' => 'Ознакомиться с настоящей Политикой конфиденциальности и условиями обработки моих персональных данных',
                'style' => ['color' => '#FFFFFF', 'line-height' => '18px']
            ]
        ) ?>

    </div>


    <div class="block-submit-registration">

        <?= Html::submitButton('Зарегистрировать меня', [
            'class' => 'btn btn-default',
            'name' => 'singup-button',
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