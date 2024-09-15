<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\AppAsset;
use yii\bootstrap\Modal;
use app\models\User;

/**
 * @var string $content
 * @var User $user
 */

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

    <?php $user = User::findOne(Yii::$app->user->getId()); ?>

    <div class="shared-container" id="simplebar-shared-container">

        <div class="wrap" id="identifying_recipient_new_message-<?= Yii::$app->user->getId() ?>">

            <div class="nav-bar-menu-desktop">

                <?php
                NavBar::begin([
                    'id' => 'main_menu_user',
                    'brandLabel' => Yii::$app->name = '<div class="layout-brand-label">Spaccel</div>',
                    'brandUrl' => Yii::$app->homeUrl,
                    'brandOptions' => ['class' => 'font_nav_menu_brand'],
                    'options' => [
                        'class' => 'navbar-inverse navbar-fixed-top',
                    ],
                    'renderInnerContainer' => false,
                    'headerContent' => !Yii::$app->user->isGuest ? ('<div class="navbar-header-content">' . $user->getTextRole() . ': ' . (mb_strlen($user->getUsername()) > 12 ? mb_substr($user->getUsername(), 0, 10) . '...' : $user->getUsername()) . '</div>') : (''),
                ]);

                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right font_nav_menu_link'],
                    'items' => [

                        !Yii::$app->user->isGuest ? ([
                            'label' => $user->countUnreadCommunications ? '<div class="countUnreadCommunications active">' . $user->countUnreadCommunications . '</div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления'])
                                : '<div class="countUnreadCommunications"></div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления']), 'url' => ['/communications/notifications', 'id' => $user->getId()]
                        ]) : (''),

                        !Yii::$app->user->isGuest ? (['label' => Html::img('/images/icons/projects_icon.png', ['class' => 'icon_messager', 'title' => 'Проекты']), 'url' => ['/projects/index', 'id' => $user->getId()]]) : (''),

                        !Yii::$app->user->isGuest ? ([
                            'label' => $user->countUnreadCommunicationsFromContractors ? '<div class="countUnreadCommunicationsFromContractors active">' . $user->countUnreadCommunicationsFromContractors . '</div>' . Html::img('/images/icons/users_group_icon.png', ['class' => 'icon_messager', 'title' => 'Исполнители проектов'])
                                : '<div class="countUnreadCommunicationsFromContractors"></div>' . Html::img('/images/icons/users_group_icon.png', ['class' => 'icon_messager', 'title' => 'Исполнители проектов']), 'url' => ['/contractors/index', 'id' => $user->getId()]
                        ]) : (''),

                        !Yii::$app->user->isGuest ? ([
                            'label' => $user->getAvatarImage() ? Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'icon_user_avatar user_profile_picture'])
                                : Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_user_avatar_default user_profile_picture']),
                            'items' => [
                                ['label' => 'Мой профиль', 'url' => Url::to(['/site/profile', 'id' => $user->getId()])],
                                ['label' => '<span>Выход ('.$user->getUsername().')</span>', 'url' => Url::to(['/site/logout'])],
                            ],
                        ]) : (''),

                        !Yii::$app->user->isGuest ? (
                        ['label' => $user->countUnreadMessages ? '<div class="countUnreadMessages active">' . $user->countUnreadMessages . '</div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения'])
                            : '<div class="countUnreadMessages"></div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения']), 'url' => ['/message/index', 'id' => $user->getId()]]) : '',

                        !Yii::$app->user->isGuest ? (['label' => Html::img('/images/icons/about-service.png', ['class' => 'icon_messager', 'title' => 'О сервисе']), 'url' => ['/about']]) : (['label' => 'О сервисе', 'url' => ['/about']]),

                        !Yii::$app->user->isGuest ? (
                        ['label' => Html::img('/images/icons/icon_light_bulb.png', ['class' => 'icon_messager', 'title' => 'Методическое руководство']), 'url' => ['/site/methodological-guide']]) : '',
                    ],
                    'encodeLabels' => false,
                ]);
                NavBar::end();
                ?>

            </div>

            <div class="nav-bar-menu-mobile">

                <?php
                $existUnreadBlock = '<div class="existUnreadMessagesOrCommunications"></div>';
                if (($user->countUnreadCommunications + $user->countUnreadMessages + $user->countUnreadCommunicationsFromContractors) > 0) {
                    $existUnreadBlock = '<div class="existUnreadMessagesOrCommunications active"></div>';
                } ?>

                <?php
                NavBar::begin([
                    'id' => 'main_menu_user_mobile',
                    'options' => ['class' => 'navbar-inverse navbar-fixed-top'],
                    'renderInnerContainer' => false,
                    'headerContent' => !Yii::$app->user->isGuest ? ('<div class="navbar-header-content">' . $user->getTextRole() . ': ' . (mb_strlen($user->getUsername()) > 12 ? mb_substr($user->getUsername(), 0, 10) . '...' : $user->getUsername()) . '</div>' . $existUnreadBlock) : (''),
                ]);

                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right font_nav_menu_link'],
                    'items' => [

                        !Yii::$app->user->isGuest ? ([
                            'label' => $user->countUnreadCommunications ? '<div class="link_nav_bar_menu_mobile">Уведомления</div><div class="countUnreadCommunications active">' . $user->countUnreadCommunications . '</div>'
                                : '<div class="link_nav_bar_menu_mobile">Уведомления</div><div class="countUnreadCommunications"></div>', 'url' => ['/communications/notifications', 'id' => $user->getId()]
                        ]) : (''),

                        !Yii::$app->user->isGuest ? (['label' => '<div class="link_nav_bar_menu_mobile">Проекты</div>', 'url' => ['/projects/index', 'id' => $user->getId()]]) : (''),

                        !Yii::$app->user->isGuest ? ([
                            'label' => $user->countUnreadCommunicationsFromContractors ? '<div class="link_nav_bar_menu_mobile">Исполнители</div><div class="countUnreadCommunicationsFromContractors active">' . $user->countUnreadCommunicationsFromContractors . '</div>'
                                : '<div class="link_nav_bar_menu_mobile">Исполнители</div><div class="countUnreadCommunicationsFromContractors"></div>', 'url' => ['/contractors/index', 'id' => $user->getId()]
                        ]) : (''),

                        !Yii::$app->user->isGuest ? (
                        ['label' => $user->countUnreadMessages ? '<div class="link_nav_bar_menu_mobile">Сообщения</div><div class="countUnreadMessages active">' . $user->countUnreadMessages . '</div>'
                            : '<div class="link_nav_bar_menu_mobile">Сообщения</div><div class="countUnreadMessages"></div>', 'url' => ['/message/index', 'id' => $user->getId()]]) : '',

                        !Yii::$app->user->isGuest ? (['label' => '<div class="link_nav_bar_menu_mobile">Методическое руководство</div>', 'url' => ['/site/methodological-guide']]) : '',

                        ['label' => '<div class="link_nav_bar_menu_mobile">О сервисе</div>', 'url' => ['/about']],

                        !Yii::$app->user->isGuest ? (['label' => '<div class="link_nav_bar_menu_mobile">Мой профиль</div>', 'url' => Url::to(['/site/profile', 'id' => $user->getId()])]) : (''),
                        !Yii::$app->user->isGuest ? (['label' => '<div class="link_nav_bar_menu_mobile">Выход</div>', 'url' => Url::to(['/site/logout'])]) : (''),

                        ['label' => '<div class="contacts_mobile"><div>+7 930 690 06 44</div><div>spaccel@mail.ru</div></div>']
                    ],
                    'encodeLabels' => false,
                ]);
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


    <!--Modal Hypothesis delete begin-->

    <?php
    // Подтверждение удаления гипотезы
    Modal::begin([
        'options' => [
            'id' => "delete_hypothesis_modal",
            'class' => 'delete_hypothesis_modal',
        ],
        'size' => 'modal-md',
        'footer' => '<div class="text-center">'.

            Html::a('Отмена', ['#'],[
                'class' => 'btn btn-default button-cancel',
                'style' => ['width' => '120px'],
                'onclick' => "$('#delete_hypothesis_modal').modal('hide'); return false;"
            ]).

            Html::a('Удалить', ['#'],[
                'class' => 'btn btn-default button-remove',
                'style' => ['width' => '120px'],
                'id' => "confirm_delete_hypothesis",
            ]).

            '</div>'
    ]); ?>
    <div class="text-center modal-main-content"></div>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--Modal Hypothesis delete end-->


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

    <!--Create Task begin-->

    <?php // Модальное окно - создание задания для исполнителя проекта
    Modal::begin([
        'options' => ['id' => 'showFormCreateTask', 'class' => 'showFormCreateTask'],
        'size' => 'modal-lg',
        'header' => Html::a('<span class="text-link"></span>' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page_in_modal open_modal_instruction_page_expertise', 'title' => 'Инструкция', 'onclick' => 'return false']),
        'headerOptions' => ['style' => ['text-align' => 'center']]
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--Create Task end-->

    <!--Get Tasks begin-->

    <?php // Модальное окно - получение заданий для исполнителей по этапу проекту
    Modal::begin([
        'options' => ['id' => 'showContractorTasks', 'class' => 'showContractorTasks'],
        'size' => 'modal-lg',
        'header' => Html::a('<span class="text-link"></span>' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page_in_modal open_modal_instruction_page_expertise', 'title' => 'Инструкция', 'onclick' => 'return false']),
        'headerOptions' => ['style' => ['text-align' => 'center']]
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--Get Tasks end-->

    <!--All-information Confirm begin-->

    <?php // Модальное окно - Данные подтверждения гипотезы
    Modal::begin([
        'options' => ['id' => 'data_confirm_hypothesis_modal', 'class' => 'data_confirm_hypothesis_modal'],
        'size' => 'modal-lg',
    ]); ?>
    <!--Контент добавляется через Ajax-->
    <?php Modal::end(); ?>

    <!--All-information Confirm end-->

    <!--Choosing a confirmation option begin-->

    <?php // Модальное окно - Выбор варианта подтверждения
    Modal::begin([
        'options' => [
            'id' => 'choosing_confirmation_option_modal',
            'class' => 'choosing_confirmation_option_modal'
        ],
        'size' => 'modal-lg',
        'header' => '<h3 class="text-center">Выберите вариант подтверждения</h3>'
    ]); ?>

        <div class="modal-main-content">
            <div class="modal-info-content">
                <div class="bolder pt-10">Подтверждение в наличии</div>
                <div>1. Используется для учебных целей - выполнение практических занятий, упрощенное подтверждение. Для учебных целей информация может быть задана.</div>
                <div>2. Используется при наличии (заранее полученные маркетинговые знания от экспертов рынка) подтвержденных знаний
                в виде файлов с аналитической информацией. Она может быть передана от предприятия или от эксперта.</div>
                <div class="bolder pt-10">Требуются исследования</div>
                <div>Применяется предпринимателями, когда информации о рынке мало и нет информации о подтвержденных целевых сегментах.</div>
            </div>

            <div class="modal-buttons">
                <?= Html::a('Подтверждение в наличии', ['#'],[
                    'id' => 'hypothesis_existing_confirmation',
                    'class' => 'btn btn-success',
                ]).

                Html::a('Требуются исследования', ['#'],[
                    'id' => 'confirmation_research_require',
                    'class' => 'btn btn-success',
                ]) ?>
            </div>
        </div>

    <?php Modal::end(); ?>

    <!--Choosing a confirmation option end-->


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
