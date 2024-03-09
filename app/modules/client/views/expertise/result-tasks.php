<?php

use app\models\CommunicationTypes;
use app\models\Projects;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Сводная таблица назначений экспертов на проекты';
$this->registerCssFile('@web/css/expertise-result-tasks-style.css');

/**
 * @var $countProjects int
 * @var $countEnableProjects int
 * @var Projects[] $projects
 * @var Pagination $pages
 */

?>

<div class="row expertise-result-tasks">

    <div class="col-md-12" style="margin-bottom: 15px;">

        <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page open_modal_instruction_page',
            'title' => 'Инструкция', 'onclick' => 'return false'
        ]) ?>

    </div>

    <div class="col-xs-12">
        <div class="">Количество проектов: <?= $countProjects ?></div>
        <div class="">Количество проектов для назначения экспертизы: <?= $countEnableProjects ?></div>
    </div>

    <div class="col-md-12">

        <div class="row headers_expertise_result_tasks">
            <div class="col-md-4">Наименование проекта</div>
            <div class="col-md-2">Описание проекта</div>
            <div class="col-md-2">Отправлен запрос экспертам</div>
            <div class="col-md-2">Эксперты подтвердили участие</div>
            <div class="col-md-2">Эксперты назначены</div>
        </div>

        <?php foreach ($projects as $project): ?>

            <div class="row table_row_expertise_result_tasks showDataResultTaskExpertise" id="dataShortResultTaskExpertise-<?= $project->getId() ?>">
                <div class="col-md-4">
                    <?= $project->getProjectName() ?>
                    <?php if ($project->getDeletedAt()): ?>
                        <span class="color-red"> - проект удален</span>
                    <?php endif; ?>
                </div>
                <div class="col-md-2 text-center"><?= $project->getEnableExpertiseAt() ? '<span class="color-green bolder">' . date('d.m.Y', $project->getEnableExpertiseAt()) . '</span>' : Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) ?></div>
                <div class="col-md-2 text-center">
                    <?php $lastCommunication_mainAdminAsks = $project->getLastProjectCommunicationByType(CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE);
                    if ($lastCommunication_mainAdminAsks) {
                        $date = $project->getTargetDateAskExpert();
                        if ($lastCommunication_mainAdminAsks->getCreatedAt() > $date) {
                            echo '<span class="bolder">' . date('d.m.Y', $lastCommunication_mainAdminAsks->getCreatedAt()) . '</span><span class="color-red bolder"> ! </span>';
                        } else {
                            echo '<span class="color-green bolder">' . date('d.m.Y', $lastCommunication_mainAdminAsks->getCreatedAt()) . '</span>';
                        }
                    } elseif ($project->getEnableExpertiseAt()) {
                        $date = $project->getTargetDateAskExpert();
                        if (time() > $date) {
                            echo '<span class="color-red bolder">' . date('d.m.Y', $date) . '</span>';
                        } else {
                            echo '<span class="bolder">' . date('d.m.Y', $date) . '</span>';
                        }
                    } ?>
                </div>
                <div class="col-md-2 text-center">
                    <?php $lastCommunication_expertAnswer = null;
                    if ($lastCommunication_mainAdminAsks) {
                        $userAccessToProject = $lastCommunication_mainAdminAsks->userAccessToProject;
                        $lastCommunication_expertAnswer = $project->getLastProjectCommunicationByType(CommunicationTypes::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE);
                        if ($lastCommunication_expertAnswer) {
                            $isExpiredAccess = $userAccessToProject->getDateStop() < $lastCommunication_expertAnswer->getCreatedAt();
                            if (!$isExpiredAccess) {
                                echo '<span class="color-green bolder">' . date('d.m.Y', $userAccessToProject->getDateStop()) . '</span>';
                            } else {
                                echo '<span class="bolder">' . date('d.m.Y', $userAccessToProject->getDateStop()) . '</span><span class="color-red bolder"> ! </span>';
                            }
                        }
                        else {
                            $date = $userAccessToProject->getDateStop();
                            if (time() > $date) {
                                echo '<span class="color-red bolder">' . date('d.m.Y', $date) . '</span>';
                            } else {
                                echo '<span class="bolder">' . date('d.m.Y', $date) . '</span>';
                            }
                        }
                    } ?>
                </div>
                <div class="col-md-2 text-center">
                    <?php if ($lastCommunication_expertAnswer) {
                        $lastCommunication_mainAdminAppoints = $project->getLastProjectCommunicationByType(CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT);
                        $date = $project->getTargetDateAppointExpert();
                        if ($lastCommunication_mainAdminAppoints) {
                            if ($lastCommunication_mainAdminAppoints->getCreatedAt() > $date) {
                                echo '<span class="bolder">' . date('d.m.Y', $lastCommunication_mainAdminAppoints->getCreatedAt()) . '</span><span class="color-red bolder"> ! </span>';
                            } else {
                                echo '<span class="color-green bolder">' . date('d.m.Y', $lastCommunication_mainAdminAppoints->getCreatedAt()) . '</span>';
                            }
                        } elseif ($project->getEnableExpertiseAt()) {
                            if (time() > $date) {
                                echo '<span class="color-red bolder">' . date('d.m.Y', $date) . '</span>';
                            } else {
                                echo '<span class="bolder">' . date('d.m.Y', $date) . '</span>';
                            }
                        }
                    } elseif ($project->getEnableExpertiseAt()) {
                        $date = $project->getTargetDateAppointExpert();
                        if (time() > $date) {
                            echo '<span class="color-red bolder">' . date('d.m.Y', $date) . '</span>';
                        } else {
                            echo '<span class="bolder">' . date('d.m.Y', $date) . '</span>';
                        }
                    } ?>
                </div>
            </div>

            <div class="row rowDataResultTaskExpertise" id="dataResultTaskExpertise-<?= $project->getId() ?>"></div>

        <?php endforeach; ?>

        <div class="pagination-admin-projects-result">
            <?= LinkPager::widget([
                'pagination' => $pages,
                'activePageCssClass' => 'pagination_active_page',
                'options' => ['class' => 'admin-projects-result-pagin-list'],
            ]) ?>
        </div>

    </div>
</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/expertise_result_tasks.js'); ?>
