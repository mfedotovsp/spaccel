<?php

use app\models\Projects;
use app\models\User;

$this->title = 'Уведомления';
$this->registerCssFile('@web/css/notifications-style.css');

/**
 * @var Projects[] $projects
 * @var User $user
 */

?>

<div class="row page-notifications">

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
<?php $this->registerJsFile('@web/js/expert_notifications.js'); ?>