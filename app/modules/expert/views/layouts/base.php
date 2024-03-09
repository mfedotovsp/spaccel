<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\AppAsset;
use app\models\User;

/**
 * @var string $content
 * @var User $user
 */

$user = User::findOne(Yii::$app->user->getId());
$user_id = Yii::$app->user->getId();

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

        <div class="wrap" id="identifying_recipient_new_message-<?= $user_id ?>">

            <div style="margin-bottom: -20px;">

                <?php
                NavBar::begin([
                    'id' => 'main_menu_expert',
                    'brandLabel' => Yii::$app->name = 'Spaccel <span style="font-size: 13px;font-weight: 700;border-bottom: 1px solid #c0c0c0;">expert-panel</span>',
                    'brandUrl' => ['/expert/default/index'],
                    'brandOptions' => ['class' => 'font_nav_menu_brand'],
                    'options' => [
                        'class' => 'navbar-inverse navbar-fixed-top',
                    ],
                    'renderInnerContainer' => false,
                    'headerContent' => '<div class="navbar-header-content">' . $user->getTextRole() . ': ' . (mb_strlen($user->getUsername()) > 12 ? mb_substr($user->getUsername(), 0, 10) . '...' : $user->getUsername()) . '</div>',
                ]);

                echo Nav::widget([
                    'id' => 'main_navbar_right',
                    'options' => ['class' => 'navbar-nav navbar-right font_nav_menu_link'],
                    'items' => [

                        ['label' => $user->countUnreadCommunications ? '<div class="countUnreadCommunications active">' . $user->countUnreadCommunications . '</div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления'])
                            : '<div class="countUnreadCommunications"></div>' . Html::img('/images/icons/icon_notification_bell.png', ['class' => 'icon_messager', 'title' => 'Уведомления']), 'url' => ['/expert/communications/notifications', 'id' => $user->getId()]],

                        ['label' => Html::img('/images/icons/icon_expertise.png', ['class' => 'icon_messager', 'title' => 'Экспертизы']), 'url' => ['/expert/expertise/index']],

                        [
                            'label' => $user->getAvatarImage() ? Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'icon_user_avatar user_profile_picture'])
                                : Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_user_avatar_default user_profile_picture']),
                            'items' => [
                                ['label' => 'Мой профиль', 'url' => Url::to(['/expert/profile/index', 'id' => $user->getId()])],
                                ['label' => '<span>Выход ('.$user->getAvatarImage().')</span>', 'url' => Url::to(['/site/logout'])],
                            ],
                        ],

                        ['label' => $user->countUnreadMessages ? '<div class="countUnreadMessages active">' . $user->countUnreadMessages . '</div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения'])
                            : '<div class="countUnreadMessages"></div>' . Html::img('/images/icons/icon_messager_animation.svg', ['class' => 'icon_messager', 'title' => 'Сообщения']), 'url' => ['/expert/message/index', 'id' => $user->getId()]],

                        ['label' => Html::img('/images/icons/icon_light_bulb.png', ['class' => 'icon_messager', 'title' => 'Методическое руководство']), 'url' => ['/site/methodological-guide']],
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
