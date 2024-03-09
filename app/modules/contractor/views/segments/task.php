<?php

use app\models\ContractorTasks;
use app\models\Projects;
use app\models\Segments;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Генерация гипотез целевых сегментов';
$this->registerCssFile('@web/css/segments-index-style.css');

/**
 * @var Projects $project
 * @var Segments[] $models
 * @var ContractorTasks $task
 * @var bool $existTrashList
 * @var Segments[] $trashList
 * @var FormTaskComplete $formTaskComplete
 */

?>

<div class="segment-index">

    <div class="header-title-index-mobile">
        <div style="overflow: hidden; max-width: 70%;">Проект: <?= $project->getProjectName() ?></div>
        <div class="buttons-project-menu-mobile" style="position: absolute; right: 20px; top: 5px;">
            <?= Html::img('@web/images/icons/icon-four-white-squares.png', ['class' => 'open-project-menu-mobile', 'style' => ['width' => '30px']]) ?>
            <?= Html::img('@web/images/icons/icon-white-cross.png', ['class' => 'close-project-menu-mobile', 'style' => ['width' => '30px', 'display' => 'none']]) ?>
        </div>
    </div>

    <div class="project-menu-mobile">
        <div class="project_buttons_mobile">

            <?= Html::a('Презентация проекта', ['/projects/presentation-mobile', 'id' => $project->getId()], [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'width' => '96%',
                    'height' => '36px',
                    'background' => '#7F9FC5',
                    'color' => '#FFFFFF',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'border-radius' => '0',
                    'border' => '1px solid #ffffff',
                    'font-size' => '18px',
                    'margin' => '10px 2% 10px 2%',
                ],
            ]) ?>

        </div>
        <div class="task-block">
            <div>
                    <span class="task-header">
                        Описание задания:
                    </span>
                <span class="task-description">
                        <?= $task->getDescription() ?>
                    </span>
            </div>
            <div>
                    <span class="task-header">
                        Статус:
                    </span>
                <span class="task-description">
                        <?= $task->getStatusToString() ?>
                    </span>
            </div>
            <div>
                    <span class="task-header">
                        Создано:
                    </span>
                <span class="task-description">
                        <?= date('d.m.Y', $task->getCreatedAt()) ?>
                    </span>
            </div>

            <?php if ($histories = $task->histories): ?>

                <div class="mt-15">
                    <div class="task-header">
                        История изменения статуса задания:
                    </div>

                    <?php foreach ($histories as $key => $history): ?>
                        <div class="mt-5">
                            <div class="task-header">№ <?= ($key+1) ?></div>
                            <div>
                                <span class="task-description">Дата и время: </span>
                                <span class="task-description">
                                    <?= date('d.m.Y H:i:s', $history->getCreatedAt()) ?>
                                </span>
                            </div>
                            <div>
                                <span class="task-description">Изменение: </span>
                                <span class="text-danger"><?= ContractorTasks::statusToString($history->getOldStatus()) ?> >>> </span>
                                <span class="text-success"><?= ContractorTasks::statusToString($history->getNewStatus()) ?></span>
                            </div>
                            <div>
                                <span class="task-description">Комментарий: </span>
                                <span class="task-description"><?= $history->getComment() ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>

    <div class="arrow_stages_project_mobile">
        <div class="item-stage active"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
    </div>

    <div class="arrow_links_router_mobile">
        <div class="arrow_link_router_mobile_left"></div>
        <div class="text-stage">1/9. Генерация гипотез целевых сегментов</div>
        <div class="arrow_link_router_mobile_right"></div>
    </div>

    <?php if (!$project->getDeletedAt()): ?>

        <div class="row row_header_data_generation_mobile">

            <div class="col-xs-7">
                <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                    <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый сегмент</div></div>', ['/contractor/segments/get-hypothesis-to-create', 'id' => $task->getId()],
                        ['id' => 'showHypothesisToCreate', 'class' => 'new_hypothesis_link_plus']
                    ) ?>
                <?php endif; ?>
            </div>

            <div class="col-xs-5">

                <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                    Url::to('/segments/get-instruction'), [
                        'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                        'title' => 'Инструкция', 'style' => ['margin-top' => '5px']
                    ]) ?>

                <?php if ($existTrashList): ?>
                    <?=  Html::a('<div class="hypothesis_trash_link_block"><div>' .  Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div></div>',
                        ['/contractor/segments/trash-list', 'id' => $task->getId()], ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right', 'title' => 'Корзина']
                    ) ?>
                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

    <div class="container-fluid container-data row">

        <div class="row row_header_data_generation">
            <div class="row">
                <div class="col-md-8">
                    <span class="task-header">
                        Описание задания:
                    </span>
                    <span class="task-description">
                        <?= $task->getDescription() ?>
                    </span>
                </div>
                <div class="col-md-2">
                    <span class="task-header">
                        Статус:
                    </span>
                    <span class="task-description">
                        <?= $task->getStatusToString() ?>
                    </span>
                </div>
                <div class="col-md-2">
                    <span class="task-header">
                        Создано:
                    </span>
                    <span class="task-description">
                        <?= date('d.m.Y', $task->getCreatedAt()) ?>
                    </span>
                </div>
            </div>

            <?php if ($histories = $task->histories): ?>

                <div class="row">
                    <div class="col-md-12">
                        <span class="task-header">История изменения статусов:</span>
                    </div>

                    <?php foreach ($histories as $key => $history): ?>

                        <div class="col-md-12 font-size-14">
                            <span class="bolder">№ <?= ($key+1) ?>. </span>
                            <span><?= date('d.m.Y H:i:s', $history->getCreatedAt()) ?> | </span>
                            <span class="text-danger"><?= ContractorTasks::statusToString($history->getOldStatus()) ?> >>> </span>
                            <span class="text-success"><?= ContractorTasks::statusToString($history->getNewStatus()) ?></span>
                            <span><?= $history->getComment() ? '( ' . $history->getComment() . ' )' : '' ?></span>
                        </div>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <span class="task-header">
                        Проект:
                    </span>
                    <span class="task-description">
                        <?= $project->getProjectName() ?>
                    </span>
                    <span class="pl-5">
                        <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px']]),
                            ['/projects/show-all-information', 'id' => $project->getId()], [
                                'class' => 'openAllInformationProject', 'title' => 'Смотреть описание проекта',
                            ]) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="row row_header_data_generation">
            <div class="col-md-7 pt-2 pb-2" style="padding-left: 0;">
                <?= Html::a('Сегменты' . Html::img('/images/icons/icon_report_next.png'), ['/segments/get-instruction'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

            <?php if ($existTrashList): ?>

                <div class="col-md-3 p-0">
                    <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && !$project->getDeletedAt() && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Новый сегмент</div></div>', ['/contractor/segments/get-hypothesis-to-create', 'id' => $task->getId()],
                            ['id' => 'showHypothesisToCreate', 'class' => 'new_hypothesis_link_plus pull-right']
                        ) ?>
                    <?php endif; ?>
                </div>

                <div class="col-md-2 p-0">
                    <?php if (!$project->getDeletedAt()): ?>
                        <?=  Html::a( '<div class="hypothesis_trash_link_block"><div>' . Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div><div style="padding-left: 20px;">Корзина</div></div>',
                            ['/contractor/segments/trash-list', 'id' => $task->getId()],
                            ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right']
                        ) ?>
                    <?php endif; ?>
                </div>

            <?php else: ?>

                <div class="col-md-5 p-0">
                    <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && !$project->getDeletedAt() && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Новый сегмент</div></div>', ['/contractor/segments/get-hypothesis-to-create', 'id' => $task->getId()],
                            ['id' => 'showHypothesisToCreate', 'class' => 'new_hypothesis_link_plus pull-right']
                        ) ?>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>

        <!--Заголовки для списка сегментов-->
        <div class="row all_headers_data_hypothesis">

            <div class="col-lg-4 headers_data_hypothesis_hi">
                <div class="row">
                    <div class="col-md-1" style="padding: 0;"></div>
                    <div class="col-md-11">Наименование сегмента</div>
                </div>
            </div>

            <div class="col-lg-1 headers_data_hypothesis_hi text-center">Тип</div>
            <div class="col-lg-2 headers_data_hypothesis_hi text-center">Сфера деятельности</div>
            <div class="col-lg-2 headers_data_hypothesis_hi text-center">Вид / специализация деятельности</div>

            <div class="col-lg-2 text-center">

                <div class="headers_data_hypothesis_hi">
                    Платеже&shy;способность
                </div>
                <div class="headers_data_hypothesis_low">
                    млн. руб./год
                </div>
            </div>

            <div class="col-lg-1"></div>
        </div>

        <div class="block_all_hypothesis row pl-10 pr-10">

            <!--Данные для списка сегментов-->
            <?php if (!$project->getDeletedAt()): ?>

                <?= $this->render('_index_ajax',
                    ['task' => $task, 'models' => $models, 'formTaskComplete' => $formTaskComplete,
                ]) ?>

            <?php else: ?>

                <?= $this->render('_trash_index_ajax', [
                    'trashList' => $trashList
                ]) ?>

            <?php endif; ?>

        </div>

        <!--Модальные окна-->
        <?= $this->render('modal') ?>
        
    </div>

    <?php if (count($models) > 0) : ?>

        <div class="row information_status_confirm">

            <div>

                <div class="display-flex align-items-center">
                    <?= Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>Сегмент подтвержден</div>
                </div>

                <div class="display-flex align-items-center">
                    <?= Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>Сегмент не подтвержден</div>
                </div>

                <div class="display-flex align-items-center">
                    <?= Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>Сегмент ожидает подтверждения</div>
                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<!--Подключение скриптов-->
<?php
$this->registerJsFile('@web/js/hypothesis_segment_index.js');
?>