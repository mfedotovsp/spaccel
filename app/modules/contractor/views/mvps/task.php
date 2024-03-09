<?php

use app\models\ConfirmGcp;
use app\models\ContractorTasks;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Разработка MVP';
$this->registerCssFile('@web/css/mvp-index-style.css');

/**
 * @var ContractorTasks $task
 * @var ConfirmGcp $confirmGcp
 * @var Gcps $gcp
 * @var Problems $problem
 * @var Segments $segment
 * @var Projects $project
 * @var Mvps[] $models
 * @var bool $existTrashList
 * @var Mvps[] $trashList
 * @var FormTaskComplete $formTaskComplete
 */

?>

<div class="mvp-index">

    <div class="header-title-index-mobile">
        <div style="overflow: hidden; max-width: 70%;">Проект: <?= $project->getProjectName() ?></div>
        <div class="buttons-project-menu-mobile" style="position: absolute; right: 20px; top: 5px;">
            <?= Html::img('@web/images/icons/icon-four-white-squares.png', ['class' => 'open-project-menu-mobile', 'style' => ['width' => '30px']]) ?>
            <?= Html::img('@web/images/icons/icon-white-cross.png', ['class' => 'close-project-menu-mobile', 'style' => ['width' => '30px', 'display' => 'none']]) ?>
        </div>
    </div>

    <div class="project-menu-mobile">
        <div class="project_buttons_mobile flex-column">

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
                    'margin' => '10px 2% 5px 2%',
                ],
            ]) ?>

            <?= Html::a('Описание сегмента', ['/segments/show-all-information', 'id' => $segment->getId()], [
                'class' => 'btn btn-default openAllInformationSegment',
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
                    'margin' => '5px 2% 10px 2%',
                ],
            ]) ?>

        </div>

        <div class="task-block">
            <div>
                    <span class="task-header">
                        Проблема:
                    </span>
                    <span class="task-description">
                        <?= $problem->getDescription() ?>
                    </span>
            </div>
            <div>
                    <span class="task-header">
                        ЦП:
                    </span>
                    <span class="task-description">
                        <?= $gcp->getDescription() ?>
                    </span>
            </div>
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
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage active"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
    </div>

    <div class="arrow_links_router_mobile">
        <div class="arrow_link_router_mobile_left"></div>
        <div class="text-stage">7/9. Разработка MVP</div>
        <div class="arrow_link_router_mobile_right"></div>
    </div>

    <div class="row block_description_stage">
        <div>Наименование сегмента:</div>
        <div><?= $segment->getName() ?></div>
        <div>Формулировка проблемы:</div>
        <div><?= $problem->getDescription() ?></div>
        <div>Формулировка ценностного предложения:</div>
        <div><?= $gcp->getDescription() ?></div>
    </div>


    <div class="container-fluid container-data_for_user row">

        <div class="row row_header_data_generation" style="margin: 5px 15px 0 15px; padding: 10px; border-bottom: 1px solid #ccc;">
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

                <div class="row mt-10">
                    <div class="col-md-12">
                        <span class="task-header">История изменения статусов:</span>
                    </div>

                    <?php foreach ($histories as $key => $history): ?>

                        <div class="col-md-12 font-size-14 pt-5">
                            <span class="bolder">№ <?= ($key+1) ?>. </span>
                            <span><?= date('d.m.Y H:i:s', $history->getCreatedAt()) ?> | </span>
                            <span class="text-danger"><?= ContractorTasks::statusToString($history->getOldStatus()) ?> >>> </span>
                            <span class="text-success"><?= ContractorTasks::statusToString($history->getNewStatus()) ?></span>
                            <span><?= $history->getComment() ? '( ' . $history->getComment() . ' )' : '' ?></span>
                        </div>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

            <div class="row mt-10">
                <div class="col-md-12">
                    <span class="task-header">
                        Проект:
                    </span>
                    <span class="task-description">
                        <?= $project->getProjectName() ?>
                    </span>
                    <span class="pl-5">
                        <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px', 'margin-bottom' => '5px']]),
                            ['/projects/show-all-information', 'id' => $project->getId()], [
                                'class' => 'openAllInformationProject', 'title' => 'Смотреть описание проекта',
                            ]) ?>
                    </span>
                </div>
            </div>

            <div class="row mt-10">
                <div class="col-md-12">
                    <span class="task-header">
                        Сегмент:
                    </span>
                    <span class="task-description">
                        <?= $segment->getName() ?>
                    </span>
                    <span class="pl-5">
                        <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px', 'margin-bottom' => '5px']]),
                            ['/segments/show-all-information', 'id' => $segment->getId()], [
                                'class' => 'openAllInformationSegment', 'title' => 'Смотреть описание сегмента',
                            ]) ?>
                    </span>
                </div>
            </div>

            <div class="row mt-10">
                <div class="col-md-12">
                    <span class="task-header">
                        Проблема:
                    </span>
                    <span class="task-description">
                        <?= $problem->getDescription() ?>
                    </span>
                </div>
            </div>

            <div class="row mt-10">
                <div class="col-md-12">
                    <span class="task-header">
                        ЦП:
                    </span>
                    <span class="task-description">
                        <?= $gcp->getDescription() ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="row row_header_data_generation" style="margin-left: 10px; margin-right: 10px; border-bottom: 1px solid #ccc;">

            <div class="col-md-8" style="padding-top: 17px; padding-bottom: 17px;">
                <?= Html::a('Продукты MVP' . Html::img('/images/icons/icon_report_next.png'), ['/mvps/get-instruction'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

            <?php if (!$confirmGcp->getDeletedAt()): ?>

                <?php if ($existTrashList): ?>

                    <div class="col-md-2" style="padding-top: 15px; padding-bottom: 15px;">
                        <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                            <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый MVP</div></div>',
                                ['/contractor/confirm-gcp/data-availability-for-next-step', 'id' => $task->getId()],
                                ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus pull-right']
                            ) ?>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-2" style="padding-top: 15px; padding-bottom: 15px;">
                        <?=  Html::a( '<div class="hypothesis_trash_link_block"><div>' . Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div><div style="padding-left: 20px;">Корзина</div></div>',
                            ['/contractor/mvps/trash-list', 'id' => $task->getId()],
                            ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right']
                        ) ?>
                    </div>

                <?php else: ?>

                    <div class="col-md-4" style="padding-top: 15px; padding-bottom: 15px;">
                        <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                            <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый MVP</div></div>',
                                ['/contractor/confirm-gcp/data-availability-for-next-step', 'id' => $task->getId()],
                                ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus pull-right']
                            ) ?>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <div class="col-md-4"></div>

            <?php endif; ?>

        </div>

        <?php if (!$confirmGcp->getDeletedAt()): ?>

            <div class="row row_header_data_generation_mobile">
                <div class="col-xs-7">
                    <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>

                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый MVP</div></div>',
                            ['/contractor/confirm-gcp/data-availability-for-next-step', 'id' => $task->getId()],
                            ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus']
                        ) ?>

                    <?php endif; ?>
                </div>
                <div class="col-xs-5">

                    <?php if ($existTrashList): ?>

                        <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                            Url::to('/mvps/get-instruction'), [
                                'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                                'style' => ['margin-top' => '5px'],
                                'title' => 'Инструкция'
                            ]) ?>

                        <?=  Html::a('<div class="hypothesis_trash_link_block"><div>' .  Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div></div>',
                            ['/contractor/mvps/trash-list', 'id' => $task->getId()], ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right', 'title' => 'Корзина']
                        ) ?>

                    <?php else: ?>

                        <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                            Url::to('/mvps/get-instruction'), [
                                'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                                'title' => 'Инструкция'
                            ]) ?>

                    <?php endif; ?>

                </div>
            </div>

        <?php endif; ?>


        <!--Заголовки для списка MVP-->
        <div class="row headers_data_hypothesis" style="margin: 0; padding: 10px;">

            <div class="col-lg-1 ">
                <div class="row">
                    <div class="col-lg-4" style="padding: 0;"></div>
                    <div class="col-lg-8" style="padding: 0;">Обознач.</div>
                </div>

            </div>

            <div class="col-lg-8" style="padding-left: 10px;">Описание минимально жизнеспособного продукта</div>

            <div class="col-lg-1 text-center"><div>Дата создания</div></div>

            <div class="col-lg-1 text-center header_date_confirm"><div>Дата подтв.</div></div>

            <div class="col-lg-1"></div>

        </div>


        <div class="block_all_hypothesis row" style="padding-left: 10px; padding-right: 10px;">

            <?php if (!$confirmGcp->getDeletedAt()): ?>

                <?= $this->render('_index_ajax', [
                    'task' => $task, 'models' => $models, 'formTaskComplete' => $formTaskComplete,
                ]) ?>

            <?php else: ?>

                <?= $this->render('_trash_index_ajax', [
                    'trashList' => $trashList
                ]) ?>

            <?php endif; ?>

        </div>
    </div>


    <?php if (count($models) > 0) : ?>

        <div class="row information_status_confirm">

            <div>

                <div style="display:flex; align-items: center;">
                    <?= Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>MVP подтвержден</div>
                </div>

                <div style="display:flex; align-items: center;">
                    <?= Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>MVP не подтвержден</div>
                </div>

                <div style="display:flex; align-items: center;">
                    <?= Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>MVP ожидает подтверждения</div>
                </div>

            </div>

        </div>

    <?php endif; ?>


    <!--Модальные окна-->
    <?= $this->render('modal') ?>

</div>

<!--Подключение скриптов-->
<?php
$this->registerJsFile('@web/js/hypothesis_mvp_index.js');
?>
