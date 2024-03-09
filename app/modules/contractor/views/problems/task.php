<?php

use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\forms\FormCreateProblem;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Генерация гипотез проблем сегмента';
$this->registerCssFile('@web/css/problem-index-style.css');

/**
 * @var ContractorTasks $task
 * @var Problems[] $models
 * @var ConfirmSegment $confirmSegment
 * @var Segments $segment
 * @var Projects $project
 * @var FormCreateProblem $formModel
 * @var FormTaskComplete $formTaskComplete
 * @var bool $existTrashList
 * @var Problems[] $trashList
 */

?>

<div class="generation-problem-index">

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
        <div class="item-stage active"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
    </div>

    <div class="arrow_links_router_mobile" style="margin-bottom: 5px;">
        <div class="arrow_link_router_mobile_left"></div>
        <div class="text-stage">3/9. Генерация гипотез проблем сегментов</div>
        <div class="arrow_link_router_mobile_right"></div>
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
                                'class' => 'openAllInformationSegment', 'title' => 'Смотреть описание проекта',
                            ]) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="row row_header_data_generation pt-5 pb-5" style="margin: 0 15px; border-bottom: 1px solid #ccc;">

            <div class="col-md-6 pt-2 pl-10">
                <?= Html::a('Проблемы' . Html::img('/images/icons/icon_report_next.png'), ['/problems/get-instruction'],[
                    'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                ]) ?>
            </div>

            <?php if (!$confirmSegment->getDeletedAt()): ?>

                <?php if ($existTrashList): ?>

                    <div class="col-md-4">
                        <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                            <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новая проблема</div></div>',
                                ['/contractor/confirm-segment/data-availability-for-next-step', 'id' => $task->getId()],
                                ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus pull-right']
                            ) ?>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-2">
                        <?=  Html::a( '<div class="hypothesis_trash_link_block"><div>' . Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div><div style="padding-left: 20px;">Корзина</div></div>',
                            ['/contractor/problems/trash-list', 'id' => $task->getId()],
                            ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right']
                        ) ?>
                    </div>

                <?php else: ?>

                    <div class="col-md-6">
                        <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                            <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новая проблема</div></div>',
                                ['/contractor/confirm-segment/data-availability-for-next-step', 'id' => $task->getId()],
                                ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus pull-right']
                            ) ?>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <div class="col-md-6"></div>

            <?php endif; ?>

        </div>

        <?php if (!$confirmSegment->getDeletedAt()): ?>

            <div class="row row_header_data_generation_mobile">
                <div class="col-xs-7">
                    <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новая проблема</div></div>',
                            ['/contractor/confirm-segment/data-availability-for-next-step', 'id' => $task->getId()],
                            ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus']
                        ) ?>
                    <?php endif; ?>
                </div>
                <div class="col-xs-5">

                    <?php if ($existTrashList): ?>

                        <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                            Url::to('/problems/get-instruction'), [
                                'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                                'style' => ['margin-top' => '5px'],
                                'title' => 'Инструкция'
                            ]) ?>

                        <?=  Html::a('<div class="hypothesis_trash_link_block"><div>' .  Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '30px', 'height' => '35px']]) . '</div></div>',
                            ['/contractor/problems/trash-list', 'id' => $task->getId()], ['id' => 'show_trash_list', 'class' => 'hypothesis_link_trash pull-right', 'title' => 'Корзина']
                        ) ?>

                    <?php else: ?>

                        <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                            Url::to('/problems/get-instruction'), [
                                'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                                'title' => 'Инструкция'
                            ]) ?>

                    <?php endif; ?>

                </div>
            </div>

        <?php endif; ?>

        <!--Заголовки для списка проблем-->
        <div class="row headers_data_hypothesis" style="margin: 0; padding: 10px;">

            <div class="col-lg-1 ">
                <div class="row">
                    <div class="col-md-4" style="padding: 0;"></div>
                    <div class="col-md-8" style="padding: 0;">Обознач.</div>
                </div>
            </div>

            <div class="col-lg-7 headers_data_hypothesis">
                Описание гипотезы проблемы сегмента
            </div>

            <div class="col-lg-3">
                <div class="row" style="display: flex; align-items: center;">
                    <div class="col-lg-6 text-center">Показатель прохождения теста</div>
                    <div class="col-lg-3 text-center"><div>Дата создания</div></div>
                    <div class="col-lg-3 text-center header_date_confirm"><div>Дата подтв.</div></div>
                </div>
            </div>

            <div class="col-lg-1"></div>

        </div>


        <div class="block_all_hypothesis row" style="padding-left: 10px; padding-right: 10px;">

            <!--Данные для списка проблем-->
            <?php if (!$confirmSegment->getDeletedAt()): ?>

                <?= $this->render('_index_ajax',
                    ['task' => $task, 'models' => $models, 'formTaskComplete' => $formTaskComplete,
                    ]) ?>

            <?php else: ?>

                <?= $this->render('_trash_index_ajax', [
                    'trashList' => $trashList
                ]) ?>

            <?php endif; ?>

        </div>
    </div>


    <?php if ((count($models)) > 0) : ?>

        <div class="row information_status_confirm">

            <div>

                <div style="display:flex; align-items: center;">
                    <?= Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>Проблема подтверждена</div>
                </div>

                <div style="display:flex; align-items: center;">
                    <?= Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>Проблема не подтверждена</div>
                </div>

                <div style="display:flex; align-items: center;">
                    <?= Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
                    <div>Проблема ожидает подтверждения</div>
                </div>

            </div>

        </div>

    <?php endif; ?>


    <div class="formExpectedResults" style="display: none;">

        <?php
        $form = ActiveForm::begin([
            'id' => 'formExpectedResults'
        ]); ?>

        <div class="formExpectedResults_inputs">

            <div class="row container-fluid rowExpectedResults rowExpectedResults-" style="margin-bottom: 15px;">

                <div class="col-xs-6 field-EXR">

                    <?= $form->field($formModel, "_expectedResultsInterview[0][question]", ['template' => '{input}'])->textarea([
                        'rows' => 3,
                        'maxlength' => true,
                        'required' => true,
                        'placeholder' => 'Вопрос',
                        'id' => '_expectedResults_question-',
                        'class' => 'style_form_field_respond form-control',
                    ]) ?>

                </div>

                <div class="col-xs-6 field-EXR">

                    <?= $form->field($formModel, "_expectedResultsInterview[0][answer]", ['template' => '{input}'])->textarea([
                        'rows' => 3,
                        'maxlength' => true,
                        'required' => true,
                        'placeholder' => 'Ответ',
                        'id' => '_expectedResults_answer-',
                        'class' => 'style_form_field_respond form-control',
                    ]) ?>

                </div>

                <div class="col-xs-12">

                    <?= Html::button('Удалить вопрос', [
                        'id' => 'remove-expectedResults-',
                        'class' => "remove-expectedResults btn btn-default",
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'width' => '180px',
                            'height' => '40px',
                            'font-size' => '16px',
                            'border-radius' => '8px',
                            'text-transform' => 'uppercase',
                            'font-weight' => '700',
                            'padding-top' => '9px'
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
    </div>


    <!--Модальные окна-->
    <?= $this->render('modal') ?>

</div>

<!--Подключение скриптов-->
<?php
$this->registerJsFile('@web/js/hypothesis_problem_index.js');
?>