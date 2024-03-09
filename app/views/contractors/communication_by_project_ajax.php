<?php

use app\models\ContractorCommunicationResponse;
use app\models\ContractorCommunications;
use app\models\ContractorCommunicationTypes;
use app\modules\contractor\models\form\FormCreateCommunicationResponse;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $models ContractorCommunications[]*/

?>

<div class="row headers_data_notifications">

    <div class="col-md-2">
        Дата и время
    </div>

    <div class="col-md-2">
        Тип получателя
    </div>

    <div class="col-md-2">
        Вид деятельности
    </div>

    <div class="col-md-5">
        Описание коммуникации
    </div>

    <div class="col-md-1 pl-25">
        Статус
    </div>

</div>

<?php foreach ($models as $key => $model): ?>

    <div class="row line_data_notifications">

        <div class="col-md-2">
            <?= date('d.m.Y H:i:s', $model->getCreatedAt()) ?>
        </div>

        <div class="col-md-2">
            <?= $model->user->getId() === $model->getAdresseeId() ? 'Руководитель' : 'Исполнитель' ?>
        </div>

        <div class="col-md-2">
            <?= $model->activity->getTitle() ?>
        </div>

        <div class="col-md-5">
            <?= $model->getDescription() ?>

            <?php if ($model->getType() === ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT): ?>

                <div><b>Ответ: </b> <?= FormCreateCommunicationResponse::getAnswers()[$model->communicationResponse->getAnswer()] ?></div>
                <div><b>Комментарий: </b> <?= $model->communicationResponse->getComment() ?></div>

                <?php if ($model->isNeedReadButton()) : ?>

                    <?php if ($model->communicationResponse->getAnswer() === ContractorCommunicationResponse::POSITIVE_RESPONSE): ?>
                        <div class="row mt-15 mb-5 response-action-to-communication">
                            <div class="col-md-6">
                                <?= Html::a('Назначить', Url::to([
                                    '/contractors/send-communication',
                                    'adressee_id' => $model->getSenderId(),
                                    'type' => ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT,
                                    'project_id' => $model->getProjectId(),
                                    'activity_id' => $model->getActivityId(),
                                    'triggered_communication_id' => $model->getId()
                                ]), [
                                    'class' => 'btn btn-success appoints-contractor-project',
                                    'id' => 'appoints_contractor_project-'.$model->getId(),
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
                                    '/contractors/send-communication',
                                    'adressee_id' => $model->getSenderId(),
                                    'type' => ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT,
                                    'project_id' => $model->getProjectId(),
                                    'activity_id' => $model->getActivityId(),
                                    'triggered_communication_id' => $model->getId()
                                ]), [
                                    'class' => 'btn btn-danger appoints-contractor-project',
                                    'id' => 'appoints_does_not_contractor_project-'.$model->getProjectId(),
                                    'style' => [
                                        'min-width' => '100%',
                                        'font-size' => '18px',
                                        'border-radius' => '8px',
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="read-notification">
                            Чтобы отметить уведомление как прочитанное, нажмите <?= Html::button('OK', [
                                'id' => 'read_notification-'.$model->getId(),
                                'class' => 'btn btn-default link-read-notification',
                                'style' => ['border-radius' => '8px'],
                            ]) ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            <?php elseif ($model->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT && !ContractorCommunications::findOne(['triggered_communication_id' => $model->getId()])): ?>

                <div class="row mt-15 mb-5 response-action-to-communication">
                    <div class="col-md-12">
                        <?= Html::a('Отозвать запрос', Url::to([
                            '/contractors/send-communication',
                            'adressee_id' => $model->getAdresseeId(),
                            'type' => ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT,
                            'project_id' => $model->getProjectId(),
                            'activity_id' => $model->getActivityId(),
                            'triggered_communication_id' => $model->getId()
                        ]), [
                            'class' => 'btn btn-danger appoints-contractor-project',
                            'id' => 'withdraws_request_about_readiness_to_join_project-'.$model->getProjectId(),
                            'style' => [
                                'min-width' => '50%',
                                'font-size' => '18px',
                                'border-radius' => '8px',
                            ]
                        ]) ?>
                    </div>
                </div>

            <?php elseif ($model->getType() === ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT &&
                !ContractorCommunications::findOne([
                    'type' => ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT,
                    'triggered_communication_id' => $model->getId(),
                ])): ?>

                <div class="row mt-15 mb-5 response-action-to-communication">
                    <div class="col-md-12">
                        <?= Html::a('Отозвать с проекта', Url::to([
                            '/contractors/send-communication',
                            'adressee_id' => $model->getAdresseeId(),
                            'type' => ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT,
                            'project_id' => $model->getProjectId(),
                            'activity_id' => $model->getActivityId(),
                            'triggered_communication_id' => $model->getId()
                        ]), [
                            'class' => 'btn btn-danger appoints-contractor-project',
                            'id' => 'withdraws_contractor_from_project-'.$model->getProjectId(),
                            'style' => [
                                'min-width' => '50%',
                                'font-size' => '18px',
                                'border-radius' => '8px',
                            ]
                        ]) ?>
                    </div>
                </div>

            <?php elseif ($model->getType() === ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK): ?>

                <?php if ($model->isNeedReadButton()) : ?>

                    <div class="read-notification">
                        Чтобы отметить уведомление как прочитанное, нажмите <?= Html::button('OK', [
                            'id' => 'read_notification-'.$model->getId(),
                            'class' => 'btn btn-default link-read-notification',
                            'style' => ['border-radius' => '8px'],
                        ]) ?>
                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </div>

        <div class="col-md-1">
            <?= $model->getStatus() === ContractorCommunications::STATUS_READ ? 'Прочитана' : 'Ожидает прочтения' ?>
        </div>

    </div>

    <div class="line_data_notifications_mobile pt-15 pb-15">

        <div>
            <span class="bolder">Дата и время:</span>
            <span><?= date('d.m.Y H:i:s', $model->getCreatedAt()) ?></span>
        </div>

        <div>
            <span class="bolder">Тип получателя:</span>
            <span><?= $model->user->getId() === $model->getAdresseeId() ? 'Руководитель' : 'Исполнитель' ?></span>
        </div>

        <div>
            <span class="bolder">Вид деятельности:</span>
            <span><?= $model->activity->getTitle() ?></span>
        </div>

        <div>
            <span class="bolder">Статус:</span>
            <span><?= $model->getStatus() === ContractorCommunications::STATUS_READ ? 'Прочитана' : 'Ожидает прочтения' ?></span>
        </div>

        <div>
            <span class="bolder">Описание коммуникации:</span>
            <span><?= $model->getDescription() ?></span>

            <?php if ($model->getType() === ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT): ?>

                <div><b>Ответ: </b> <?= FormCreateCommunicationResponse::getAnswers()[$model->communicationResponse->getAnswer()] ?></div>
                <div><b>Комментарий: </b> <?= $model->communicationResponse->getComment() ?></div>

                <?php if ($model->isNeedReadButton()) : ?>

                    <?php if ($model->communicationResponse->getAnswer() === ContractorCommunicationResponse::POSITIVE_RESPONSE): ?>
                        <div class="row mt-15 mb-5 response-action-to-communication">
                            <div class="col-md-6">
                                <?= Html::a('Назначить', Url::to([
                                    '/contractors/send-communication',
                                    'adressee_id' => $model->getSenderId(),
                                    'type' => ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT,
                                    'project_id' => $model->getProjectId(),
                                    'activity_id' => $model->getActivityId(),
                                    'triggered_communication_id' => $model->getId()
                                ]), [
                                    'class' => 'btn btn-success appoints-contractor-project',
                                    'id' => 'appoints_contractor_project-'.$model->getId(),
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
                                    '/contractors/send-communication',
                                    'adressee_id' => $model->getSenderId(),
                                    'type' => ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT,
                                    'project_id' => $model->getProjectId(),
                                    'activity_id' => $model->getActivityId(),
                                    'triggered_communication_id' => $model->getId()
                                ]), [
                                    'class' => 'btn btn-danger appoints-contractor-project',
                                    'id' => 'appoints_does_not_contractor_project-'.$model->getProjectId(),
                                    'style' => [
                                        'min-width' => '100%',
                                        'font-size' => '18px',
                                        'border-radius' => '8px',
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="read-notification">
                            Чтобы отметить уведомление как прочитанное, нажмите <?= Html::button('OK', [
                                'id' => 'read_notification-'.$model->getId(),
                                'class' => 'btn btn-default link-read-notification',
                                'style' => ['border-radius' => '8px'],
                            ]) ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            <?php elseif ($model->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT && !ContractorCommunications::findOne(['triggered_communication_id' => $model->getId()])): ?>

                <div class="row mt-15 mb-5 response-action-to-communication">
                    <div class="col-md-12">
                        <?= Html::a('Отозвать запрос', Url::to([
                            '/contractors/send-communication',
                            'adressee_id' => $model->getAdresseeId(),
                            'type' => ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT,
                            'project_id' => $model->getProjectId(),
                            'activity_id' => $model->getActivityId(),
                            'triggered_communication_id' => $model->getId()
                        ]), [
                            'class' => 'btn btn-danger appoints-contractor-project',
                            'id' => 'withdraws_request_about_readiness_to_join_project-'.$model->getProjectId(),
                            'style' => [
                                'min-width' => '50%',
                                'font-size' => '18px',
                                'border-radius' => '8px',
                            ]
                        ]) ?>
                    </div>
                </div>

            <?php elseif ($model->getType() === ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT &&
                !ContractorCommunications::findOne([
                    'type' => ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT,
                    'triggered_communication_id' => $model->getId(),
                ])): ?>

                <div class="row mt-15 mb-5 response-action-to-communication">
                    <div class="col-md-12">
                        <?= Html::a('Отозвать с проекта', Url::to([
                            '/contractors/send-communication',
                            'adressee_id' => $model->getAdresseeId(),
                            'type' => ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT,
                            'project_id' => $model->getProjectId(),
                            'activity_id' => $model->getActivityId(),
                            'triggered_communication_id' => $model->getId()
                        ]), [
                            'class' => 'btn btn-danger appoints-contractor-project',
                            'id' => 'withdraws_contractor_from_project-'.$model->getProjectId(),
                            'style' => [
                                'min-width' => '50%',
                                'font-size' => '18px',
                                'border-radius' => '8px',
                            ]
                        ]) ?>
                    </div>
                </div>

            <?php elseif ($model->getType() === ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK): ?>

                <?php if ($model->isNeedReadButton()) : ?>

                    <div class="read-notification">
                        Чтобы отметить уведомление как прочитанное, <br/> нажмите <?= Html::button('OK', [
                            'id' => 'read_notification-'.$model->getId(),
                            'class' => 'btn btn-default link-read-notification',
                            'style' => ['border-radius' => '8px'],
                        ]) ?>
                    </div>

                <?php endif; ?>

            <?php endif; ?>
        </div>

    </div>

<?php endforeach; ?>
