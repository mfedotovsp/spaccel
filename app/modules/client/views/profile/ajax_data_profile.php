<?php

use app\models\forms\AvatarForm;
use app\models\forms\PasswordChangeForm;
use app\models\forms\ProfileForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use yii\widgets\ActiveForm;

/**
 * @var User $user
 * @var int $count_users
 * @var int $countProjects
 * @var ProfileForm $profile
 * @var PasswordChangeForm $passwordChangeForm
 * @var AvatarForm $avatarForm
 */

?>

<div class="col-md-12 col-lg-4">

    <?php if ($user->getAvatarImage()) : ?>

        <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'avatar_image']) ?>

        <?php if ($user->getId() === Yii::$app->user->getId()) : ?>

            <div class="block_for_buttons_avatar_image">

                <div class="container_link_button_avatar_image">
                    <?= Html::a('Обновить фотографию', '#', ['class' => 'add_image link_button_avatar_image']) ?>
                </div>

                <div class="container_link_button_avatar_image">
                    <?= Html::a('Редактировать миниатюру', '#', ['class' => 'update_image link_button_avatar_image']) ?>
                </div>

                <div class="container_link_button_avatar_image">
                    <?= Html::a('Удалить фотографию', Url::to(['/client/profile/delete-avatar', 'id' => $avatarForm->getUserId()]), ['class' => 'delete_image link_button_avatar_image']) ?>
                </div>

            </div>

        <?php endif; ?>

    <?php else : ?>

        <?= Html::img('/images/avatar/default.jpg',['class' => 'avatar_image']) ?>

        <?php if ($user->getId() === Yii::$app->user->getId()) : ?>

            <div class="block_for_buttons_avatar_image">

                <div class="container_link_button_avatar_image"><?= Html::a('Добавить фотографию', '#', ['class' => 'add_image link_button_avatar_image']) ?></div>

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

    <div class="view_user_form row">

        <?php if ($user->getId() !== Yii::$app->user->getId()) : ?>

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

            <?php ActiveForm::end(); ?>

        <?php endif; ?>

        <?php if (User::isUserAdminCompany(Yii::$app->user->identity['username'])) :?>

            <?php if ($count_users > 0) : ?>

                <div class="col-md-4 text-center" style="margin-top: 37px; font-weight: 700; font-size: 24px;">Администрирование:</div>

                <div class="col-md-4">
                    <?= Html::a('<div class="text-center">Пользователи - ' . $count_users . '</div>',
                        Url::to(['/client/users/group', 'id' => $user['id']]), [
                            'class' => 'btn btn-default',
                            'style' => [
                                'color' => '#FFFFFF',
                                'background' => '#707F99',
                                'padding' => '0 7px',
                                'width' => '100%',
                                'height' => '40px',
                                'font-size' => '24px',
                                'border-radius' => '8px',
                                'margin-top' => '35px',
                            ]
                        ])?>
                </div>

                <div class="col-md-4">
                    <?= Html::a( '<div class="text-center">Проекты - ' . $countProjects . '</div>',
                        Url::to(['/client/projects/group', 'id' => $user['id']]), [
                            'class' => 'btn btn-default',
                            'style' => [
                                'color' => '#FFFFFF',
                                'background' => '#707F99',
                                'padding' => '0 7px',
                                'width' => '100%',
                                'height' => '40px',
                                'font-size' => '24px',
                                'border-radius' => '8px',
                                'margin-top' => '35px',
                            ]
                        ]) ?>
                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>

    <?php if ($user->getId() === Yii::$app->user->getId()) : ?>
        <div class="update_user_form row">

            <?php $form = ActiveForm::begin([
                'id' => 'update_data_profile',
                'action' => Url::to(['/client/profile/update-profile', 'id' => $profile->getId()]),
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
                    'action' => Url::to(['/client/profile/change-password', 'id' => $user->getId()]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <div class="col-md-4">
                    <?= $form->field($passwordChangeForm, 'currentPassword', [
                        'template' => '<div class="font-label">Актуальный пароль</div><div>{input}</div>'
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
                        'template' => '<div class="font-label">Новый пароль</div><div>{input}</div>'
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
                        'template' => '<div class="font-label">Повторите новый пароль</div><div>{input}</div>'
                    ])->passwordInput([
                        'maxlength' => 32,
                        'minlength' => 6,
                        'required' => true,
                        'class' => 'style_form_field_respond form-control',
                        'placeholder' => 'Введите от 6 до 32 символов',
                        'autocomplete' => 'off'
                    ]) ?>
                </div>

                <div class="col-xs-12" style="padding-top: 27px; padding-bottom: 10px;">
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
                            'font-size' => '18px',
                            'text-transform' => 'uppercase',
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
