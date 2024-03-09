<?php

use app\models\Projects;
use yii\data\Pagination;
use yii\helpers\Html;
use app\modules\expert\models\form\FormCreateCommunicationResponse;
use app\models\ProjectCommunications;
use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Уведомления';
$this->registerCssFile('@web/css/notifications-style.css');

/**
 * @var ProjectCommunications[] $communications
 * @var Pagination $pages
 */

?>

<div class="row page-notifications">

    <div class="col-md-12 notifications-content">

        <?php if ($communications) : ?>

            <!--Заголовки для списка уведомлений по проекту-->
            <div class="row headers_data_notifications">
                <div class="col-xs-10">Уведомления</div>
                <div class="col-xs-2">Дата и время</div>
            </div>

            <?php foreach ($communications as $key => $communication) : ?>
            
                <?php
                /** @var $project Projects */
                $project = Projects::find(false)
                    ->andWhere(['id' => $communication->getProjectId()])
                    ->one();
                ?>

                <div class="row line_data_notifications">

                    <?php if ($communication->getType() !== CommunicationTypes::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE): ?>

                        <!--Коммуникации по проекту с проектантом ProjectCommunications (разрешение экспертизы по этапу проекта) -->
                        <div class="col-md-2 bolder">
                            <?= $communication->user->getUsername() ?>
                        </div>

                        <div class="col-md-6">
                            <?php if ($communication->getType() === CommunicationTypes::USER_ALLOWED_PROJECT_EXPERTISE): ?>
                                Проектант разрешил эспертизу по этапу «описание проекта:
                                <?= Html::a($project->getProjectName(), ['/projects/index', 'id' => $project->getUserId()])?>»
                            <?php endif; ?>
                        </div>

                        <?php if ($communication->getStatus() === ProjectCommunications::NO_READ) : ?>

                            <div class="col-md-2">
                                Чтобы отметить уведомление как прочитанное, нажмите
                                <?= Html::button('OK', [
                                    'id' => 'read_notification-'.$communication->getId(),
                                    'class' => 'btn btn-default link-read-notification',
                                    'style' => ['border-radius' => '8px'],
                                ]) ?>
                            </div>

                        <?php else : ?>

                            <div class="col-md-2 text-success">Прочитано</div>

                        <?php endif; ?>

                        <div class="col-md-2 text-center">
                            <?= date('d.m.Y H:i',$communication->getCreatedAt()) ?>
                        </div>

                    <?php else: ?>

                        <!--Коммуникации по проекту с экспертом ProjectCommunications (ответ эксперта) -->

                        <?php $communicationResponse = $communication->communicationResponse; ?>

                        <div class="col-md-2 bolder">
                            <?= $communication->expert->getUsername() ?>
                        </div>

                        <div class="col-md-3">
                            <?= FormCreateCommunicationResponse::getAnswers()[$communicationResponse->getAnswer()] . ' ' .
                            Html::a($project->getProjectName(), ['/projects/index', 'id' => $project->getUserId()]) ?>
                        </div>

                        <div class="col-md-3">
                            <?php if ($communicationResponse->getComment()) : ?>
                                <b>Комментарий: </b><?= $communicationResponse->getComment() ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) : ?>

                            <?php if ($responsiveCommunication = $communication->responsiveCommunication) : ?>

                                <?php if ($responsiveCommunication->getType() === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) : ?>

                                    <div class="col-md-2 text-success">Назначен(-а) на проект</div>

                                <?php elseif ($responsiveCommunication->getType() === CommunicationTypes::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT) : ?>

                                    <div class="col-md-2 text-danger">Отказано</div>

                                <?php endif; ?>

                            <?php else : ?>

                                <div class="col-md-2 response-action-to-communication">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= Html::a('Назначить', Url::to([
                                                '/admin/communications/get-form-types-expert',
                                                'id' => $communication->getId(),
                                            ]), [
                                                'class' => 'btn btn-success get-form-types-expert',
                                                'id' => 'appoints_expert_project-'.$communication->getId(),
                                                'style' => [
                                                    'background' => '#52BE7F',
                                                    'min-width' => '100%',
                                                    'font-size' => '18px',
                                                    'border-radius' => '8px',
                                                ]
                                            ]) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= Html::a('Отказать', Url::to([
                                                '/admin/communications/send',
                                                'adressee_id' => $communication->getSenderId(),
                                                'project_id' => $communication->getProjectId(),
                                                'type' => CommunicationTypes::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT,
                                                'triggered_communication_id' => $communication->getId()
                                            ]), [
                                                'class' => 'btn btn-danger send-communication',
                                                'id' => 'appoints_does_not_expert_project-'.$communication->getProjectId(),
                                                'style' => [
                                                    'min-width' => '100%',
                                                    'font-size' => '18px',
                                                    'border-radius' => '8px',
                                                ]
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>

                            <?php endif; ?>

                        <?php else : ?>

                            <?php if ($communication->getStatus() === ProjectCommunications::NO_READ) : ?>

                                <div class="col-md-2">
                                    Чтобы отметить уведомление как прочитанное, нажмите
                                    <?= Html::button('OK', [
                                        'id' => 'read_notification-'.$communication->getId(),
                                        'class' => 'btn btn-default link-read-notification',
                                        'style' => ['border-radius' => '8px'],
                                    ]) ?>
                                </div>

                            <?php else : ?>

                                <div class="col-md-2 text-success">Прочитано</div>

                            <?php endif; ?>

                        <?php endif; ?>

                        <div class="col-md-2 text-center">
                            <?= date('d.m.Y H:i',$communication->getCreatedAt()) ?>
                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

            <div class="pagination-admin-projects-result">
                <?= LinkPager::widget([
                    'pagination' => $pages,
                    'activePageCssClass' => 'pagination_active_page',
                    'options' => ['class' => 'admin-projects-result-pagin-list'],
                ]) ?>
            </div>

        <?php else : ?>

            <h3 class="text-center">У вас пока нет уведомлений...</h3>

        <?php endif; ?>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/main_admin_notifications.js'); ?>
