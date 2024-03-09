<?php

use app\models\ContractorCommunications;
use app\models\User;
use yii\helpers\Html;

/**
 * @var $models User[]
 * @var $user User
 */

?>

<?php if (count($models) !== 0): ?>

    <?php foreach ($models as $model) : ?>

        <?php /** @var $lastCommunication ContractorCommunications|null */
        $lastCommunication = ContractorCommunications::getLastCommunicationWithContractor($model->getId()) ?>

        <div class="row container-one_user">

            <div class="col-md-4 column-user-fio" id="linkContractorInfo-<?= $model->getId() ?>" title="Перейти в профиль исполнителя">

                <!--Проверка существования аватарки-->
                <?php if ($model->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$model->getId().'/avatar/'.$model->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($model->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="block-fio-and-date-last-visit">
                    <div class="block-fio"><?= $model->getUsername() ?></div>
                    <div class="block-date-last-visit">
                        <?php if(is_string($model->checkOnline)) : ?>
                            Пользователь был в сети <?= $model->checkOnline ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="col-md-1">
                <?= $model->getCountContractorMyProjects() . ' / ' . $model->getCountContractorProjects() ?>
            </div>

            <div class="col-md-5" style="display:flex; align-items: center; justify-content: space-between;">
                <div><?= $lastCommunication ? $lastCommunication->getDescription() : '' ?></div>
                <div style="min-width: 100px; padding-left: 5px; padding-right: 5px;">
                    <?= $lastCommunication ? date('d.m.Y', $lastCommunication->getCreatedAt()) : '' ?>
                </div>
            </div>

            <div class="col-md-2">
                <div>
                    <?php $countUnreadCommunications = $user->getCountUnreadCommunicationsByContractor($model->getId()) ?>
                    <?= Html::a($countUnreadCommunications ? '<div class="countUnreadCommunicationsByContractor active">' . $countUnreadCommunications . '</div>Коммуникации' : 'Коммуникации', ['/contractors/communication-projects', 'id' => $model->getId()], [
                        'class' => 'btn btn-default pull-right',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'color' => '#FFFFFF',
                            'background' => '#707F99',
                            'width' => '180px',
                            'height' => '35px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                        ]]) ?>
                </div>
                <div>
                    <?= Html::a('Задания', ['/contractors/task-projects', 'id' => $model->getId()], [
                        'class' => 'btn btn-default pull-right',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'color' => '#FFFFFF',
                            'background' => '#52BE7F',
                            'width' => '180px',
                            'height' => '35px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                        ]]) ?>
                </div>
            </div>

        </div>

        <div class="container-one_user_mobile">

            <div class="column-user-fio" id="linkContractorInfo-<?= $model->getId() ?>" title="Перейти в профиль исполнителя">

                <!--Проверка существования аватарки-->
                <?php if ($model->getAvatarImage()) : ?>
                    <?= Html::img('@web/upload/user-'.$model->getId().'/avatar/'.$model->getAvatarImage(), ['class' => 'user_picture']) ?>
                <?php else : ?>
                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                <?php endif; ?>

                <!--Проверка онлайн статуса-->
                <?php if ($model->checkOnline === true) : ?>
                    <div class="checkStatusOnlineUser active"></div>
                <?php else : ?>
                    <div class="checkStatusOnlineUser"></div>
                <?php endif; ?>

                <div class="block-fio-and-date-last-visit">
                    <div class="block-fio"><?= $model->getUsername() ?></div>
                    <div class="block-date-last-visit">
                        <?php if(is_string($model->checkOnline)) : ?>
                            Пользователь был в сети <?= $model->checkOnline ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div>
                <span class="bolder">Дата последней коммуникации</span>
                <span><?= $lastCommunication ? date('d.m.Y', $lastCommunication->getCreatedAt()) : '' ?></span>
            </div>

            <div>
                <span class="bolder">Последняя коммуникация</span>
                <span><?= $lastCommunication ? $lastCommunication->getDescription() : '' ?></span>
            </div>

            <div class="display-flex justify-content-center mt-20">
                <div>
                    <?php $countUnreadCommunications = $user->getCountUnreadCommunicationsByContractor($model->getId()) ?>
                    <?= Html::a($countUnreadCommunications ? '<div class="countUnreadCommunicationsByContractor active">' . $countUnreadCommunications . '</div>Коммуникации' : 'Коммуникации', ['/contractors/communication-projects', 'id' => $model->getId()], [
                        'class' => 'btn btn-default pull-right',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'color' => '#FFFFFF',
                            'background' => '#707F99',
                            'width' => '180px',
                            'height' => '35px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                            'margin-right' => '10px',
                        ]]) ?>
                </div>
                <div>
                    <?= Html::a('Задания', ['/contractors/task-projects', 'id' => $model->getId()], [
                        'class' => 'btn btn-default pull-right',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'color' => '#FFFFFF',
                            'background' => '#52BE7F',
                            'width' => '120px',
                            'height' => '35px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                        ]]) ?>
                </div>
            </div>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="text-center mt-30 ">Отсутствуют исполнители проектов...</div>

<?php endif; ?>
