<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\AppAsset;
use app\models\User;
use yii\bootstrap\Modal;

/**
 * @var string $content
 * @var User $user
 */

$user = User::findOne(Yii::$app->user->getId());

AppAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/images/icons/favicon.png']);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <div class="shared-container" id="simplebar-shared-container">

        <div class="wrap" id="identifying_recipient_new_message-<?= $user->getId() ?>">

            <div style="margin-bottom: -20px;">

                <?php
                NavBar::begin([
                    'id' => 'main_menu_client',
                    'brandLabel' => Yii::$app->name = 'Spaccel <span style="font-size: 13px;font-weight: 700;border-bottom: 1px solid #c0c0c0;">client-panel</span>',
                    'brandUrl' => ['/client/default/index'],
                    'brandOptions' => ['class' => 'font_nav_menu_brand'],
                    'options' => [
                        'class' => 'navbar-inverse navbar-fixed-top',
                    ],
                    'renderInnerContainer' => false,
                    'headerContent' => '<div class="navbar-header-content">' . $user->getTextRole() . ': ' . (mb_strlen($user->getUsername()) > 12 ? mb_substr($user->getUsername(), 0, 10) . '...' : $user->getUsername()) . '</div>',
                ]);

                if (User::isUserAdmin($user->getUsername())) :

                    echo Nav::widget([
                        'id' => 'main_navbar_right',
                        'options' => ['class' => 'navbar-nav navbar-right font_nav_menu_link'],
                        'items' => [

                            ['label' => $user->countUnreadCommunications ? '<div class="countUnreadCommunications active">' . $user->countUnreadCommunications . '</div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления'])
                                : '<div class="countUnreadCommunications"></div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления']), 'url' => ['/client/communications/notifications', 'id' => $user->getId()]],

                            ['label' => Html::img('/images/icons/projects_icon.png', ['class' => 'icon_messager', 'title' => 'Проекты']), 'url' => ['/client/projects/group', 'id' => $user->getId()]],
                            ['label' => Html::img('/images/icons/users_group_icon.png', ['class' => 'icon_messager', 'title' => 'Пользователи']), 'url' => ['/client/users/group', 'id' => $user->getId()]],

                            [
                                'label' => $user->getAvatarImage() ? Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'icon_user_avatar user_profile_picture'])
                                    : Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_user_avatar_default user_profile_picture']),
                                'items' => [
                                    ['label' => 'Мой профиль', 'url' => Url::to(['/client/profile/index', 'id' => $user->getId()])],
                                    ['label' => '<span>Выход ('.$user->getUsername().')</span>', 'url' => Url::to(['/site/logout'])],
                                ],
                            ],

                            ['label' => $user->countUnreadMessages ? '<div class="countUnreadMessages active">' . $user->countUnreadMessages . '</div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения'])
                                : '<div class="countUnreadMessages"></div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения']), 'url' => ['/client/message/index', 'id' => $user->getId()]],

                            ['label' => Html::img('/images/icons/icon_light_bulb.png', ['class' => 'icon_messager', 'title' => 'Методическое руководство']), 'url' => ['/site/methodological-guide']],
                        ],
                        'encodeLabels' => false,
                    ]);

                elseif (User::isUserAdminCompany($user->getUsername())) :

                    echo Nav::widget([
                        'id' => 'main_navbar_right',
                        'options' => ['class' => 'navbar-nav navbar-right font_nav_menu_link'],
                        'items' => [

                            ['label' => Html::img('/images/icons/projects_icon.png', ['class' => 'icon_messager', 'title' => 'Проекты']), 'url' => ['/client/projects/index']],
                            ['label' => Html::img('/images/icons/users_group_icon.png', ['class' => 'icon_messager', 'title' => 'Пользователи']), 'url' => ['/client/users/index']],
                            ['label' => Html::img('/images/icons/icon_expertise.png', ['class' => 'icon_messager', 'title' => 'Экспертизы']), 'url' => ['/client/expertise/index']],
                            ['label' => Html::img('/images/icons/icon_external_data_entry.png', ['class' => 'icon_messager', 'title' => 'Ввод внешних данных']), 'url' => ['/client/external/index']],

                            [
                                'label' => $user->getAvatarImage() ? Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'icon_user_avatar user_profile_picture'])
                                    : Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_user_avatar_default user_profile_picture']),
                                'items' => [
                                    ['label' => 'Мой профиль', 'url' => Url::to(['/client/profile/index', 'id' => $user->getId()])],
                                    ['label' => 'Профиль организации', 'url' => Url::to(['/client/client-profile/index'])],
                                    ['label' => '<span>Выход ('.$user->getUsername().')</span>', 'url' => Url::to(['/site/logout'])],
                                ],
                            ],

                            ['label' => $user->countUnreadMessages ? '<div class="countUnreadMessages active">' . $user->countUnreadMessages . '</div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения'])
                                : '<div class="countUnreadMessages"></div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения']), 'url' => ['/client/message/index', 'id' => $user->getId()]],

                            ['label' => $user->countUnreadCommunications ? '<div class="countUnreadCommunications active">' . $user->countUnreadCommunications . '</div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления'])
                                : '<div class="countUnreadCommunications"></div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления']), 'url' => ['/client/communications/notifications', 'id' => $user->getId()]],
                        ],
                        'encodeLabels' => false,
                    ]);

                endif;

                NavBar::end();
                ?>

            </div>


            <div class="container-fluid">

                <?= $content ?>

            </div>

        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row footer_desktop">
                    <div class="col-xs-7 col-sm-9 col-lg-10">&copy; СТАРТПУЛ, <?= date('Y') ?></div>
                    <div class="col-xs-5 col-sm-3 col-lg-2">
                        <div>тел: +7 930 690 06 44</div>
                        <div>e-mail: spaccel@mail.ru</div>
                    </div>
                </div>
                <div class="row footer_mobile pull-right">
                    <div class="col-xs-12">&copy; СТАРТПУЛ, <?= date('Y') ?></div>
                </div>
            </div>
        </footer>

    </div>

    <!--instruction_page begin-->

    <?php // Модальное окно - Инструкция для стадии разработки
    Modal::begin([
        'options' => ['class' => 'modal_instruction_page'],
        'size' => 'modal-lg',
        'headerOptions' => ['class' => 'header_hypothesis_modal']
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--instruction_page end-->

    <!--All-information Project begin-->

    <?php // Модальное окно - данные проекта
    Modal::begin([
        'options' => ['id' => 'data_project_modal'],
        'size' => 'modal-lg',
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--All-information Project end-->


    <!--All-information Segment begin-->

    <?php // Модальное окно - Данные сегмента
    Modal::begin([
        'options' => ['id' => 'data_segment_modal', 'class' => 'data_segment_modal'],
        'size' => 'modal-lg',
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--All-information Segment end-->


    <!--Roadmap Project begin-->

    <?php // Модальное окно - Трэкшн карта проекта
    Modal::begin([
        'options' => ['id' => 'showRoadmapProject', 'class' => 'showRoadmapProject'],
        'size' => 'modal-lg',
        'header' => '<h2 class="text-center" style="font-size: 32px; color: #4F4F4F;"></h2>',
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--Roadmap Project end-->


    <!--Roadmap Segment begin-->

    <?php // Модальное окно - Трэкшн карта сегмента
    Modal::begin([
        'options' => ['id' => 'showRoadmapSegment', 'class' => 'showRoadmapSegment'],
        'size' => 'modal-lg',
        'header' => '<div class="roadmap_segment_modal_header_title"><h2 class="roadmap_segment_modal_header_title_h2"></h2></div>',
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--Roadmap Segment end-->


    <!--Result Project begin-->

    <?php // Модальное окно - сводная таблица проекта
    Modal::begin([
        'options' => ['id' => 'showResultTableProject', 'class' => 'showResultTableProject'],
        'size' => 'modal-lg',
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--Result Project end-->


    <!--Report Project begin-->

    <?php // Модальное окно - протокол проекта
    Modal::begin([
        'options' => ['id' => 'showReportProject', 'class' => 'showReportProject'],
        'size' => 'modal-lg',
        'header' => '<h2 class="text-center" style="font-size: 32px; color: #4F4F4F;"></h2>',
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--Report Project end-->

    <!--View Expertise begin-->

    <?php // Модальное окно - просмотр экспертизы по одному из этапов проекта
    Modal::begin([
        'options' => ['id' => 'showListExpertise', 'class' => 'showListExpertise'],
        'size' => 'modal-lg',
        'header' => Html::a('<span class="text-link"></span>' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page_in_modal open_modal_instruction_page_expertise', 'title' => 'Инструкция', 'onclick' => 'return false']),
        'headerOptions' => ['style' => ['text-align' => 'center']]
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--View Expertise end-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
