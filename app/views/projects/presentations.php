<?php

use app\models\Projects;
use yii\helpers\Html;
use app\models\User;

$this->title = 'Презентации';
$this->registerCssFile('@web/css/profile-data-style.css');

/**
 * @var User $user
 * @var Projects[] $projects
 */

?>

<div class="profile-result">

    <div class="row profile_menu">

        <?= Html::a('Проекты', ['/projects/index', 'id' => $user->getId()], [
            'class' => 'link_in_the_header',
        ]) ?>

        <?= Html::a('Сводные таблицы', ['/projects/results', 'id' => $user->getId()], [
            'class' => 'link_in_the_header',
        ]) ?>

        <?= Html::a('Трэкшн карты', ['/projects/roadmaps', 'id' => $user->getId()], [
            'class' => 'link_in_the_header',
        ]) ?>

        <?= Html::a('Протоколы', ['/projects/reports', 'id' => $user->getId()], [
            'class' => 'link_in_the_header',
        ]) ?>

        <?= Html::a('Презентации', ['/projects/presentations', 'id' => $user->getId()], [
            'class' => 'link_in_the_header',
        ]) ?>

    </div>

    <div class="container-fluid row">
        <div class="row" style="margin-left: 10px; margin-right: 10px; border-bottom: 1px solid #ccc;">
            <div class="col-md-3" style="padding: 2px 0;">
                <?= Html::a('Презентации' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция', 'onclick' => 'return false;'
                ]) ?>
            </div>
        </div>
    </div>

    <?php if ($projects) : ?>

        <?php foreach ($projects as $project) : ?>

            <div id="result-<?= $project->getId() ?>">

                <div class="container-one_hypothesis">

                    <div class="col-md-8">
                        <div class="project_name_table">
                            <?= $project->getProjectName() ?> -<span class="project_fullname_text"><?= $project->getProjectFullname() ?></span>
                        </div>
                    </div>

                    <div class="col-md-4 informationAboutAction">
                        Посмотреть презентацию проекта
                    </div>

                </div>

                <div class="hereAddDataOfProject"></div>

            </div>

        <?php endforeach; ?>

    <?php else : ?>

        <h3 class="text-center">Пока нет проектов...</h3>

        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

            <div class="text-center">
                <?= Html::a('Создать проект', ['/projects/index', 'id' => Yii::$app->user->getId()],[
                    'class' => 'btn btn-default',
                    'style' => [
                        'background' => '#E0E0E0',
                        'color' => '4F4F4F',
                        'border-radius' => '8px',
                        'width' => '220px',
                        'height' => '40px',
                        'font-size' => '16px',
                        'font-weight' => '700'
                    ]
                ]) ?>
            </div>

        <?php endif; ?>
    <?php endif; ?>

</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/profile_presentation.js'); ?>
