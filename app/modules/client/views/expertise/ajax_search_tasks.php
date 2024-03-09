<?php

use app\models\Projects;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/**
 * @var Projects[] $projects
 * @var Pagination $pages
 * @var bool $search
 */

?>

<?php if ($projects) : ?>

    <?php foreach ($projects as $project) : ?>

        <div id="expertise_task-<?= $project->getId()?>">

            <div class="container-one_hypothesis">

                <div class="col-md-9 col-lg-10">
                    <div class="project_name_table">
                        <?= $project->getProjectName() ?> -<span class="project_fullname_text"><?= $project->getProjectFullname() ?></span>
                        <?php if ($project->getDeletedAt()): ?>
                            <p class="color-red">проект удален</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-3 col-lg-2 informationAboutAction">
                    <b>Автор проекта:</b> <?= $project->user->getUsername() ?>
                </div>

            </div>

            <div class="hereAddDataOfProject">
                <!--Меню по экспертизам проекта-->
                <div class="block-links-menu-tasks">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <?= Html::a('Сводная таблица проекта', ['/client/expertise/get-project-summary-table', 'id' => $project->getId()], ['class' => 'link-menu-tasks']) ?>
                        </div>
                        <div class="col-md-3 text-center">
                            <?= Html::a('Поиск экспертов', ['/client/expertise/get-search-form-experts', 'id' => $project->getId()], ['class' => 'link-menu-tasks']) ?>
                        </div>
                        <div class="col-md-3 text-center">
                            <?= Html::a('Коммуникации', ['/client/communications/get-communications', 'id' => $project->getId()], ['class' => 'link-menu-tasks']) ?>
                        </div>
                        <div class="col-md-3 text-center">
                            <?= Html::a('Экспертизы', ['/client/expertise/get-expertise-by-project', 'id' => $project->getId()], ['class' => 'link-menu-tasks']) ?>
                        </div>
                    </div>
                </div>

                <!--Блок для вывода контента меню-->
                <div class="block-tasks-content"></div>
            </div>

        </div>

    <?php endforeach; ?>

    <div class="pagination-admin-projects-result">
        <?= LinkPager::widget([
            'pagination' => $pages,
            'activePageCssClass' => 'pagination_active_page',
            'options' => ['class' => 'admin-projects-result-pagin-list'],
        ]) ?>
    </div>

<?php else : ?>

    <?php if ($search) : ?>
        <h3 class="text-center">По вашему запросу ничего не найдено...</h3>
    <?php else : ?>
        <h3 class="text-center">Пока нет проектов...</h3>
    <?php endif; ?>

<?php endif; ?>