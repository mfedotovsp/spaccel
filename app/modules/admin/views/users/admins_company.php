<?php

use app\models\Client;
use app\models\Projects;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use yii\widgets\LinkPager;

/**
 * @var Client $client
 * @var User[] $users
 * @var Pagination $pages
 */

$this->title = 'Трекеры «' . $client->getName() . '»';
$this->registerCssFile('@web/css/users-index-style.css');

?>

<div class="users-admins">

    <div class="col-md-12" style="margin-top: 35px; padding-left: 25px;">
        <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page open_modal_instruction_page',
            'title' => 'Инструкция', 'onclick' => 'return false'
        ]) ?>
    </div>

    <div class="container-fluid">

        <div class="row" style="display:flex; align-items: center; padding: 30px 0 15px 0; font-weight: 700;">

            <div class="col-md-3" style="padding-left: 30px;">
                Логин
            </div>

            <div class="col-md-3 text-center">
                Пользователи, проекты
            </div>

            <div class="col-md-2 text-center">
                Статус
            </div>

            <div class="col-md-2 text-center">
                E-mail
            </div>

            <div class="col-md-1 text-center">
                Дата измен.
            </div>

            <div class="col-md-1 text-center">
                Дата регистр.
            </div>

        </div>

        <div class="row block_all_users">

            <?php foreach ($users as $user) : ?>

                <div class="row container-one_user user_container_number-<?=$user->getId() ?>">

                    <div class="col-md-3 column-user-fio" id="link_user_profile-<?= $user->getId() ?>">

                        <!--Проверка существования аватарки-->
                        <?php if ($user->getAvatarImage()) : ?>
                            <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($user->checkOnline === true) : ?>
                            <div class="checkStatusOnlineUser active"></div>
                        <?php else : ?>
                            <div class="checkStatusOnlineUser"></div>
                        <?php endif; ?>

                        <div class="block-fio-and-date-last-visit">
                            <div class="block-fio"><?= $user->getUsername() ?></div>
                            <div class="block-date-last-visit">
                                <?php if(is_string($user->checkOnline)) : ?>
                                    Пользователь был в сети <?= $user->checkOnline ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-3 column-tracker">

                        <?php $count_users = User::find()->andWhere(['id_admin' => $user->getId()])->count();?>

                        <?= Html::a( '<span class="glyphicon glyphicon-user" style="font-size: 16px;"></span><span style="margin-left: 5px;"> - '.$count_users.'</span>', Url::to(['/admin/users/group', 'id' => $user->getId()]), [
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'width' => '80px',
                                'height' => '40px',
                                'font-size' => '18px',
                                'border-radius' => '8px 0 0 8px',
                            ],
                            'class' => 'btn btn-lg btn-default',
                        ]) ?>

                        <?php
                        $countProjects = Projects::find()->with('user')
                            ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                            ->andWhere(['user.id_admin' => $user->getId()])->count();
                        ?>

                        <?= Html::a( 'Проекты - '.$countProjects, Url::to(['/admin/projects/group', 'id' => $user->getId()]), [
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

                    <div class="col-md-2 column-user-status">

                        <?php if ($user->getStatus() === User::STATUS_DELETED) : ?>
                            <span class="text-danger">Заблокирован</span>
                        <?php elseif ($user->getStatus() === User::STATUS_NOT_ACTIVE) : ?>
                            <span>Не активирован</span>
                        <?php elseif ($user->getStatus() === User::STATUS_ACTIVE) : ?>
                            <span class="text-success">Активирован</span>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-2 text-center">
                        <div class=""><?= $user->getEmail() ?></div>
                    </div>

                    <div class="col-md-1 text-center">
                        <?= date('d.m.Y', $user->getUpdatedAt()) ?>
                    </div>

                    <div class="col-md-1 text-center">
                        <?= date('d.m.Y', $user->getCreatedAt()) ?>
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

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/users_index_main_admin.js'); ?>
