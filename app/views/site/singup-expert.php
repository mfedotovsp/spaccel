<?php

use app\models\forms\SingupExpertForm;
use app\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use app\models\ExpertType;

/**
 * @var SingupExpertForm $formRegistration
 * @var array $dataClients
 */

?>

<style>
    .select2-container--krajee .select2-selection--multiple .select2-search--inline .select2-search__field {
        height: 38px;
    }
    .select2-container .select2-search--inline .select2-search__field {
        font-size: 16px;
    }
    .select2-container--krajee .select2-selection {
        font-size: 20px;
        height: 45px;
        padding-left: 15px;
        padding-top: 7px;
        padding-bottom: 15px;
        border: 1px solid #4F4F4F;
        border-radius: 8px;
    }
    #type-expert-field {
        margin-bottom: 15px;
    }
    #type-expert-field .select2-container--krajee .select2-selection {
        font-size: 20px;
        height: 125px;
        padding-left: 5px;
        padding-top: 7px;
        padding-bottom: 15px;
        border: 1px solid #4F4F4F;
        border-radius: 8px;
    }
    @media screen and (max-width: 1140px) {
        #type-expert-field .select2-container--krajee .select2-selection {
            font-size: 14px;
            height: 160px;
        }
        .select2-container--krajee .select2-results__option[aria-selected] {
            font-size: 14px;
        }
    }
    .select2-container .select2-search--inline .select2-search__field {
        font-size: 20px;
    }
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
    .select2-container--krajee .select2-selection__clear {
        top: 0.9rem;
    }
</style>


<?php

$form = ActiveForm::begin([
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
        'data' => [User::ROLE_EXPERT => 'Эксперт'],
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
    ]);


    echo $form->field($formRegistration, 'education', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Образование *</div><div>{input}</div>'
    ])->textInput([
        'maxlength' => true,
        'minlength' => 2,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Укажите наименование ВУЗа(ов)',
        'autocomplete' => 'off'
    ]);


    echo $form->field($formRegistration, 'academic_degree', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Ученая степень, звание *</div><div>{input}</div>'
    ])->textInput([
        'maxlength' => true,
        'minlength' => 2,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Кандидат экономических наук и т.д.',
        'autocomplete' => 'off'
    ]);


    echo $form->field($formRegistration, 'position', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Должность *</div><div>{input}</div>'
    ])->textInput([
        'maxlength' => true,
        'minlength' => 2,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Должность в компании',
        'autocomplete' => 'off'
    ]);


    echo $form->field($formRegistration, 'type', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Тип экпертной деятельности *</div><div>{input}</div>',
        'options' => ['id' => 'type-expert-field']
    ])->widget(Select2::class, [
        'data' => ExpertType::getListTypes(),
        'options' => [
            'id' => 'type-expert',
            'placeholder' => 'Выберите тип экпертной деятельности',
            'multiple' => true,
            'required' => true
        ],
        'toggleAllSettings' => [
            'selectLabel' => '<i class="fas fa-check-circle"></i> Выбрать все',
            'unselectLabel' => '<i class="fas fa-times-circle"></i> Убрать все',
            'selectOptions' => ['class' => 'text-success'],
            'unselectOptions' => ['class' => 'text-danger'],
        ],
    ]);


    echo $form->field($formRegistration, 'scope_professional_competence', [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Сфера профессиональной компетенции *</div><div>{input}</div>'
        ])->textarea([
            'row' => 2,
            'maxlength' => true,
            'minlength' => 2,
            'required' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Область(и) ваших знаний для оказания экспертных услуг',
            'autocomplete' => 'off'
        ]);


    echo $form->field($formRegistration, 'publications', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Научные публикации *</div><div>{input}</div>'
    ])->textarea([
        'row' => 2,
        'maxlength' => true,
        'minlength' => 2,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Укажите наиболее значимые на ваш взгляд',
        'autocomplete' => 'off'
    ]);


    echo $form->field($formRegistration, 'implemented_projects', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Реализованные проекты *</div><div>{input}</div>'
    ])->textarea([
        'row' => 2,
        'maxlength' => true,
        'minlength' => 2,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Дайте краткое описание с указанием компаний/проектов и достигнутых результатов',
        'autocomplete' => 'off'
    ]);


    echo $form->field($formRegistration, 'role_in_implemented_projects', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Роль в реализованных проектах *</div><div>{input}</div>'
    ])->textarea([
        'row' => 2,
        'maxlength' => true,
        'minlength' => 2,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Комментарий о вашей роли в реализованных проектах',
        'autocomplete' => 'off'
    ]);


    echo $form->field($formRegistration, 'keywords', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Ключевые слова *</div><div>{input}</div>'
    ])->textarea([
        'row' => 2,
        'maxlength' => true,
        'required' => true,
        'class' => 'style_form_field_respond form-control',
        'placeholder' => 'Укажите ключевые слова или словосочетания, отражающие Ваши научные интересы. Желательно указывать ключевые слова как на русском, так и на английском языке',
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


