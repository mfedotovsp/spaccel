<?php

use app\models\ContractorEducations;
use app\models\forms\SingupContractorForm;
use app\models\User;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;

/**
 * @var SingupContractorForm $formRegistration
 * @var array $dataClients
 * @var array $contractorActivities
 * @var ContractorEducations $education
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
    #exist_experience_checkbox {
        margin-top: 30px;
        margin-bottom: 30px;
    }
    #exist_experience_checkbox input[type="checkbox"].custom-checkbox + label {
        font-size: 16px;
    }
    #contractor-activities-field {
        margin-bottom: 15px;
    }
    #contractor-activities-field .select2-container--krajee .select2-selection {
        height: 55px;
    }
    @media screen and (max-width: 1140px) {
        #contractor-activities-field .select2-container--krajee .select2-selection {
            font-size: 14px;
            height: 75px;
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
        'data' => [User::ROLE_CONTRACTOR => 'Исполнитель'],
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

    ?>

    <div class="" style="border-bottom: 1px solid #cccccc; border-top: 1px solid #cccccc; padding-top: 20px; padding-bottom: 30px; margin-bottom: 20px; margin-top: 30px;">

        <?php

        echo $form->field($education, "[0]educational_institution", [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Учебное заведение *</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'id' => 'educational_institution-0',
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]);

        echo $form->field($education, "[0]faculty", [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Факультет *</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'required' => true,
            'id' => 'faculty-0',
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]);

        echo $form->field($education, "[0]course", [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Курс</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'id' => 'course-0',
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
            'autocomplete' => 'off'
        ]);

        echo '<div style="padding-left: 15px; padding-bottom: 5px;">Дата окончания</div>';
        echo DatePicker::widget([
            'type' => 2,
            'removeButton' => false,
            'name' => 'ContractorEducations[0][finish_date]',
            'value' => empty($education->finish_date) ? null : $education->finish_date,
            'readonly' => true,
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'dd.mm.yyyy',
            ],
            'options' => [
                'id' => "finish_date",
                'class' => 'text-center style_form_field_respond form-control',
                'style' => ['padding-right' => '20px'],
                'placeholder' => 'Выберите дату',
            ]
        ]);

        ?>

    </div>

    <?php

    echo $form->field($formRegistration, 'activities', [
        'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Вид деятельности *</div><div>{input}</div>',
        'options' => ['id' => 'contractor-activities-field']
    ])->widget(Select2::class, [
        'data' => $contractorActivities,
        'options' => [
            'id' => 'contractor-activities',
            'placeholder' => 'Выберите вид деятельности',
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

    echo $form->field($formRegistration, 'exist_experience', [
        'template' => '{input}{label}',
        'options' => ['id' => 'exist_experience_checkbox']])
        ->checkbox([
            'value' => 1,
            'checked ' => false,
            'class' => 'custom-checkbox'
        ], false);

    ?>

    <div class="block-for-experience">

        <?php

        echo $form->field($formRegistration, 'academic_degree', [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Ученая степень, звание</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'minlength' => 2,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Кандидат экономических наук и т.д.',
            'autocomplete' => 'off'
        ]);


        echo $form->field($formRegistration, 'position', [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Должность</div><div>{input}</div>'
        ])->textInput([
            'maxlength' => true,
            'minlength' => 2,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Должность в компании',
            'autocomplete' => 'off'
        ]);


        echo $form->field($formRegistration, 'publications', [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Научные публикации</div><div>{input}</div>'
        ])->textarea([
            'row' => 2,
            'maxlength' => true,
            'minlength' => 2,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Укажите наиболее значимые на ваш взгляд',
            'autocomplete' => 'off'
        ]);


        echo $form->field($formRegistration, 'implemented_projects', [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Реализованные проекты</div><div>{input}</div>'
        ])->textarea([
            'row' => 2,
            'maxlength' => true,
            'minlength' => 2,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Дайте краткое описание с указанием компаний/проектов и достигнутых результатов',
            'autocomplete' => 'off'
        ]);


        echo $form->field($formRegistration, 'role_in_implemented_projects', [
            'template' => '<div style="padding-left: 15px; padding-bottom: 5px;">Роль в реализованных проектах</div><div>{input}</div>'
        ])->textarea([
            'row' => 2,
            'maxlength' => true,
            'minlength' => 2,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => 'Комментарий о вашей роли в реализованных проектах',
            'autocomplete' => 'off'
        ]); ?>

    </div>

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


