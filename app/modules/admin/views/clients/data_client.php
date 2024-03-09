<?php

use app\models\Client;
use app\models\ClientActivation;
use app\models\ClientRatesPlan;
use app\models\ClientSettings;
use app\models\CustomerManager;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Client $client
 */

?>

<div class="col-md-3 column-client-name" id="link_client_page-<?= $client->getId() ?>">

    <?php if ($client->settings->getAvatarImage()) : ?>
        <?= Html::img('@web/upload/company-'.$client->getId().'/avatar/'.$client->settings->getAvatarImage(), ['class' => 'user_picture']) ?>
    <?php else : ?>
        <?= Html::img('/images/avatar/client_default.png', ['class' => 'user_picture_default']) ?>
    <?php endif; ?>

    <div class="block-name-and-fullname">
        <div class="block-name">
            <?= Html::a($client->getName(), ['/admin/clients/view', 'id' => $client->getId()], [
                'class' => 'block_name_link',
                'title' => 'Перейти на страницу организации'
            ]) ?>

            <span>
                <?php if ($client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                    echo Html::img('/images/icons/access_open.png', ['width' => '20px']);
                } else {
                    echo Html::img('/images/icons/access_close.png', ['width' => '20px']);
                } ?>
            </span>
        </div>
        <div class="block-fullname"><?= $client->getFullname() ?></div>
        <div class="block-admin-profile-link">
            <div class="bolder">Администратор</div>
            <?php $admin = $client->settings->admin; ?>
            <?= Html::a($admin->getUsername(), ['/admin/profile/index', 'id' => $admin->getId()], [
                'class' => 'block_name_link',
                'title' => 'Перейти в профиль'
            ]) ?>
        </div>
    </div>

</div>

<div class="col-md-2 column-client-info-entities mt-10">

    <?php
    /** @var CustomerManager $customerManager */
    $customerManager = $client->findCustomerManager();
    $manager = $customerManager->user; ?>

    <?php if ($manager) : ?>

        <?= Html::a($manager->getUsername(), ['/admin/clients/get-list-managers', 'clientId' => $client->getId()], [
            'class' => 'btn btn-lg btn-default open_change_manager_modal',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'background' => '#E0E0E0',
                'width' => '200px',
                'border-radius' => '8px',
                'overflow' => 'hidden',
                'white-space' => 'normal',
                'font-size' => '16px'
            ],
        ]) ?>

    <?php else : ?>

        <?= Html::a('Назначить менеджера', ['/admin/clients/get-list-managers', 'clientId' => $client->getId()], [
            'class' => 'btn btn-lg btn-default open_change_manager_modal',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'background' => '#E0E0E0',
                'width' => '200px',
                'border-radius' => '8px',
                'overflow' => 'hidden',
                'white-space' => 'normal',
                'font-size' => '16px'
            ],
        ]) ?>

    <?php endif; ?>

</div>

<div class="col-md-2 column-client-info-entities mt-10">

    <?= Html::a( '<span class="glyphicon glyphicon-user" style="font-size: 16px;"></span><span style="margin-left: 5px;"> - '.$client->countTrackers.'</span>', Url::to(['/admin/users/admins', 'id' => $client->getId()]), [
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'justify-content' => 'center',
            'background' => '#E0E0E0',
            'width' => '110px',
            'height' => '40px',
            'font-size' => '18px',
            'border-radius' => '8px 0 0 8px',
        ],
        'class' => 'btn btn-lg btn-default',
        'title' => 'Трекеры',
    ]) ?>

    <?= Html::a( '<span class="glyphicon glyphicon-user" style="font-size: 16px;"></span><span style="margin-left: 5px;"> - '.$client->countExperts.'</span>', Url::to(['/admin/users/experts', 'id' => $client->getId()]), [
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'justify-content' => 'center',
            'background' => '#E0E0E0',
            'width' => '110px',
            'height' => '40px',
            'font-size' => '18px',
            'border-radius' => '0 8px 8px 0',
        ],
        'class' => 'btn btn-lg btn-default',
        'title' => 'Эксперты',
    ]) ?>

</div>

