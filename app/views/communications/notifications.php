<?php

use app\models\DuplicateCommunications;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Уведомления';
$this->registerCssFile('@web/css/notifications-style.css');

/**
 * @var DuplicateCommunications[] $communications
 * @var Pagination $pages
 */

?>

<div class="row">
    <div class="col-xs-12 header-title-mobile"><?= $this->title ?></div>
</div>

<div class="row page-notifications">

    <div class="col-md-12 notifications-content">

        <?php if ($communications) : ?>

            <!--Заголовки для списка уведомлений по проекту-->
            <div class="row headers_data_notifications">
                <div class="col-xs-10">Уведомления</div>
                <div class="col-xs-2">Дата и время</div>
            </div>

            <?php foreach ($communications as $key => $communication) : ?>

                <div class="row line_data_notifications">

                    <div class="col-xs-10">

                        <?php if ($communication->isNeedReadButton()) : ?>

                            <div class="notification_no_read-description">
                                <?= $communication->getDescription() ?>
                            </div>

                            <div class="read-notification">
                                Чтобы отметить уведомление как прочитанное, нажмите <?= Html::button('OK', [
                                    'id' => 'read_notification-'.$communication->getId(),
                                    'class' => 'btn btn-default link-read-notification',
                                    'style' => ['border-radius' => '8px'],
                                ]) ?>
                            </div>

                        <?php else: ?>

                            <div class="notification-description">
                                <?= $communication->getDescription() ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="col-xs-2 text-center">
                        <?= date('d.m.Y H:i',$communication->getCreatedAt()) ?>
                    </div>

                </div>

                <div class="line_data_notifications_mobile">

                    <?php if ($communication->isNeedReadButton()) : ?>

                        <div class="notification_no_read-description">
                            <?= $communication->getDescription() ?>
                        </div>

                        <div class="read-notification">
                            Чтобы отметить уведомление как прочитанное, нажмите <?= Html::button('OK', [
                                'id' => 'read_notification-'.$communication->getId(),
                                'class' => 'btn btn-default link-read-notification',
                                'style' => ['border-radius' => '8px'],
                            ]) ?>
                        </div>

                        <div class="notification_no_read-date">
                            <?= date('d.m.Y H:i',$communication->getCreatedAt()) ?>
                        </div>

                    <?php else: ?>

                        <div class="notification-description">
                            <?= $communication->getDescription() ?>
                        </div>

                        <div class="notification-date">
                            <?= date('d.m.Y H:i',$communication->getCreatedAt()) ?>
                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

            <div class="pagination-users">
                <?= LinkPager::widget([
                    'pagination' => $pages,
                    'activePageCssClass' => 'pagination_active_page',
                    'options' => ['class' => 'pagination-users-list'],
                ]) ?>
            </div>

        <?php else : ?>

            <h3 class="text-center">У вас пока нет уведомлений...</h3>

        <?php endif; ?>

    </div>
</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/user_notifications.js'); ?>