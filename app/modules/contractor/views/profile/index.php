<?php

use app\models\forms\AvatarForm;
use app\models\forms\PasswordChangeForm;
use app\modules\contractor\models\form\ProfileContractorForm;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$this->title = 'Исполнитель | Профиль';
$this->registerCssFile('@web/css/profile-style.css');

/**
 * @var User $user
 * @var ProfileContractorForm $profile
 * @var PasswordChangeForm $passwordChangeForm
 * @var AvatarForm $avatarForm
 * @var array $contractorActivities
 */

?>


<style>
    .select2-container--krajee-bs3
    .select2-selection--multiple
    .select2-search--inline
    .select2-search__field {
        height: 38px;
    }
    .select2-container
    .select2-search--inline
    .select2-search__field {
        font-size: 16px;
    }
    .select2-container--krajee-bs3
    .select2-selection {
        border: 1px solid #828282;
    }
    .select2-container--krajee-bs3 .select2-selection--multiple .select2-search--inline .select2-search__field {
        height: 38px;
    }
    .select2-container .select2-search--inline .select2-search__field {
        font-size: 16px;
    }
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 20px;
        height: 45px;
        padding-left: 15px;
        padding-top: 7px;
        padding-bottom: 15px;
        border: 1px solid #4F4F4F;
        border-radius: 8px;
    }
    #contractor-activities-field {
        margin-bottom: 15px;
    }
    #contractor-activities-field .select2-container--krajee-bs3 .select2-selection {
        height: 55px;
    }
    @media screen and (max-width: 1140px) {
        #contractor-activities-field .select2-container--krajee-bs3 .select2-selection {
            font-size: 14px;
            height: 75px;
        }
        .select2-container--krajee-bs3 .select2-results__option[aria-selected] {
            font-size: 14px;
        }
    }
    .select2-container .select2-search--inline .select2-search__field {
        font-size: 20px;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
    .select2-container--krajee-bs3 .select2-selection__clear {
        top: 0.9rem;
    }
</style>


<div class="user-index">

    <div class="row profile_menu" style="height: 51px;">

    </div>


    <div class="data_user_content">

        <div class="col-md-12 col-lg-4">

            <?php if ($user->getAvatarImage()) : ?>

                <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'avatar_image']) ?>

                <?php if ($user->getId() === Yii::$app->user->getId()) : ?>

                    <div class="block_for_buttons_avatar_image">

                        <div class="container_link_button_avatar_image"><?= Html::a('Обновить фотографию', '#', ['class' => 'add_image link_button_avatar_image']) ?></div>

                        <div class="container_link_button_avatar_image"><?= Html::a('Редактировать миниатюру', '#', ['class' => 'update_image link_button_avatar_image']) ?></div>

                        <div class="container_link_button_avatar_image"><?= Html::a('Удалить фотографию', Url::to(['/contractor/profile/delete-avatar', 'id' => $avatarForm->getUserId()]), ['class' => 'delete_image link_button_avatar_image']) ?></div>

                    </div>

                <?php endif; ?>

            <?php else : ?>

                <?= Html::img('/images/avatar/default.jpg',['class' => 'avatar_image']) ?>

                <?php if ($user->getId() === Yii::$app->user->getId()) : ?>

                    <div class="block_for_buttons_avatar_image">

                        <div class="container_link_button_avatar_image">
                            <?= Html::a('Добавить фотографию', '#', ['class' => 'add_image link_button_avatar_image']) ?>
                        </div>

                    </div>

                <?php endif; ?>

            <?php endif; ?>


            <?php $form = ActiveForm::begin([
                'id' => 'formAvatarImage',
                'options' => ['enctype' => 'multipart/form-data', 'class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

            <?= $form->field($avatarForm, 'loadImage', ['template' => '<div style="display:none;">{input}</div>'])->fileInput(['id' => 'loadImageAvatar', 'accept' => 'image/x-png,image/jpeg']) ?>
            <?= $form->field($avatarForm, 'imageMax')->label(false)->hiddenInput() ?>

            <?php ActiveForm::end(); ?>

        </div>


        <div class="col-md-12 col-lg-8 info_user_content">

            <div class="row">

                <?php if ($user->getId() !== Yii::$app->user->getId()) : ?>

                    <div class="col-md-12">
                        <div class="user_is_online">
                            <?php if ($user->checkOnline === true) : ?>
                                Пользователь сейчас Online
                            <?php elseif(is_string($user->checkOnline)) : ?>
                                Пользователь был в сети <?= $user->checkOnline ?>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endif; ?>

                <div class="col-lg-4">
                    <div class="info_user_content_line_key">
                        Дата регистрации
                    </div>
                    <div class="info_user_content_line_value">
                        <?= date('d.m.Y', $user->getCreatedAt()) ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info_user_content_line_key">
                        Последнее изменение
                    </div>
                    <div class="info_user_content_line_value">
                        <?= date('d.m.Y', $user->getUpdatedAt()) ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info_user_content_line_key">
                        Статус:
                    </div>
                    <div class="info_user_content_line_value">
                        <?php if ($user->getStatus() === User::STATUS_ACTIVE) : ?>
                            Активирован
                        <?php elseif ($user->getStatus() === User::STATUS_NOT_ACTIVE) : ?>
                            Не активирован
                        <?php elseif ($user->getStatus() === User::STATUS_DELETED) : ?>
                            Заблокирован
                        <?php endif; ?>
                    </div>
                </div>

            </div>


            <?php if ($user->getId() !== Yii::$app->user->getId()) : ?>

                <div class="view_user_form row" style="padding-top: 15px;">

                    <?php $form = ActiveForm::begin([
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                    <div class="col-md-6">
                        <?= $form->field($user, 'email', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                        ]) ?>
                    </div>

                    <div class="col-md-6">
                        <?= $form->field($user, 'username', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorEducations[0], 'educational_institution', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'required' => true,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите наименование учебного заведения',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorEducations[0], 'faculty', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'required' => true,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите наименование факультета',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorEducations[0], 'course', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите на каком курсе вы учитесь (опционально)',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12 mb-15">
                        <div class="bolder pb-5 pl-10">Дата окончания</div>
                        <?= DatePicker::widget([
                            'type' => 2,
                            'removeButton' => false,
                            'name' => 'ContractorEducations[0][finish_date]',
                            'value' => empty($user->contractorEducations[0]->finish_date) ? null : date('d.m.Y', $user->contractorEducations[0]->finish_date),
                            'readonly' => true,
                            'disabled' => true,
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
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?php $user->contractorInfo->setActivities(explode('|', $user->contractorInfo->getActivities())); ?>
                        <?= $form->field($user->contractorInfo, 'activities', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->widget(Select2::class, [
                            'data' => $contractorActivities,
                            'options' => [
                                'id' => 'activities-contractor',
                                'placeholder' => 'Выберите виды деятельности',
                                'multiple' => true,
                                'required' => true,
                                'disabled' => true,
                                'class' => 'style_form_field_respond'
                            ],
                            'toggleAllSettings' => [
                                'selectLabel' => '<i class="fas fa-check-circle"></i> Выбрать все',
                                'unselectLabel' => '<i class="fas fa-times-circle"></i> Убрать все',
                                'selectOptions' => ['class' => 'text-success'],
                                'unselectOptions' => ['class' => 'text-danger'],
                            ],
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorInfo, 'academic_degree', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Кандидат экономических наук и т.д.',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorInfo, 'position', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'required' => true,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Должность в компании',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorInfo, 'publications', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textarea([
                            'row' => 2,
                            'maxlength' => true,
                            'minlength' => 2,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите наиболее значимые на ваш взгляд',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorInfo, 'implemented_projects', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textarea([
                            'row' => 2,
                            'maxlength' => true,
                            'minlength' => 2,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Дайте краткое описание с указанием компаний/проектов и достигнутых результатов',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($user->contractorInfo, 'role_in_implemented_projects', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textarea([
                            'row' => 2,
                            'maxlength' => true,
                            'minlength' => 2,
                            'readonly' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Комментарий о вашей роли в реализованных проектах',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>

            <?php else: ?>

                <div class="update_user_form row">

                    <?php $form = ActiveForm::begin([
                        'id' => 'update_data_profile',
                        'action' => Url::to(['/contractor/profile/update-profile', 'id' => $profile->getId()]),
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                    <div class="col-md-6">
                        <?= $form->field($profile, 'email', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'type' => 'email',
                            'maxlength' => true,
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-6">
                        <?= $form->field($profile, 'username', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'minlength' => 3,
                            'maxlength' => 32,
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Введите от 3 до 32 символов',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'educational_institution', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите наименование учебного заведения',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'faculty', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите наименование факультета',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'course', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите на каком курсе вы учитесь (опционально)',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12 mb-15">
                        <div class="bolder pb-5 pl-10">Дата окончания</div>
                        <?= DatePicker::widget([
                            'type' => 2,
                            'removeButton' => false,
                            'name' => 'ProfileContractorForm[finish_date]',
                            'value' => empty($profile->finish_date) ? null : date('d.m.Y', $profile->finish_date),
                            'readonly' => true,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'dd.mm.yyyy',
                            ],
                            'options' => [
                                'id' => "finish_date_update",
                                'class' => 'text-center style_form_field_respond form-control',
                                'style' => ['padding-right' => '20px'],
                                'placeholder' => 'Выберите дату',
                            ]
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'activities', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->widget(Select2::class, [
                            'data' => $contractorActivities,
                            'options' => [
                                'id' => 'activities-contractor-update',
                                'placeholder' => 'Выберите виды деятельности',
                                'multiple' => true,
                                'required' => true,
                                'disabled' => true,
                                'class' => 'style_form_field_respond'
                            ],
                            'toggleAllSettings' => [
                                'selectLabel' => '<i class="fas fa-check-circle"></i> Выбрать все',
                                'unselectLabel' => '<i class="fas fa-times-circle"></i> Убрать все',
                                'selectOptions' => ['class' => 'text-success'],
                                'unselectOptions' => ['class' => 'text-danger'],
                            ],
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'academic_degree', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Кандидат экономических наук и т.д.',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'position', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textInput([
                            'maxlength' => true,
                            'minlength' => 2,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Должность в компании',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'publications', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textarea([
                            'row' => 2,
                            'maxlength' => true,
                            'minlength' => 2,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Укажите наиболее значимые на ваш взгляд',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'implemented_projects', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textarea([
                            'row' => 2,
                            'maxlength' => true,
                            'minlength' => 2,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Дайте краткое описание с указанием компаний/проектов и достигнутых результатов',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-md-12">
                        <?= $form->field($profile, 'role_in_implemented_projects', [
                            'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                        ])->textarea([
                            'row' => 2,
                            'maxlength' => true,
                            'minlength' => 2,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Комментарий о вашей роли в реализованных проектах',
                            'autocomplete' => 'off'
                        ]) ?>
                    </div>

                    <div class="col-xs-6">
                        <?= Html::button( 'Изменить пароль',[
                            'id' => 'show_form_change_password',
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#707F99',
                                'width' => '100%',
                                'height' => '40px',
                                'color' => '#FFFFFF',
                                'font-size' => '18px',
                                'text-transform' => 'uppercase',
                                'border-radius' => '8px',
                                'margin-top' => '35px',
                            ],
                        ]) ?>
                    </div>

                    <div class="col-xs-6">
                        <?= Html::submitButton( 'Сохранить',[
                            'class' => 'btn btn-success',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#52BE7F',
                                'width' => '100%',
                                'height' => '40px',
                                'font-size' => '24px',
                                'border-radius' => '8px',
                                'margin-top' => '35px',
                            ],
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>

                <div class="change_password_content">

                    <div class="row change_password_content_data_user">

                        <div class="col-lg-4">
                            <div class="info_user_content_line_key">
                                Логин
                            </div>
                            <div class="info_user_content_line_value">
                                <?= $user->getUsername() ?>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="info_user_content_line_key">
                                E-mail
                            </div>
                            <div class="info_user_content_line_value">
                                <?= $user->getEmail() ?>
                            </div>
                        </div>

                    </div>

                    <div class="row change_password_form">

                        <?php $form = ActiveForm::begin([
                            'id' => 'form_change_password_user',
                            'action' => Url::to(['/contractor/profile/change-password', 'id' => $user->getId()]),
                            'options' => ['class' => 'g-py-15'],
                            'errorCssClass' => 'u-has-error-v1',
                            'successCssClass' => 'u-has-success-v1-1',
                        ]); ?>

                        <div class="col-md-4">
                            <?= $form->field($passwordChangeForm, 'currentPassword', [
                                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                            ])->passwordInput([
                                'maxlength' => 32,
                                'minlength' => 6,
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Введите от 6 до 32 символов',
                                'autocomplete' => 'off'
                            ]) ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($passwordChangeForm, 'newPassword', [
                                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                            ])->passwordInput([
                                'maxlength' => 32,
                                'minlength' => 6,
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Введите от 6 до 32 символов',
                                'autocomplete' => 'off'
                            ]) ?>
                        </div>

                        <div class="col-md-4">
                            <?= $form->field($passwordChangeForm, 'newPasswordRepeat', [
                                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
                            ])->passwordInput([
                                'maxlength' => 32,
                                'minlength' => 6,
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Введите от 6 до 32 символов',
                                'autocomplete' => 'off'
                            ]) ?>
                        </div>

                        <div class="col-md-12" style="padding-top: 27px; padding-bottom: 10px;">
                            Введите последовательно данные в указаные поля. Во время создания паролей не используйте пробел.
                        </div>

                        <div class="col-xs-6">
                            <?= Html::button('Отмена', [
                                'class' => 'show_form_update_profile btn btn-default',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#707F99',
                                    'width' => '100%',
                                    'height' => '40px',
                                    'color' => '#FFFFFF',
                                    'font-size' => '18px',
                                    'text-transform' => 'uppercase',
                                    'border-radius' => '8px',
                                    'margin-top' => '35px',
                                ],
                            ])?>
                        </div>

                        <div class="col-xs-6">
                            <?= Html::submitButton( 'Сохранить',[
                                'class' => 'btn btn-success',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#52BE7F',
                                    'width' => '100%',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                    'margin-top' => '35px',
                                ],
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <!--Модальные окна-->
    <?= $this->render('modal') ?>

</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/profile_contractor_index.js'); ?>
