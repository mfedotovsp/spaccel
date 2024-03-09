<?php

use app\models\Client;
use app\models\ClientActivation;
use app\models\ClientRatesPlan;
use app\models\ClientSettings;
use app\modules\client\models\form\AvatarCompanyForm;
use app\modules\client\models\form\FormUpdateClient;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var Client $client
 * @var FormUpdateClient $model
 * @var AvatarCompanyForm $avatarForm
 */

$selection_list = [ClientSettings::ACCESS_ADMIN_TRUE => 'Доступ разрешен', ClientSettings::ACCESS_ADMIN_FALSE => 'Доступ запрещен'];

?>

<div class="col-md-12 col-lg-4">

    <?php if ($client->settings->getAvatarImage()) : ?>

        <?= Html::img('@web/upload/company-'.$client->getId().'/avatar/'.$client->settings->getAvatarImage(), ['class' => 'avatar_image']) ?>

        <div class="block_for_buttons_avatar_image">

            <div class="container_link_button_avatar_image">
                <?= Html::a('Обновить фотографию', '#', ['class' => 'add_image link_button_avatar_image']) ?>
            </div>
            <div class="container_link_button_avatar_image">
                <?= Html::a('Редактировать миниатюру', '#', ['class' => 'update_image link_button_avatar_image']) ?>
            </div>
            <div class="container_link_button_avatar_image">
                <?= Html::a('Удалить фотографию', Url::to(['/client/client-profile/delete-avatar']), ['class' => 'delete_image link_button_avatar_image']) ?>
            </div>

        </div>

    <?php else : ?>

        <?= Html::img('/images/avatar/default.jpg',['class' => 'avatar_image']) ?>

        <div class="block_for_buttons_avatar_image">
            <div class="container_link_button_avatar_image">
                <?= Html::a('Добавить фотографию', '#', ['class' => 'add_image link_button_avatar_image']) ?>
            </div>
        </div>

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


<div class="col-md-12 col-lg-8 info_client_content">

    <div class="row">

        <div class="col-lg-12"><label style="padding-left: 10px;">Дата регистрации в Spaccel:</label><span style="padding-left: 10px;"><?= date('d.m.Y', $client->getCreatedAt()) ?></span></div>

        <div class="col-lg-12"><label style="padding-left: 10px;">Статус:</label>

            <?php
            /** @var ClientActivation $clientActivation */
            $clientActivation = $client->findClientActivation();
            if ($clientActivation->getStatus() === ClientActivation::ACTIVE) : ?>
                <span style="padding-left: 10px;">Активирована</span>
            <?php elseif ($clientActivation->getStatus() === ClientActivation::NO_ACTIVE) : ?>
                <span style="padding-left: 10px;">Заблокирована</span>
            <?php endif; ?>

        </div>

        <?php
        /** @var ClientRatesPlan  $clientRatesPlan */
        if ($clientRatesPlan = $client->findLastClientRatesPlan()) : ?>
            <div class="col-lg-12">
                <label style="padding-left: 10px;">Сведения о тарифе:</label>
                <div style="padding-left: 10px;">Наименование: <u><?= $clientRatesPlan->ratesPlan->getName() ?></u></div>
                <div style="padding-left: 10px;">Описание: <u><?= $clientRatesPlan->ratesPlan->getDescription() ?></u></div>
                <div style="padding-left: 10px;">Максимальное количество проектантов: <u><?= $clientRatesPlan->ratesPlan->getMaxCountProjectUser() ?></u></div>
                <div style="padding-left: 10px;">Максимальное количество трекеров: <u><?= $clientRatesPlan->ratesPlan->getMaxCountTracker() ?></u></div>
                <div style="padding-left: 10px;">Начало действия тарифа: <u><?= date('d.m.Y', $clientRatesPlan->getDateStart()) ?></u></div>
                <div style="padding-left: 10px;">Окончание действия тарифа: <u><?= date('d.m.Y', $clientRatesPlan->getDateEnd()) ?></u></div>
            </div>
        <?php else : ?>
            <div class="col-lg-12">
                <label style="padding-left: 10px;">Сведения о тарифе:</label>
                <div style="padding-left: 10px;">
                    Тариф не установлен
                </div>
            </div>
        <?php endif; ?>

        <div class="col-lg-12 mt-15">
            <div>
                <span class="pl-10">Разрешен доступ к общим спискам запросов B2B компаний:</span>
                <span class="pl-5"><?php $accessGeneralWishList = $client->isAccessGeneralWishList() ? 'Да' : 'Нет' ?><?= $accessGeneralWishList ?></span>
            </div>
            <div>
                <span class="pl-10">Разрешен доступ к вашим спискам запросов B2B компаний:</span>
                <span class="pl-5"><?php $accessMyWishList = $client->isAccessMyWishList() ? 'Да' : 'Нет' ?><?= $accessMyWishList ?></span>
            </div>
        </div>

    </div>

    <div class="view_client_form row">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <div class="col-md-12">
            <?= $form->field($model, 'name', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'readonly' => true,
                'class' => 'style_form_field_respond form-control',
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'fullname', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'readonly' => true,
                'class' => 'style_form_field_respond form-control',
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'city', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'readonly' => true,
                'class' => 'style_form_field_respond form-control',
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'description', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textarea([
                'rows' => 2,
                'maxlength' => true,
                'readonly' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>
        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'accessAdmin', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => $selection_list,
                'options' => ['id' => 'selectViewAccessAdmin'],
                'disabled' => true,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>
        </div>

        <div class="col-xs-12 col-md-6">
            <?= Html::button('Редактировать', [
                'id' => 'show_form_update_data',
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

        <?php ActiveForm::end(); ?>

    </div>

    <div class="update_client_form row">

        <?php $form = ActiveForm::begin([
            'id' => 'update_data_profile',
            'action' => Url::to(['/client/client-profile/update-profile', 'id' => $model->getId()]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <div class="col-md-12">
            <?= $form->field($model, 'name', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'fullname', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'city', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textInput([
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'description', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>'
            ])->textarea([
                'rows' => 2,
                'maxlength' => true,
                'class' => 'style_form_field_respond form-control',
                'placeholder' => '',
            ]) ?>
        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'accessAdmin', [
                'template' => '<div style="padding-left: 10px;">{label}</div><div>{input}</div>',
            ])->widget(Select2::class, [
                'data' => $selection_list,
                'options' => ['id' => 'selectAccessAdmin'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= Html::button('Отмена', [
                'id' => 'show_form_view_data',
                'class' => 'btn btn-default',
                'style' => [
                    'background' => '#E0E0E0',
                    'padding' => '0 7px',
                    'width' => '100%',
                    'height' => '40px',
                    'font-size' => '24px',
                    'border-radius' => '8px',
                    'margin-top' => '35px',
                ]
            ]) ?>
        </div>

        <div class="col-md-6">
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
