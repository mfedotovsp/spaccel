<?php

use app\models\ProjectCommunications;
use app\models\Projects;
use yii\helpers\Html;
use app\modules\expert\models\form\FormCreateCommunicationResponse;
use app\models\CommunicationTypes;
use app\modules\expert\models\ConversationExpert;
use yii\helpers\Url;
use app\models\ExpertType;
use app\models\CommunicationResponse;

/**
 * @var ProjectCommunications[] $communications
 */

?>

<!--Заголовки для списка уведомлений по проекту-->
<div class="row headers_data_notifications">
    <div class="col-md-1">Дата и время</div>
    <div class="col-md-9">Уведомление</div>
    <div class="col-md-1">Тип (статус)</div>
    <div class="col-md-1">Доступ к проекту</div>
</div>

<?php foreach ($communications as $communication) : ?>

    <div class="row line_data_notifications">
        <div class="col-md-1 text-center">
            <?= date('d.m.Y H:i',$communication->getCreatedAt()) ?>
        </div>
        <div class="col-md-9">

            <div>
                <?= $communication->getDescriptionPattern() ?>
            </div>

            <?php if ($communication->isNeedShowButtonAnswer()) : ?>
                <div class="notification-response">
                    Чтобы ответить на уведомление, нажмите <?= Html::button('ПРОДОЛЖИТЬ', [
                        'id' => 'notification_response-'.$communication->getId(),
                        'class' => 'btn btn-default link-notification-response',
                        'style' => ['border-radius' => '8px'],
                    ]) ?>
                </div>
            <?php endif; ?>

            <?php if ($communication->isNeedReadButton()) : ?>
                <div class="read-notification">
                    Чтобы отметить уведомление как прочитанное, нажмите <?= Html::button('OK', [
                        'id' => 'read_notification-'.$communication->getId(),
                        'class' => 'btn btn-default link-read-notification',
                        'style' => ['border-radius' => '8px'],
                    ]) ?>
                </div>
            <?php endif; ?>

            <?php if ($responsive = $communication->responsiveCommunication) : ?>
                <?php if ($responsive->getType() === CommunicationTypes::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE) : ?>
                    <div>
                        <b>Ответ: </b>
                        <?= FormCreateCommunicationResponse::getAnswers()[$responsive->communicationResponse->getAnswer()] ?>
                    </div>
                    <?php if ($responsive->communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) : ?>
                        <div>
                            <b>Указанные типы экпертной деятельности: </b>
                            <?= ExpertType::getContent($responsive->communicationResponse->getExpertTypes()) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <b>Комментарий: </b>
                        <?= $responsive->communicationResponse->getComment() ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>


            <?php if ($communication->getType() === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) : ?>

                <div class="conversation-exist">

                    <?php
                    /** @var $project Projects*/
                    $project = Projects::find(false)
                        ->andWhere(['id' => $communication->getProjectId()])
                        ->one();

                    $admin = $project->user->admin;
                    ?>

                    Трекер проекта:
                    <span class="bolder">
                        <?= $admin->getUsername() ?>
                    </span>

                    <?php if (ConversationExpert::isExist($communication->expert->getId(), $admin->getId())) : ?>

                        <div>В сообщениях создана беседа с трекером.</div>

                    <?php else : ?>

                        <div>
                            Чтобы создать беседу с трекером нажмите
                            <?= Html::a('OK',
                                Url::to([
                                    '/expert/message/create-expert-conversation',
                                    'user_id' => $admin->getId(),
                                    'expert_id' => $communication->expert->getId()
                                ]), [
                                    'id' => 'create_conversation-'.$communication->getId(),
                                    'class' => 'btn btn-default link-create-conversation',
                                    'style' => ['border-radius' => '8px']
                                ]) ?>
                        </div>

                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>
        <div class="col-md-1 text-center">
            <?= $communication->getNotificationStatus() ?: '-' ?>
        </div>
        <div class="col-md-1 text-center">
            <?= $communication->getAccessStatus() ?: '-' ?>
        </div>
    </div>

<?php endforeach; ?>
