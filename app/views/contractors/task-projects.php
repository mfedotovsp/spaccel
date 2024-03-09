<?php

use app\models\Projects;
use yii\helpers\Html;
use app\models\User;

$this->title = 'Задания по проектам';
$this->registerCssFile('@web/css/notifications-style.css');

/**
 * @var User $contractor
 * @var Projects[] $projects
 */

?>

<div class="task-projects">

    <div class="row" style="margin-top: 35px; margin-bottom: 35px; padding-left: 25px; padding-right: 25px;">

        <div class="col-md-12">
            <?= Html::a('Задания для исполнителя ' . $contractor->getUsername() . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>

    </div>

    <?php if (count($projects) > 0): ?>

        <div class="allHypothesis">

            <?php foreach ($projects as $project) : ?>

                <div class="hypothesis" id="hypothesis-<?= $project->getId() ?>">

                    <div class="container-one_hypothesis">

                        <div class="col-md-8">
                            <div class="project_name_table">
                                <?= $project->getProjectName() ?> -<span class="project_fullname_text"><?= $project->getProjectFullname() ?></span>
                            </div>
                        </div>

                        <div class="col-md-4 informationAboutAction">
                            Посмотреть задания по проекту
                        </div>

                    </div>

                    <div class="hereAddProjectTasks"></div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="text-center mt-30">Задания по проектам отсутствуют...</div>

    <?php endif; ?>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/contractor_tasks.js'); ?>
