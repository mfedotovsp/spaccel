<?php

use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\EnableExpertise;
use app\models\forms\FormCreateQuestion;
use app\models\forms\FormUpdateConfirmProblem;
use app\models\forms\SearchForm;
use app\models\Problems;
use app\models\Projects;
use app\models\QuestionsConfirmProblem;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\User;

$this->title = 'Подтверждение гипотез проблем сегмента';
$this->registerCssFile('@web/css/confirm-problem-view-style.css');

/**
 * @var ConfirmProblem $model
 * @var FormUpdateConfirmProblem $formUpdateConfirmProblem
 * @var Problems $problem
 * @var ConfirmSegment $confirmSegment
 * @var Segments $segment
 * @var Projects $project
 * @var QuestionsConfirmProblem[] $questions
 * @var FormCreateQuestion $newQuestion
 * @var array $queryQuestions
 * @var SearchForm $searchForm
 * @var int $countContractorResponds
 */

?>

<div class="confirm-problem-view">


    <div class="row project_info_data">


        <div class="col-xs-12 col-md-12 col-lg-4 project_name">
            <span>Проект:</span>
            <?= $project->getProjectName() ?>
        </div>

        <?= Html::a('Данные проекта', ['/projects/show-all-information', 'id' => $project->getId()], [
            'class' => 'col-xs-12 col-sm-3 col-md-3 col-lg-2 openAllInformationProject link_in_the_header',
        ]) ?>

        <?= Html::a('Протокол проекта', ['/projects/report', 'id' => $project->getId()], [
            'class' => 'col-xs-12 col-sm-3 col-md-3 col-lg-2 openReportProject link_in_the_header text-center',
        ]) ?>

        <?= Html::a('Трэкшн карта проекта', ['/projects/show-roadmap', 'id' => $project->getId()], [
            'class' => 'col-xs-12 col-sm-3 col-md-3 col-lg-2 openRoadmapProject link_in_the_header text-center',
        ]) ?>

        <?= Html::a('Сводная таблица проекта', ['/projects/result', 'id' => $project->getId()], [
            'class' => 'col-xs-12 col-sm-3 col-md-3 col-lg-2 openResultTableProject link_in_the_header text-center',
        ]) ?>

        <?= Html::a('Экспорт в Excel', ['/export-to-excel/project', 'id' => $project->getId()], [
            'class' => 'col-xs-12 col-sm-3 col-md-3 col-lg-2 link_in_the_header text-center',
        ]) ?>

    </div>



    <div class="row navigation_blocks">

        <?= Html::a('<div class="stage_number">1</div><div>Генерация гипотез целевых сегментов</div>',
            ['/segments/index', 'id' => $project->getId()],
            ['class' => 'passive_navigation_block navigation_block']
        ) ?>

        <?= Html::a('<div class="stage_number">2</div><div>Подтверждение гипотез целевых сегментов</div>',
            ['/confirm-segment/view', 'id' => $confirmSegment->getId()],
            ['class' => 'passive_navigation_block navigation_block']
        ) ?>

        <?= Html::a('<div class="stage_number">3</div><div>Генерация гипотез проблем сегментов</div>',
            ['/problems/index', 'id' => $confirmSegment->getId()],
            ['class' => 'passive_navigation_block navigation_block']
        ) ?>

        <div class="active_navigation_block navigation_block">
            <div class="stage_number">4</div>
            <div>Подтверждение гипотез проблем сегментов</div>
        </div>

        <div class="no_transition_navigation_block navigation_block">
            <div class="stage_number">5</div>
            <div>Разработка гипотез ценностных предложений</div>
        </div>

        <div class="no_transition_navigation_block navigation_block">
            <div class="stage_number">6</div>
            <div>Подтверждение гипотез ценностных предложений</div>
        </div>

        <div class="no_transition_navigation_block navigation_block">
            <div class="stage_number">7</div>
            <div>Разработка MVP</div>
        </div>

        <div class="no_transition_navigation_block navigation_block">
            <div class="stage_number">8</div>
            <div>Подтверждение MVP</div>
        </div>

        <div class="no_transition_navigation_block navigation_block">
            <div class="stage_number">9</div>
            <div>Генерация бизнес-модели</div>
        </div>

    </div>



    <div class="row segment_info_data">

        <div class="col-xs-12 col-md-12 col-lg-8 stage_name_row">

            <?php
            $segment_name = $segment->getName();
            if (mb_strlen($segment_name) > 15){
                $segment_name = mb_substr($segment_name, 0, 15) . '...';
            }

            $problem_description = $problem->getDescription();
            if (mb_strlen($problem_description) > 50){
                $problem_description = mb_substr($problem_description, 0, 50) . '...';
            }
            ?>

            <?= Html::a('Сегмент: <div>' . $segment_name . '</div> / Проблема: <div>' . $problem_description . '</div><span class="arrow_link"><span></span><span><span></span>', ['#'], ['id' => 'view_desc_stage_width_max_1900', 'onclick' => 'return false', 'class' => 'view_block_description view_desc_stage']) ?>

            <?php
            $problem_description = $problem->getDescription();
            if (mb_strlen($problem_description) > 100){
                $problem_description = mb_substr($problem_description, 0, 100) . '...';
            }
            ?>

            <?= Html::a('Сегмент: <div>' . $segment_name . '</div> / Проблема: <div>' . $problem_description . '</div><span class="arrow_link"><span></span><span><span></span>', ['#'], ['id' => 'view_desc_stage_width_min_1900', 'onclick' => 'return false', 'class' => 'view_block_description view_desc_stage']) ?>

        </div>

        <?= Html::a('Данные сегмента', ['/segments/show-all-information', 'id' => $segment->getId()], [
            'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-2 openAllInformationSegment link_in_the_header',
        ]) ?>

        <?= Html::a('Трэкшн карта сегмента', ['/segments/show-roadmap', 'id' => $segment->getId()], [
            'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-2 openRoadmapSegment link_in_the_header text-center',
        ]) ?>

    </div>


    <div class="row block_description_stage">
        <div>Наименование сегмента:</div>
        <div><?= $segment->getName() ?></div>
        <div>Формулировка проблемы:</div>
        <div><?= $problem->getDescription() ?></div>
    </div>


    <!-- Tab links -->
    <div class="block-link-create-interview row tab">

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 1</div><div class="link_create_interview-text_right">Заполнить исходные данные подтверждения</div></div>', [
            'class' => 'tablinks link_create_interview col-xs-12 col-lg-4',
            'onclick' => "openCity(event, 'step_one')"
        ]) ?>

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 2</div><div class="link_create_interview-text_right">Сформировать список вопросов</div></div>', [
            'class' => 'tablinks link_create_interview col-xs-12 col-lg-4',
            'onclick' => "openCity(event, 'step_two')",
        ]) ?>

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 3</div><div class="link_create_interview-text_right">Заполнить информацию о респондентах и интервью</div></div>', [
            'class' => 'tablinks link_create_interview col-xs-12 col-lg-4',
            'onclick' => "openCity(event, 'step_three')",
            'id' => "defaultOpen",
        ]) ?>

    </div>


    <!-- Tab content -->

    <!--ПРОГРАММА ПОДТВЕРЖДЕНИЯ ГПС (ШАГ 1)-->
    <div id="step_one" class="tabcontent row">
        <?= $this->render('ajax_data_confirm', [
            'formUpdateConfirmProblem' => $formUpdateConfirmProblem,
            'model' => $model,
            'problem' => $problem,
            'countContractorResponds' => $countContractorResponds
        ]) ?>
    </div>

    <!--ПРОГРАММА ПОДТВЕРЖДЕНИЯ ГПС (ШАГ 2)-->
    <div id="step_two" class="tabcontent row">


        <div class="container-fluid container-data">

            <!--Заголовок для списка вопросов-->

            <div class="row row_header_data">

                <div class="col-xs-12 col-md-6" style="padding: 5px 0 0 0;">
                    <?= Html::a('Список вопросов для интервью' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-problem/get-instruction-step-two'],[
                        'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                    ]) ?>
                </div>

                <div class="col-xs-12 col-md-6" style="padding: 0;">
                    <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>
                        <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Добавить вопрос</div></div>', ['#'],
                            ['class' => 'add_new_question_button pull-right', 'id' => 'buttonAddQuestion']
                        ) ?>
                    <?php endif; ?>
                </div>

            </div>


            <?= $this->render('form_questions', [
                'questions' => $questions,
                'problem' => $problem,
                'queryQuestions' => $queryQuestions,
                'model' => $model,
                'newQuestion' => $newQuestion,
            ]) ?>

        </div>
    </div>



    <!--ПРОГРАММА ПОДТВЕРЖДЕНИЯ ГПС (ШАГ 3)-->
    <div id="step_three" class="tabcontent row">

        <!--Список респондентов-->
        <div class="container-fluid container-data">

            <div class="row row_header_data top_slide_pagination_responds">

                <div class="col-md-9" style="padding: 5px 0 0 0;">
                    <?= Html::a('Информация о респондентах и интервью' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-problem/get-instruction-step-three'],[
                        'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                    ]) ?>
                </div>

                <div class="col-md-3" style="padding: 0;">
                    <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>
                        <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Добавить респондента</div></div>', ['/responds/get-data-create-form', 'stage' => $model->getStage() , 'id' => $model->getId()],
                            ['id' => 'showRespondCreateForm', 'class' => 'link_add_respond_text pull-right']
                        ) ?>
                    <?php endif; ?>
                </div>

            </div>

            <!--Заголовки для списка респондентов-->
            <div class="row" style="margin: 0; padding: 10px;">

                <div class="col-md-3 headers_data_respond_hi">
                    Фамилия, имя, отчество
                </div>

                <div class="col-md-3" style="padding: 0;">
                    <div class="headers_data_respond_hi">
                        Данные респондента
                    </div>
                    <div class="headers_data_respond_low">
                        Кто? Откуда? Чем занят?
                    </div>
                </div>

                <div class="col-md-3" style="padding: 0;">
                    <div class="headers_data_respond_hi">
                        Место проведения
                    </div>
                    <div class="headers_data_respond_low">
                        Организация, адрес
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="row headers_data_respond_hi" style="text-align: center;">
                        Интервью
                    </div>
                    <div class="row headers_data_respond_low">
                        <div class="col-md-6" style="text-align: center;">
                            План
                        </div>
                        <div class="col-md-6" style="text-align: center;">
                            Факт
                        </div>
                    </div>
                </div>

                <div class="col-md-1" style="text-align: right; padding-top: 10px; padding-bottom: 10px;">

                    <?= Html::a(Html::img('/images/icons/icon_q&a.png', ['style' => ['width' => '40px']]), ['/confirm-problem/get-data-questions-and-answers', 'id' => $model->getId()], [
                        'class' => 'openTableQuestionsAndAnswers', 'style' => ['margin-right' => '8px'], 'title'=> 'Ответы респондентов на вопросы интервью',
                    ]) ?>

                    <?= Html::a(Html::img('/images/icons/icon_export.png', ['style' => ['width' => '22px']]), ['/confirm-problem/mpdf-data-responds', 'id' => $model->getId()], [
                        'target' => '_blank', 'title'=> 'Скачать таблицу респондентов',
                    ]) ?>

                </div>

            </div>


            <!--renderAjax /responds-confirm/get-query-responds-->
            <div class="content_responds_ajax"></div>

        </div>
    </div>