<div class="col-md-2 column-client-info-entities mt-10">

    <?= Html::a( '<span class="glyphicon glyphicon-user" style="font-size: 16px;"></span><span style="margin-left: 5px;"> - '.$client->countUsers.'</span>', Url::to(['/admin/users/index', 'id' => $client->getId()]), [
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'justify-content' => 'center',
            'background' => '#E0E0E0',
            'width' => '100px',
            'height' => '40px',
            'font-size' => '18px',
            'border-radius' => '8px 0 0 8px',
        ],
        'class' => 'btn btn-lg btn-default',
        'title' => 'Проектанты',
    ]) ?>

    <?= Html::a( 'Проекты - '.$client->countProjects, Url::to(['/admin/projects/client', 'id' => $client->getId()]), [
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'justify-content' => 'center',
            'background' => '#E0E0E0',
            'width' => '120px',
            'height' => '40px',
            'font-size' => '18px',
            'border-radius' => '0 8px 8px 0',
        ],
        'class' => 'btn btn-lg btn-default',
    ]) ?>

</div>

<div class="col-md-3 mt-10">
    <div class="row">
        <div class="col-md-7 text-center">

            <?php
            /** @var ClientRatesPlan $ratesPlan */
            if ($ratesPlan = $client->findLastClientRatesPlan()) : ?>

                <?= Html::a('<div title="'.$ratesPlan->ratesPlan->getName().'" style="overflow: hidden; width: inherit; padding: 2px 4px;">«' . $ratesPlan->ratesPlan->getName() . '»</div><div>' . date('d.m.y', $ratesPlan->getDateStart()) . ' по ' . date('d.m.y', $ratesPlan->getDateEnd()) . '</div>', ['/admin/rates-plans/get-list-rates-plans', 'clientId' => $client->getId()], [
                    'class' => 'btn btn-lg btn-default open_change_rates_plan_modal',
                    'style' => [
                        'display' => 'flex',
                        'flex-direction' => 'column',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'margin-top' => '15px',
                        'margin-bottom' => '10px',
                        'width' => '200px',
                        'background' => '#E0E0E0',
                        'border-radius' => '8px',
                        'font-size' => '16px',
                    ]
                ])?>

            <?php else : ?>

                <?= Html::a('Выбрать тарифный план', ['/admin/rates-plans/get-list-rates-plans', 'clientId' => $client->getId()], [
                    'class' => 'btn btn-lg btn-default open_change_rates_plan_modal',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'margin-top' => '15px',
                        'margin-bottom' => '10px',
                        'width' => '200px',
                        'height' => '40px',
                        'background' => '#E0E0E0',
                        'border-radius' => '8px',
                        'font-size' => '16px',
                    ]
                ])?>
            <?php endif; ?>

        </div>

        <div class="col-md-5 text-center">

            <?php
            /** @var ClientActivation $clientActivation */
            $clientActivation = $client->findClientActivation();
            if ($clientActivation->getStatus() === ClientActivation::ACTIVE) : ?>

                <?= Html::a('Заблокировать', ['/admin/clients/change-status', 'clientId' => $client->getId()], [
                    'class' => 'btn btn-lg btn-danger change_status_client',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'margin-top' => '15px',
                        'margin-bottom' => '10px',
                        'width' => '140px',
                        'height' => '40px',
                        'background' => '#d9534f',
                        'border-radius' => '8px',
                        'font-size' => '16px',
                    ]
                ])?>

            <?php else : ?>

                <?php if ($client->checkingReadinessActivation()) : ?>

                    <?= Html::a('Активировать', ['/admin/clients/change-status', 'clientId' => $client->getId()], [
                        'class' => 'btn btn-lg btn-success change_status_client',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'margin-top' => '15px',
                            'margin-bottom' => '10px',
                            'width' => '140px',
                            'height' => '40px',
                            'background' => '#52BE7F',
                            'border-radius' => '8px',
                            'font-size' => '16px',
                        ]
                    ])?>

                <?php else : ?>

                    <?= Html::a('Активировать', ['#'], [
                        'class' => 'btn btn-lg btn-success',
                        'title' => 'Необходимо назначить менеджера и выбрать тарифный план',
                        'onclick' => 'return false;',
                        'disabled' => true,
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'margin-top' => '15px',
                            'margin-bottom' => '10px',
                            'width' => '140px',
                            'height' => '40px',
                            'background' => '#52BE7F',
                            'border-radius' => '8px',
                            'font-size' => '16px',
                        ]
                    ])?>

                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>
