<?php

$this->title = 'Админка | Профиль организации';
$this->registerCssFile('@web/css/profile-style.css');

use app\models\Client;
use app\models\ClientActivation;
use app\models\ClientRatesPlan;
use app\models\ClientSettings;
use app\modules\admin\models\form\FormChangeAccessWishList;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var Client $client
 * @var ClientSettings $clientSettings
 * @var FormChangeAccessWishList $formChangeAccessWishList
 */

?>


<div class="client-profile">

    <div class="row profile_menu" style="height: 51px;">

    </div>

    <div class="data_client_content">

        <div class="col-md-12 col-lg-4">

            <?php if ($clientSettings->getAvatarImage()) : ?>
                <?= Html::img('@web/upload/company-'.$client->getId().'/avatar/'.$clientSettings->getAvatarImage(), ['class' => 'avatar_image']) ?>
            <?php else : ?>
                <?= Html::img('/images/avatar/default.jpg',['class' => 'avatar_image']) ?>
            <?php endif; ?>

        </div>
        
        <div class="col-md-12 col-lg-8 info_client_content">

            <div class="row">

                <div class="col-lg-4"><label style="padding-left: 10px;">Дата регистрации в Spaccel:</label><span style="padding-left: 10px;"><?= date('d.m.Y', $client->getCreatedAt()) ?></span></div>

                <div class="col-lg-4">
                    <label style="padding-left: 10px;">Тариф:</label>
                    <span style="padding-left: 10px;">
                        <?php
                        /** @var ClientRatesPlan $lastClientRatesPlan */
                        if ($lastClientRatesPlan = $client->findLastClientRatesPlan()) : ?>
                            <?= $lastClientRatesPlan->ratesPlan->getName() ?>
                        <?php else : ?>
                            Не установлен
                        <?php endif; ?>
                    </span></div>

                <div class="col-lg-4"><label style="padding-left: 10px;">Статус:</label>

                    <?php
                    /** @var ClientActivation $clientActivation */
                    $clientActivation = $client->findClientActivation();
                    if ($clientActivation->getStatus() === ClientActivation::ACTIVE) : ?>
                        <span style="padding-left: 10px;">Активирована</span>
                    <?php elseif ($clientActivation->getStatus() === ClientActivation::NO_ACTIVE) : ?>
                        <span style="padding-left: 10px;">Заблокирована</span>
                    <?php endif; ?>

                </div>

            </div>

            <div class="row">

                <div class="col-md-12" style="padding-top: 10px; padding-left: 25px;">
                    <label>Наименование организации:</label>
                    <div><?= $client->getName() ?></div>
                </div>

                <div class="col-md-12" style="padding-top: 10px; padding-left: 25px;">
                    <label>Полное наименование организации:</label>
                    <div><?= $client->getFullname() ?></div>
                </div>

                <div class="col-md-12" style="padding-top: 10px; padding-left: 25px;">
                    <label>Город, в котором находится организация:</label>
                    <div><?= $client->getCity() ?></div>
                </div>

                <div class="col-md-12" style="padding-top: 10px; padding-left: 25px;">
                    <label>Описание организации:</label>
                    <div><?= $client->getDescription() ?></div>
                </div>

                <div class="col-md-12" style="padding-top: 10px; padding-left: 25px;">
                    <?php $clientSettings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE ? $accessAdmin = 'Доступ разрешен' : $accessAdmin = 'Доступ запрещен'; ?>
                    <label>Доступ к данным организации:</label>
                    <div><?= $accessAdmin ?></div>
                </div>

                <div class="col-lg-12 mt-15 block_wishListChangeAccess">
                    <div class="bolder pl-10 mb-5" style="display:flex; align-items: center;">Доступ к спискам запросов B2B компаний:
                        <?= Html::button('Изменить', [
                            'class' => 'btn btn-default changeAccessWishList',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'color' => '#FFFFFF',
                                'background' => '#52BE7F',
                                'width' => '120px',
                                'height' => '40px',
                                'font-size' => '18px',
                                'border-radius' => '8px',
                                'margin-left' => '10px'
                            ]
                        ])?>
                    </div>
                    <div>
                        <span class="pl-10">Организация получила доступ к общим спискам запросов B2B компаний:</span>
                        <span class="pl-5"><?php $accessGeneralWishList = $client->isAccessGeneralWishList() ? 'Да' : 'Нет' ?><?= $accessGeneralWishList ?></span>
                    </div>
                    <div>
                        <span class="pl-10">Организация разрешает доступ к своим спискам запросов B2B компаний:</span>
                        <span class="pl-5"><?php $accessMyWishList = $client->isAccessMyWishList() ? 'Да' : 'Нет' ?><?= $accessMyWishList ?></span>
                    </div>
                </div>

                <div class="col-lg-12 mt-15 pl-20 block_wishListChangeAccessForm" style="display: none;">
                    <?php $form = ActiveForm::begin([
                        'id' => 'wishListChangeAccessForm',
                        'action' => Url::to(['/admin/clients/change-access-wish-list', 'id' => $client->getId()]),
                        'options' => ['class' => 'g-py-15 wishListChangeAccessForm'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                        <?= $form->field($formChangeAccessWishList, 'accessGeneralWishList')
                            ->checkbox(['value' => true]) ?>

                        <?= $form->field($formChangeAccessWishList, 'accessMyWishList')
                            ->checkbox(['value' => true]) ?>

                        <div class="form-group col-md-12" style="display: flex;">
                            <?= Html::submitButton('Сохранить', [
                                'class' => 'btn btn-default pull-right',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'margin-bottom' => '15px',
                                    'background' => '#7F9FC5',
                                    'width' => '180px',
                                    'height' => '40px',
                                    'border-radius' => '8px',
                                    'text-transform' => 'uppercase',
                                    'font-size' => '16px',
                                    'color' => '#FFFFFF',
                                    'font-weight' => '700',
                                    'padding-top' => '9px'
                                ]
                            ]) ?>
                            <?= Html::button('Отмена', [
                                'class' => 'btn btn-default pull-right cancelChangeAccessWishList',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'margin-bottom' => '15px',
                                    'width' => '180px',
                                    'height' => '40px',
                                    'border-radius' => '8px',
                                    'text-transform' => 'uppercase',
                                    'font-size' => '16px',
                                    'font-weight' => '700',
                                    'padding-top' => '9px'
                                ]
                            ]) ?>
                        </div>

                    <?php ActiveForm::end(); ?>
                </div>

            </div>
        </div>
    </div>
</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/admin_clients_view.js'); ?>