</div>


<div class="confirm-hypothesis-view-mobile">
    <div class="header-title-index-mobile">
        <div style="overflow: hidden; max-width: 70%;">Проект: <?= $project->getProjectName() ?></div>
        <div class="buttons-project-menu-mobile" style="position: absolute; right: 20px; top: 5px;">
            <?= Html::img('@web/images/icons/icon-four-white-squares.png', ['class' => 'open-project-menu-mobile', 'style' => ['width' => '30px']]) ?>
            <?= Html::img('@web/images/icons/icon-white-cross.png', ['class' => 'close-project-menu-mobile', 'style' => ['width' => '30px', 'display' => 'none']]) ?>
        </div>
    </div>

    <div class="project-menu-mobile">
        <div class="project_buttons_mobile">

            <?= Html::a('Сводная таблица', ['/projects/result-mobile', 'id' => $project->getId()], [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'width' => '47%',
                    'height' => '36px',
                    'background' => '#7F9FC5',
                    'color' => '#FFFFFF',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'border-radius' => '0',
                    'border' => '1px solid #ffffff',
                    'font-size' => '18px',
                    'margin' => '10px 1% 0 2%',
                ],
            ]) ?>

            <?= Html::a('Трэкшн карта', ['/projects/roadmap-mobile', 'id' => $project->getId()], [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'width' => '47%',
                    'height' => '36px',
                    'background' => '#7F9FC5',
                    'color' => '#FFFFFF',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'border-radius' => '0',
                    'border' => '1px solid #ffffff',
                    'font-size' => '18px',
                    'margin' => '10px 2% 0 1%',
                ],
            ]) ?>

        </div>

        <div class="project_buttons_mobile">

            <?= Html::a('Протокол', ['/projects/report-mobile', 'id' => $project->getId()], [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'width' => '47%',
                    'height' => '36px',
                    'background' => '#7F9FC5',
                    'color' => '#FFFFFF',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'border-radius' => '0',
                    'border' => '1px solid #ffffff',
                    'font-size' => '18px',
                    'margin' => '10px 1% 0 2%',
                ],
            ]) ?>

            <?= Html::a('Презентация', ['/projects/presentation-mobile', 'id' => $project->getId()], [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'width' => '47%',
                    'height' => '36px',
                    'background' => '#7F9FC5',
                    'color' => '#FFFFFF',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'border-radius' => '0',
                    'border' => '1px solid #ffffff',
                    'font-size' => '18px',
                    'margin' => '10px 2% 0 1%',
                ],
            ]) ?>

        </div>

        <div class="project_buttons_mobile">

            <?= Html::a('Экспорт в Excel', ['/export-to-excel/project', 'id' => $project->getId()], [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'width' => '47%',
                    'height' => '36px',
                    'background' => '#7F9FC5',
                    'color' => '#FFFFFF',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'border-radius' => '0',
                    'border' => '1px solid #ffffff',
                    'font-size' => '18px',
                    'margin' => '10px 1% 10px 2%',
                ],
            ]) ?>

        </div>

    </div>

    <div class="arrow_stages_project_mobile">
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage active"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
    </div>

    <div class="arrow_links_router_mobile">
        <div class="arrow_link_router_mobile_left">
            <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                Url::to(['/problems/index', 'id' => $confirmSegment->getId()])) ?>
        </div>
        <div class="text-stage">4/9. Подтверждение гипотез проблем сегментов</div>
        <div class="arrow_link_router_mobile_right">
            <?php if ($problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED && $problem->confirm->getEnableExpertise() === EnableExpertise::ON) : ?>
                <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                    Url::to(['/gcps/index', 'id' => $model->getId()])) ?>
            <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED): ?>
                <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
            <?php else: ?>
                <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>
                    <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                        Url::to(['/confirm-problem/moving-next-stage', 'id' => $model->getId()]), [
                            'id' => 'button_MovingNextStage']) ?>
                <?php else: ?>
                    <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="header-title-confirm-hypothesis-mobile">
        <div style="overflow: hidden; max-width: 90%;">Проблема: <?= $problem->getTitle() ?></div>
    </div>

    <!--ШАГ 1-->
    <div class="confirm-stage-mobile confirm-hypothesis-step-one-mobile">

        <div class="arrow_stages_confirm_hypothesis_mobile">
            <div class="item-stage active"></div>
            <div class="item-stage passive"></div>
            <div class="item-stage passive"></div>
        </div>

        <div class="arrow_links_router_mobile">
            <div class="arrow_link_router_mobile_left">
                <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
            </div>
            <div class="text-stage">1/3. Заполнить исходные данные подтверждения</div>
            <div class="arrow_link_router_mobile_right">
                <?= Html::img('@web/images/icons/arrow_left_active.png', [
                    'class' => 'open-confirm-hypothesis-step-two-mobile']) ?>
            </div>
        </div>

        <div class="row block-ajax-data-confirm">
            <?= $this->render('ajax_data_confirm', [
                'formUpdateConfirmProblem' => $formUpdateConfirmProblem,
                'model' => $model,
                'problem' => $problem,
                'countContractorResponds' => $countContractorResponds
            ]) ?>
        </div>

    </div>

    <!--ШАГ 2-->
    <div class="confirm-stage-mobile confirm-hypothesis-step-two-mobile">

        <div class="arrow_stages_confirm_hypothesis_mobile">
            <div class="item-stage passive"></div>
            <div class="item-stage active"></div>
            <div class="item-stage passive"></div>
        </div>

        <div class="arrow_links_router_mobile">
            <div class="arrow_link_router_mobile_left">
                <?= Html::img('@web/images/icons/arrow_left_active.png', [
                    'class' => 'open-confirm-hypothesis-step-one-mobile']) ?>
            </div>
            <div class="text-stage">2/3. Сформировать список вопросов</div>
            <div class="arrow_link_router_mobile_right">
                <?= Html::img('@web/images/icons/arrow_left_active.png', [
                    'class' => 'open-confirm-hypothesis-step-three-mobile']) ?>
            </div>
        </div>

        <div class="row container-fluid">
            <div class="col-xs-12" style="padding: 0;">
                <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                    Url::to('/confirm-problem/get-instruction-step-two'), [
                        'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                        'title' => 'Инструкция', 'style' => ['margin-left' => '10px', 'margin-top' => '5px']
                    ]) ?>
                <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>
                    <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Добавить вопрос</div></div>', ['#'],
                        ['class' => 'add_new_question_button', 'id' => 'buttonAddQuestion']
                    ) ?>
                <?php endif; ?>
            </div>
        </div>

        <?= $this->render('form_questions', [
            'questions' => $questions,
            'problem' => $problem,
            'queryQuestions' => $queryQuestions,
            'model' => $model,
            'newQuestion' => $newQuestion,
        ]) ?>

    </div>

    <!--ШАГ 3-->
    <div class="confirm-stage-mobile confirm-hypothesis-step-three-mobile">

        <div class="arrow_stages_confirm_hypothesis_mobile">
            <div class="item-stage passive"></div>
            <div class="item-stage passive"></div>
            <div class="item-stage active"></div>
        </div>

        <div class="arrow_links_router_mobile">
            <div class="arrow_link_router_mobile_left">
                <?= Html::img('@web/images/icons/arrow_left_active.png', [
                    'class' => 'open-confirm-hypothesis-step-two-mobile']) ?>
            </div>
            <div class="text-stage">3/3. Заполнить информацию</div>
            <div class="arrow_link_router_mobile_right">
                <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
            </div>
        </div>

        <div class="row row_header_data_generation_mobile">
            <div class="col-xs-8">
                <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>
                    <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Добавить респондента</div></div>', ['/responds/get-data-create-form', 'stage' => $model->getStage() , 'id' => $model->getId(), 'isMobile' => true],
                        ['class' => 'link_add_respond_text show_add_respond']
                    ) ?>
                <?php endif; ?>
            </div>

            <div class="col-xs-4">

                <?php if (!User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                        Url::to('/confirm-problem/get-instruction-step-three'),[
                            'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                            'title' => 'Инструкция', 'style' => ['margin-left' => '10px', 'margin-top' => '5px']
                        ]) ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_green_search.png'), ['#'], [
                        'class' => 'link_show_search_field_mobile show_search_responds pull-right',
                        'title' => 'Поиск респондентов', 'style' => ['margin-top' => '5px']
                    ]) ?>

                <?php else : ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                        Url::to(['/confirm-problem/get-instruction-step-three'],[
                            'class' => 'link_to_instruction_page open_modal_instruction_page',
                            'title' => 'Инструкция', 'style' => ['margin-top' => '5px']
                        ])) ?>

                <?php endif; ?>

            </div>
        </div>

        <div class="row search_block_mobile">
            <div class="col-xs-10">
                <?php $form = ActiveForm::begin([
                    'id' => 'search_responds_mobile',
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <?= $form->field($searchForm, 'search', ['template' => '{input}'])
                    ->textInput([
                        'id' => 'search_input_responds_mobile',
                        'class' => 'style_form_field_respond form-control',
                        'placeholder' => 'поиск респондента',
                        'minlength' => 5,
                        'autocomplete' => 'off'])
                    ->label(false) ?>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-xs-2 pull-right">
                <?= Html::a(Html::img('@web/images/icons/cancel_danger.png'), ['#'], ['class' => 'link_cancel_search_field_mobile show_search_responds']) ?>
            </div>
        </div>

        <div class="row add_respond_block_mobile"></div>

        <!--renderAjax /respond/get-query-responds-->
        <div class="content_responds_ajax"></div>

    </div>
</div>



<!--Модальные окна-->
<?= $this->render('view_modal', ['model' => $model]) ?>
<!--Подключение скриптов-->
<?php
$this->registerJsFile('@web/js/confirm_problem_view.js');
$this->registerJsFile('@web/js/main_expertise.js');
?>
