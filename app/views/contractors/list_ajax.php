<?php

use app\models\ContractorCommunications;
use app\models\ContractorCommunicationTypes;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $models User[]
 */

?>

<?php if (count($models) !== 0): ?>

    <?php foreach ($models as $model) : ?>

        <div class="row container-one_user user_container_number-<?= $model->getId() ?>">

            <div class="col-md-5 column-user-fio" id="linkContractorInfo-<?= $model->getId() ?>">

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
                    <div class="block-fio">
                        <?= $model->getUsername() ?>
                        <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px']]),['#'], [
                            'class' => 'openContractorInfo', 'title' => 'Смотреть описание',
                            'style' => ['padding-left' => '10px']
                        ]) ?>
                    </div>
                    <div class="block-date-last-visit">
                        <?php if(is_string($model->checkOnline)) : ?>
                            Пользователь был в сети <?= $model->checkOnline ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="col-md-3">
                <?php foreach ($model->contractorInfo->contractorActivities as $activity): ?>
                    <div><?= $activity->getTitle() ?></div>
                <?php endforeach; ?>
            </div>

            <div class="col-md-2">
                Проекты - <?= $model->getCountContractorProjects() ?>
            </div>

            <div class="col-md-2 text-center">

                <?php if (ContractorCommunications::isNeedAskContractor($model->getId(), $_POST['SearchContractorsForm']['projectId'], $_POST['SearchContractorsForm']['activityId'])) : ?>

                    <?= Html::a('Сделать запрос', Url::to([
                        '/contractors/send-communication',
                        'adressee_id' => $model->getId(),
                        'type' => ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT,
                        'project_id' => (int)$_POST['SearchContractorsForm']['projectId'],
                        'activity_id' => (int)$_POST['SearchContractorsForm']['activityId']
                    ]), [
                        'class' => 'btn btn-default send-communication',
                        'id' => 'send_communication-'.$model->getId(),
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'color' => '#FFFFFF',
                            'background' => '#52BE7F',
                            'width' => '140px',
                            'height' => '40px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                        ]
                    ]) ?>

                <?php else : ?>

                    <div class="text-success">Запрос сделан</div>

                <?php endif; ?>

            </div>

        </div>

        <div class="container-one_user_mobile user_container_number-<?= $model->getId() ?>">

            <div class="column-user-fio">

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
                    <div class="block-fio">
                        <?= $model->getUsername() ?>
                    </div>
                    <div class="block-date-last-visit">
                        <?php if(is_string($model->checkOnline)) : ?>
                            Пользователь был в сети <?= $model->checkOnline ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div>
                <span class="bolder">Виды деятельности:</span>
                <?php foreach ($model->contractorInfo->contractorActivities as $activity): ?>
                    <span><?= $activity->getTitle() ?></span>
                <?php endforeach; ?>
            </div>

            <div class="display-flex justify-content-center align-items-center mt-20 action-buttons" id="linkContractorInfo-<?= $model->getId() ?>">

                <?= Html::a('Смотреть описание', ['#'], [
                    'class' => 'btn btn-default openContractorInfo',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'color' => '#FFFFFF',
                        'background' => '#707F99',
                        'width' => '180px',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                        'margin-right' => '10px',
                    ]]) ?>

                <?php if (ContractorCommunications::isNeedAskContractor($model->getId(), $_POST['SearchContractorsForm']['projectId'], $_POST['SearchContractorsForm']['activityId'])) : ?>

                    <div class="button-send-communication">
                        <?= Html::a('Сделать запрос', Url::to([
                            '/contractors/send-communication',
                            'adressee_id' => $model->getId(),
                            'type' => ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT,
                            'project_id' => (int)$_POST['SearchContractorsForm']['projectId'],
                            'activity_id' => (int)$_POST['SearchContractorsForm']['activityId']
                        ]), [
                            'class' => 'btn btn-default send-communication',
                            'id' => 'send_communication-'.$model->getId(),
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'color' => '#FFFFFF',
                                'background' => '#52BE7F',
                                'width' => '140px',
                                'height' => '40px',
                                'font-size' => '18px',
                                'border-radius' => '8px',
                            ]
                        ]) ?>
                    </div>

                <?php else : ?>

                    <div class="text-success">Запрос сделан</div>

                <?php endif; ?>

            </div>

        </div>

        <div class="row blockContractorInfo containerContractorInfo-<?= $model->getId() ?>">

            <div class="col-md-12">
                <span class="bolder">Email:</span>
                <span><?= $model->getEmail() ?></span>
            </div>

            <div class="col-md-12">
                <div class="bolder">Образование:</div>
                <div class="row container-fluid">

                    <?php foreach ($model->contractorEducations as $education): ?>

                        <div class="col-md-4">
                            <div>
                                <span>Учебное заведение:</span>
                                <span><?= $education->getEducationalInstitution() ?></span>
                            </div>
                            <div>
                                <span>Факультет:</span>
                                <span><?= $education->getFaculty() ?></span>
                            </div>
                            <div>
                                <span>Курс:</span>
                                <span><?= $education->getCourse() ?: '--------' ?></span>
                            </div>
                            <div>
                                <span>Дата окончания:</span>
                                <span><?= $education->getFinishDate() ? date('d.m.Y', $education->getFinishDate()) : '--------' ?></span>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>

            <div class="col-md-12">

                <?php $contractorInfo = $model->contractorInfo ?>

                <div class="bolder">Опыт в проектах:</div>

                <div class="row container-fluid">
                    <div class="col-md-12">
                        <span>Ученая степень:</span>
                        <span><?= $contractorInfo->getAcademicDegree() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span>Должность:</span>
                        <span><?= $contractorInfo->getPosition() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span>Научные публикации:</span>
                        <span><?= $contractorInfo->getPublications() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span>Реализованные проекты:</span>
                        <span><?= $contractorInfo->getImplementedProjects() ?></span>
                    </div>
                    <div class="col-md-12">
                        <span>Роль в реализованных проектах:</span>
                        <span><?= $contractorInfo->getRoleInImplementedProjects() ?></span>
                    </div>
                </div>

            </div>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="text-center mt-30 ">Отсутствуют исполнители проектов...</div>

<?php endif; ?>
