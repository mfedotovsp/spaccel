<?php

use app\models\Projects;
use app\models\User;
use yii\helpers\Html;

$this->title = 'Проекты';
$this->registerCssFile('@web/css/contractor-projects-style.css');

/**
 * @var Projects[] $projects
 * @var User $user
 */

?>

<div class="row" style="margin-top: 25px; margin-bottom: 25px; padding-left: 25px; padding-right: 25px;">
    <div class="col-md-12">
        <?= Html::a('Задания по проектам ' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page open_modal_instruction_page',
            'title' => 'Инструкция', 'onclick' => 'return false'
        ]) ?>
    </div>
</div>

<div class="row page-projects">

    <div class="col-md-12 projects-content">

        <?php if ($projects) : ?>

            <?php foreach ($projects as $key => $project) : ?>

                <div id="data_project-<?= $project->getId() ?>">

                    <div class="container-one_project">

                        <div class="col-md-9 col-lg-10">
                            <?php if ($project->getDeletedAt()): ?>
                                <div class="text-danger bolder pl-5">
                                    Проект удалён
                                </div>
                            <?php endif; ?>
                            <div class="project_name_table">
                                <?= $project->getProjectName() ?> -<span class="project_fullname_text"><?= $project->getProjectFullname() ?></span>
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-2 informationAboutAction">
                            <b>Автор проекта:</b> <?= $project->user->getUsername() ?>
                        </div>

                    </div>

                    <!--Блок для вывода заданий по проекту-->
                    <div class="row container-fluid">
                        <div class="col-md-12">
                            <div class="hereAddProjectData"></div>
                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        <?php else : ?>

            <h3 class="text-center">У вас пока нет проектов...</h3>

        <?php endif; ?>

    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/contractor_projects.js'); ?>
