<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\ProfileAsset;
use app\models\User;

/**
 * @var string $content
 * @var User $user
 */

ProfileAsset::register($this);
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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
