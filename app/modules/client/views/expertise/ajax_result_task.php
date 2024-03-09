<?php

use app\models\CommunicationResponse;
use app\models\CommunicationTypes;
use app\models\ExpertType;
use app\models\ProjectCommunications;
use app\models\Projects;

/**
 * @var Projects $project
 * @var ProjectCommunications[] $communicationsMainAdminAskExpert
 * @var ProjectCommunications[] $communicationsMainAdminAppointExpert
 */

?>

<div class="col-md-12">

    <div class="mb-5">
        <span class="bb-1">Описание проекта</span>
    </div>

    <div>
        <span>Полное наименование проекта:</span>
        <span><?= $project->getProjectFullname() ?></span>
    </div>
    <div>
        <span>Проектант:</span>
        <span><?= $project->user->getUsername() ?></span>
    </div>

    <?php if ($project->getEnableExpertiseAt()): ?>

        <div>
            <span>Проектант разрешил экспертизу:</span>
            <span><?= date('d.m.Y H:i', $project->getEnableExpertiseAt()) ?></span>
        </div>

        <div class="mt-10 mb-5">
            <span class="bb-1">Отправлены запросы экспертам</span>
        </div>

        <div>
            <span>Запросы экспертам необходимо отправить до:</span>
            <span><?= date('d.m.Y H:i', $project->getTargetDateAskExpert()) ?></span>
        </div>

        <?php if ($communicationsMainAdminAskExpert): ?>

            <div class="row tableHeadersDataItemResultTask">
                <div class="col-md-4">Эксперт</div>
                <div class="col-md-4">Дата, время запроса</div>
                <div class="col-md-4">Отмена запроса</div>
            </div>

            <?php foreach ($communicationsMainAdminAskExpert as $communication): ?>

                <div class="row tableDataItemResultTask">
                    <div class="col-md-4"><?= $communication->expert->getUsername() ?></div>
                    <div class="col-md-4"><?= date('d.m.Y H:i', $communication->getCreatedAt()) ?></div>
                    <div class="col-md-4">
                        <?php if ($communication->getCancel() === ProjectCommunications::CANCEL_TRUE) {
                            echo '<span class="color-red">Да</span>';
                        } else {
                            echo '<span>Нет</span>';
                        } ?>
                    </div>
                </div>

            <?php endforeach; ?>

            <div class="mt-10 mb-5">
                <span class="bb-1">Ответы экспертов на запросы</span>
            </div>

            <div class="row tableHeadersDataItemResultTask">
                <div class="col-md-3">Эксперт</div>
                <div class="col-md-3">Макс. дата, время ответа</div>
                <div class="col-md-3">Дата, время ответа</div>
                <div class="col-md-3">Ответ</div>
            </div>

            <?php foreach ($communicationsMainAdminAskExpert as $communication): ?>

                <?php $communicationResponsive = $communication->responsiveCommunication ?>

                <div class="row tableDataItemResultTask">
                    <div class="col-md-3"><?= $communication->expert->getUsername() ?></div>
                    <div class="col-md-3"><?= date('d.m.Y H:i', $communication->userAccessToProject->getDateStop()) ?></div>
                    <div class="col-md-3">
                        <?php if ($communicationResponsive) {
                            echo date('d.m.Y H:i', $communicationResponsive->getCreatedAt());
                        } ?>
                    </div>
                    <div class="col-md-3">
                        <?php if ($communicationResponsive && $communicationResponse = $communicationResponsive->communicationResponse) {
                            if ($communicationResponse->getAnswer() === CommunicationResponse::POSITIVE_RESPONSE) {
                                echo '<span class="color-green">Готов(-а) провести экспертизу проекта</span>';
                            } elseif ($communicationResponse->getAnswer() === CommunicationResponse::NEGATIVE_RESPONSE) {
                                echo '<span class="color-red">Не готов(-а) провести экспертизу проекта</span>';
                            }
                        } ?>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php else: ?>
            <div class="row">
                <div class="col-md-12 color-red">Запросы пока отсутствуют</div>
            </div>
        <?php endif; ?>

        <div class="mt-10 mb-5">
            <span class="bb-1">Назначенные на проект эксперты</span>
        </div>

        <div>
            <span>Назначить экспертов на проект необходимо до:</span>
            <span><?= date('d.m.Y H:i', $project->getTargetDateAppointExpert()) ?></span>
        </div>

        <?php if ($communicationsMainAdminAskExpert && $communicationsMainAdminAppointExpert): ?>

            <div class="row tableHeadersDataItemResultTask">
                <div class="col-md-3">Эксперт</div>
                <div class="col-md-3">Дата, время</div>
                <div class="col-md-3">Типы деятельности</div>
                <div class="col-md-3">Отозван с проекта</div>
            </div>

            <?php foreach ($communicationsMainAdminAppointExpert as $communication): ?>

                <?php $communicationResponsive = $communication->responsiveCommunication ?>

                <div class="row tableDataItemResultTask">
                    <div class="col-md-3"><?= $communication->expert->getUsername() ?></div>
                    <div class="col-md-3"><?= date('d.m.Y H:i', $communication->getCreatedAt()) ?></div>
                    <div class="col-md-3"><?= ExpertType::getContent($communication->typesAccessToExpertise->getTypes()) ?></div>
                    <div class="col-md-3">
                        <?php if ($communicationResponsive && $communicationResponsive->getType() === CommunicationTypes::MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT) {
                            echo '<span class="color-red">Да</span>';
                        } else {
                            echo '<span>Нет</span>';
                        } ?>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    <?php endif; ?>

</div>
