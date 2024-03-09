<?php

use app\models\Projects;
use app\models\User;
use yii\helpers\Html;

$this->title = 'Уведомления';
$this->registerCssFile('@web/css/notifications-style.css');

/**
 * @var Projects[] $projects
 * @var User $user
 */

?>

<div class="row" style="margin-top: 25px; margin-bottom: 25px; padding-left: 25px; padding-right: 25px;">
    <div class="col-md-12">
        <?= Html::a('Уведомления по проектам ' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page open_modal_instruction_page',
            'title' => 'Инструкция', 'onclick' => 'return false'
        ]) ?>
    </div>
</div>

<div class="row page-contractor-notifications">

    <div class="col-md-12 notifications-content">

        <?php if ($projects) : ?>

            <?php foreach ($projects as $key => $project) : ?>

                <div id="communications_project-<?= $project->getId() ?>">

                    <div class="container-one_hypothesis">

                        <div class="col-md-9 col-lg-10">
                            <div class="project_name_table">

                                <?= $project->getProjectName() ?> -<span class="project_fullname_text"><?= $project->getProjectFullname() ?></span>

                                <?php if ($countUnreadCommunications = $user->getCountUnreadCommunicationsByProject($project->getId())) : ?>
                                    <div class="countUnreadCommunicationsByProject active pull-left"><?= $countUnreadCommunications ?></div>
                                <?php endif; ?>

                            </div>
                        </div>

                        <div class="col-md-3 col-lg-2 informationAboutAction">
                            <b>Автор проекта:</b> <?= $project->user->getUsername() ?>
                        </div>

                    </div>

                    <!--Блок для вывода уведомлений по проекту (коммуникаций)-->
                    <div class="hereAddProjectCommunications"></div>

                </div>

            <?php endforeach; ?>

        <?php else : ?>

            <h3 class="text-center">У вас пока нет уведомлений...</h3>

        <?php endif; ?>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/contractor_notifications.js'); ?>