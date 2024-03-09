<?php

use app\models\Client;
use app\models\ClientActivation;
use app\models\ClientRatesPlan;
use app\models\ClientSettings;
use app\models\CustomerManager;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Организации';
$this->registerCssFile('@web/css/clients-index-style.css');

/**
 * @var Client[] $clients
 * @var Client $clientSpaccel
 * @var Pagination $pages
 */

?>


<div class="container-fluid">

    <div class="row hi-line-page">
        <div class="col-md-7" style="margin-top: 35px; padding-left: 25px;">
            <?= Html::a('Организации' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>
        <div class="col-md-2">
            <?= Html::a( 'Тарифы', Url::to(['/admin/rates-plans/index']),[
                'class' => 'btn btn-success pull-right',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#52BE7F',
                    'width' => '100%',
                    'min-width' => '200px',
                    'height' => '40px',
                    'font-size' => '24px',
                    'border-radius' => '8px',
                    'margin-top' => '35px',
                ],
            ]) ?>
        </div>
        <div class="col-md-3 " style="margin-top: 30px;">
            <?=  Html::a( '<div class="new_client_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новая организация</div></div>', ['/admin/clients/create'],
                ['id' => 'showClientToCreate', 'class' => 'new_client_link_plus pull-right']
            ) ?>
        </div>
    </div>

    <div class="row headers-list-clients">

        <div class="col-md-3">
            Наименование организации
        </div>

        <div class="col-md-2 text-center">
            Менеджер Spaccel
        </div>

        <div class="col-md-2 text-center">
            Трекеры / Эксперты
        </div>

        <div class="col-md-2 text-center">
            Проектанты / Проекты
        </div>

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-7 text-center">Сведения о тарифе</div>
                <div class="col-md-5 text-center">Статус</div>
            </div>
        </div>

    </div>

    <div class="row block_all_clients">

        <?= $this->render('index_client_spaccel', ['client' => $clientSpaccel]) ?>

        <?php foreach ($clients as $client) : ?>

            <div class="row container-one_client client_container_number-<?=$client->getId() ?>">

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
                    $customerManager ? $manager = $customerManager->user : $manager = null;
                    ?>
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
                        ])?>
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
                        ])?>
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
                                ]) ?>

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
                                ]) ?>
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
                                    ]) ?>

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
                                    ]) ?>

                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="pagination-users">
            <?= LinkPager::widget([
                'pagination' => $pages,
                'activePageCssClass' => 'pagination_active_page',
                'options' => ['class' => 'pagination-users-list'],
            ]) ?>
        </div>
    </div>
</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/clients_index_main_admin.js'); ?>
