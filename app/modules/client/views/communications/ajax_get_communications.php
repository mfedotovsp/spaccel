<?php

use app\models\CommunicationTypes;
use app\models\Projects;
use app\models\UserAccessToProjects;
use yii\helpers\Html;
use app\modules\expert\models\form\FormCreateCommunicationResponse;
use yii\helpers\Url;
use app\models\CommunicationResponse;
use app\models\ExpertType;

/**
 * @var UserAccessToProjects[] $admittedExperts
 * @var int $project_id
 */

?>


<?php if ($admittedExperts) : ?>

    <!--Заголовки для списка коммуникаций по проекту-->
    <div class="row headers_data_communications">
        <div class="col-md-2">Логин эксперта</div>
        <div class="col-md-2">Запрос на готовность провести экспертизу</div>
        <div class="col-md-1">Дата, время</div>
        <div class="col-md-3">Ответ на запрос</div>
        <div class="col-md-1">Дата, время</div>
        <div class="col-md-2">Назначение на проект</div>
        <div class="col-md-1">Дата, время</div>
    </div>

    <?php foreach ($admittedExperts as $admittedExpert) : ?>

        <div class="row line_data_communication">

            <?php $userCommunications = $admittedExpert->userCommunicationsForAdminTable;
            foreach ($userCommunications as $key => $communication) : ?>

                <div class="row">

                    <?php if ($key === 0) : ?>
                        <div class="col-md-2 text-center">
                            <?= $admittedExpert->user->getUsername() ?>
                        </div>
                    <?php else : ?>
                        <div class="col-md-2"></div>
                    <?php endif; ?>

                    <div class="col-md-3">

                        <div class="row">
                            <div class="col-md-8">

                                <?php if ($communication->getType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) : ?>

                                    <div class="text-success">Запрос сделан</div>
                                    <div class="">Доступ к проекту до:</div>
                                    <div class=""><?= date('d.m.y H:i', $communication->userAccessToProject->getDateStop()) ?></div>

                                    <?php if ($key === array_key_last($userCommunications)) : ?>

                                        <div class="revoke-request-button">
                                            <?= Html::a('Отозвать запрос', Url::to([
                                                '/client/communications/send',
                                                'adressee_id' => $communication->expert->getId(),
                                                'project_id' => $communication->getProjectId(),
                                                'type' => CommunicationTypes::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE
                                            ]), [
                                                'class' => 'btn btn-danger send-communication',
                                                'id' => 'send_communication-'.$communication->expert->getId(),
                                                'style' => [
                                                    'display' => 'flex',
                                                    'align-items' => 'center',
                                                    'justify-content' => 'center',
                                                    'width' => '140px',
                                                    'font-size' => '18px',
                                                    'border-radius' => '8px',
                                                ]
                                            ]) ?>
                                        </div>

                                    <?php endif; ?>

                                <?php elseif ($communication->getType() === CommunicationTypes::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE) : ?>
                                    <div class="text-danger">Запрос отозван</div>
                                <?php endif; ?>

                            </div>

                            <div class="col-md-4">
                                <?php if ($communication->getType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE ||
                                    $communication->getType() === CommunicationTypes::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE) : ?>
                                    <div><?= date('d.m.y H:i', $communication->getCreatedAt()) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($communication->getSenderId() !== $admittedExpert->getUserId()) : ?>

                        <?php $communicationExpert = $communication->responsiveCommunication; ?>
                        <?php if ($communicationResponse = $communicationExpert->communicationResponse) : ?>

                            <div class="col-md-3">

                                <?php if ($communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) : ?>

                                    <div><b>Ответ: </b><span class="text-success"><?= FormCreateCommunicationResponse::getAnswers()[$communicationResponse->getAnswer()] ?></span></div>

                                    <div><b>Типы деятельности: </b><?= ExpertType::getContent($communicationResponse->getExpertTypes()) ?></div>

                                    <?php if ($communicationResponse->getComment()) : ?>
                                        <div><b>Комментарий: </b><?= $communicationResponse->getComment() ?></div>
                                    <?php endif; ?>

                                <?php elseif ($communicationResponse->getAnswer() === CommunicationResponse::NEGATIVE_RESPONSE) : ?>

                                    <div><b>Ответ: </b><span class="text-danger"><?= FormCreateCommunicationResponse::getAnswers()[$communicationResponse->getAnswer()] ?></span></div>

                                    <?php if ($communicationResponse->getComment()) : ?>
                                        <div><b>Комментарий: </b><?= $communicationResponse->getComment() ?></div>
                                    <?php endif; ?>

                                <?php endif; ?>

                            </div>

                            <div class="col-md-1">
                                <?= date('d.m.Y H:i', $communicationExpert->getCreatedAt()) ?>
                            </div>

                            <?php if ($communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) : ?>

                                <?php if ($responsiveCommunication = $communicationExpert->responsiveCommunication) : ?>

                                    <?php if ($communicationWithdrawFromProject = $responsiveCommunication->responsiveCommunication) : ?>

                                        <div class="col-md-2 text-danger">Отозван(-а) с проекта</div>
                                        <div class="col-md-1"><?= date('d.m.Y H:i', $communicationWithdrawFromProject->getCreatedAt()) ?></div>

                                    <?php else : ?>

                                        <?php if ($responsiveCommunication->getType() === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) : ?>

                                            <div class="col-md-2">
                                                <div class="text-success">Назначен(-а) на проект</div>
                                                <div><b>Типы деятельности: </b><?= ExpertType::getContent($responsiveCommunication->typesAccessToExpertise->getTypes()) ?></div>
                                                <div>
                                                    <?php /** @var $project Projects */
                                                    $project = Projects::find(false)
                                                        ->andWhere(['id' => $project_id])
                                                        ->one();

                                                    if (!$project->getDeletedAt()): ?>

                                                        <?= Html::a('Отозвать эксперта', Url::to([
                                                            '/client/communications/send',
                                                            'adressee_id' => $communicationExpert->getSenderId(),
                                                            'project_id' => $communicationExpert->getProjectId(),
                                                            'type' => CommunicationTypes::MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT,
                                                            'triggered_communication_id' => $responsiveCommunication->getId()
                                                        ]), [
                                                            'class' => 'btn btn-danger send-communication',
                                                            'id' => 'withdraws_expert_from_project-'.$communicationExpert->getProjectId(),
                                                            'style' => [
                                                                'width' => '160px',
                                                                'font-size' => '18px',
                                                                'border-radius' => '8px',
                                                                'margin-top' => '10px'
                                                            ]
                                                        ]) ?>

                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                            <div class="col-md-1"><?= date('d.m.Y H:i', $responsiveCommunication->getCreatedAt()) ?></div>

                                        <?php elseif ($responsiveCommunication->getType() === CommunicationTypes::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT) : ?>

                                            <div class="col-md-2 text-danger">Отказано</div>
                                            <div class="col-md-1"><?= date('d.m.Y H:i', $responsiveCommunication->getCreatedAt()) ?></div>

                                        <?php endif; ?>

                                    <?php endif; ?>

                                <?php else : ?>

                                    <div class="col-md-2">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?= Html::a('Назначить', Url::to([
                                                    '/client/communications/get-form-types-expert',
                                                    'id' => $communicationExpert->getId(),
                                                ]), [
                                                    'class' => 'btn btn-success get-form-types-expert',
                                                    'style' => [
                                                        'background' => '#52BE7F',
                                                        'width' => '140px',
                                                        'font-size' => '18px',
                                                        'border-radius' => '8px',
                                                        'margin-top' => '5px'
                                                    ]
                                                ]) ?>
                                            </div>
                                            <div class="col-md-12">
                                                <?= Html::a('Отказать', Url::to([
                                                    '/client/communications/send',
                                                    'adressee_id' => $communicationExpert->getSenderId(),
                                                    'project_id' => $communicationExpert->getProjectId(),
                                                    'type' => CommunicationTypes::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT,
                                                    'triggered_communication_id' => $communicationExpert->getId()
                                                ]), [
                                                    'class' => 'btn btn-danger send-communication',
                                                    'id' => 'appoints_does_not_expert_project-'.$communicationExpert->getProjectId(),
                                                    'style' => [
                                                        'width' => '140px',
                                                        'font-size' => '18px',
                                                        'border-radius' => '8px',
                                                        'margin-top' => '10px'
                                                    ]
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">-----</div>

                                <?php endif; ?>

                            <?php else : ?>
                                <div class="col-md-2">-----</div>
                                <div class="col-md-1">-----</div>
                            <?php endif; ?>

                        <?php else : ?>
                            <div class="col-md-3">-----</div>
                            <div class="col-md-1">-----</div>
                            <div class="col-md-2">-----</div>
                            <div class="col-md-1">-----</div>
                        <?php endif; ?>

                    <?php endif; ?>

                </div>

                <?php if ($key !== array_key_last($userCommunications)) : ?>
                    <br>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>

    <?php endforeach; ?>

<?php else : ?>

    <h4 class="text-center" style="margin: 30px;">Коммуникации по данному проекту не найдены...</h4>

<?php endif; ?>