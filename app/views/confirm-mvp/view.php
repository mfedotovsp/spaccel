<?php

use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\EnableExpertise;
use app\models\forms\FormCreateQuestion;
use app\models\forms\FormUpdateConfirmMvp;
use app\models\forms\SearchForm;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\Projects;
use app\models\QuestionsConfirmMvp;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\QuestionStatus;

$this->title = 'Подтверждение MVP';
$this->registerCssFile('@web/css/confirm-mvp-view-style.css');

/**
 * @var ConfirmMvp $model
 * @var FormUpdateConfirmMvp $formUpdateConfirmMvp
 * @var Mvps $mvp
 * @var ConfirmGcp $confirmGcp
 * @var Gcps $gcp
 * @var ConfirmProblem $confirmProblem
 * @var Problems $problem
 * @var ConfirmSegment $confirmSegment
 * @var Segments $segment
 * @var Projects $project
 * @var QuestionsConfirmMvp[] $questions
 * @var FormCreateQuestion $newQuestion
 * @var array $queryQuestions
 * @var SearchForm $searchForm
 * @var int $countContractorResponds
 */

?>
<div class="confirm-mvp-view">

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

        <?= Html::a('<div class="stage_number">4</div><div>Подтверждение гипотез проблем сегментов</div>',
            ['/confirm-problem/view', 'id' => $confirmProblem->getId()],
            ['class' => 'passive_navigation_block navigation_block']
        ) ?>

        <?= Html::a('<div class="stage_number">5</div><div>Разработка гипотез ценностных предложений</div>',
            ['/gcps/index', 'id' => $confirmProblem->getId()],
            ['class' => 'passive_navigation_block navigation_block']
        ) ?>

        <?= Html::a('<div class="stage_number">6</div><div>Подтверждение гипотез ценностных предложений</div>',
            ['/confirm-gcp/view', 'id' => $confirmGcp->getId()],
            ['class' => 'passive_navigation_block navigation_block']
        ) ?>

        <?= Html::a('<div class="stage_number">7</div><div>Разработка MVP</div>',
            ['/mvps/index', 'id' => $confirmGcp->getId()],
            ['class' => 'passive_navigation_block navigation_block']
        ) ?>

        <div class="active_navigation_block navigation_block">
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
            if (mb_strlen($segment_name) > 12){
                $segment_name = mb_substr($segment_name, 0, 12) . '...';
            }

            $problem_description = $problem->getDescription();
            if (mb_strlen($problem_description) > 12){
                $problem_description = mb_substr($problem_description, 0, 12) . '...';
            }

            $gcp_description = $gcp->getDescription();
            if (mb_strlen($gcp_description) > 15){
                $gcp_description = mb_substr($gcp_description, 0, 15) . '...';
            }

            $mvp_description = $mvp->getDescription();
            if (mb_strlen($mvp_description) > 15){
                $mvp_description = mb_substr($mvp_description, 0, 15) . '...';
            }
            ?>

            <?= Html::a('Сегмент: <div>' . $segment_name . '</div> / Проблема: <div>' . $problem_description . '</div> / ЦП: <div>' . $gcp_description . '</div> / MVP: <div>' . $mvp_description . '</div><span class="arrow_link"><span></span><span><span></span>', ['#'], ['id' => 'view_desc_stage_width_max_1900', 'onclick' => 'return false', 'class' => 'view_block_description view_desc_stage']) ?>

            <?php
            $mvp_description = $mvp->getDescription();
            if (mb_strlen($mvp_description) > 50){
                $mvp_description = mb_substr($mvp_description, 0, 50) . '...';
            }
            ?>

            <?= Html::a('Сегмент: <div>' . $segment_name . '</div> / Проблема: <div>' . $problem_description . '</div> / ЦП: <div>' . $gcp_description . '</div> / MVP: <div>' . $mvp_description . '</div><span class="arrow_link"><span></span><span><span></span>', ['#'], ['id' => 'view_desc_stage_width_min_1900', 'onclick' => 'return false', 'class' => 'view_block_description view_desc_stage']) ?>

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
        <div>Формулировка ценностного предложения:</div>
        <div><?= $gcp->getDescription() ?></div>
        <div>Формулировка минимально жизнеспособного продукта:</div>
        <div><?= $mvp->getDescription() ?></div>
    </div>



    <!-- Tab links -->
    <div class="block-link-create-interview tab row">

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

    <!--ПРОГРАММА ПОДТВЕРЖДЕНИЯ MVP (ШАГ 1)-->
    <div id="step_one" class="tabcontent row">
        <?= $this->render('ajax_data_confirm', [
            'model' => $model,
            'mvp' => $mvp,
            'formUpdateConfirmMvp' => $formUpdateConfirmMvp,
            'countContractorResponds' => $countContractorResponds
        ]) ?>
    </div>


    <!--ПРОГРАММА ПОДТВЕРЖДЕНИЯ MVP (ШАГ 2)-->
    <div id="step_two" class="tabcontent row">

        <div class="container-fluid container-data">

            <!--Заголовок для списка вопросов-->

            <div class="row row_header_data">

                <div class="col-xs-12 col-md-6" style="padding: 5px 0 0 0;">
                    <?= Html::a('Список вопросов для интервью' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-mvp/get-instruction-step-two'],[
                        'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                    ]) ?>
                </div>

                <div class="col-xs-12 col-md-6" style="padding: 0;">

                    <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                        <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Добавить вопрос</div></div>', ['#'],
                            ['class' => 'add_new_question_button pull-right', 'id' => 'buttonAddQuestion']
                        ) ?>

                    <?php endif; ?>

                </div>

            </div>

            <?= $this->render('form_questions', [
                'questions' => $questions,
                'mvp' => $mvp,
                'queryQuestions' => $queryQuestions,
                'model' => $model,
                'newQuestion' => $newQuestion,
            ]) ?>

        </div>
    </div>



    <!--ПРОГРАММА ПОДТВЕРЖДЕНИЯ MVP (ШАГ 3)-->
    <div id="step_three" class="tabcontent row">

        <!--Список респондентов-->
        <div class="container-fluid container-data">

            <div class="row row_header_data top_slide_pagination_responds">

                <div class="col-md-9" style="padding: 5px 0 0 0;">
                    <?= Html::a('Информация о респондентах и интервью' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-mvp/get-instruction-step-three'],[
                        'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                    ]) ?>
                </div>

                <div class="col-md-3" style="padding: 0;">

                    <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

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

                    <?= Html::a(Html::img('/images/icons/icon_q&a.png', ['style' => ['width' => '40px']]), ['/confirm-mvp/get-data-questions-and-answers', 'id' => $model->getId()], [
                        'class' => 'openTableQuestionsAndAnswers', 'style' => ['margin-right' => '8px'], 'title'=> 'Ответы респондентов на вопросы интервью',
                    ]) ?>

                    <?= Html::a(Html::img('/images/icons/icon_export.png', ['style' => ['width' => '22px']]), ['/confirm-mvp/mpdf-data-responds', 'id' => $model->getId()], [
                        'target'=>'_blank',
                        'title'=> 'Скачать таблицу респондентов',
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
                    'margin' => '10px 1% 10px 2%',
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
                    'margin' => '10px 2% 10px 1%',
                ],
            ]) ?>

        </div>

    </div>

    <div class="arrow_stages_project_mobile">
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage active"></div>
        <div class="item-stage passive"></div>
    </div>

    <div class="arrow_links_router_mobile">
        <div class="arrow_link_router_mobile_left">
            <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                Url::to(['/mvps/index', 'id' => $confirmGcp->getId()])) ?>
        </div>
        <div class="text-stage">8/9. Подтверждение MVP</div>
        <div class="arrow_link_router_mobile_right">
            <?php if ($mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED && $mvp->confirm->getEnableExpertise() === EnableExpertise::ON) : ?>
                <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                    Url::to(['/business-model/index', 'id' => $model->getId()])) ?>
            <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED): ?>
                <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
            <?php else: ?>
                <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>
                    <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                        Url::to(['/confirm-mvp/moving-next-stage', 'id' => $model->getId()]), [
                            'id' => 'button_MovingNextStage']) ?>
                <?php else: ?>
                    <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="header-title-confirm-hypothesis-mobile">
        <div style="overflow: hidden; max-width: 90%;">Продукт: <?= $mvp->getTitle() ?></div>
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
                'formUpdateConfirmMvp' => $formUpdateConfirmMvp,
                'model' => $model,
                'mvp' => $mvp,
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
                    Url::to('/confirm-mvp/get-instruction-step-two'), [
                        'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                        'title' => 'Инструкция', 'style' => ['margin-left' => '10px', 'margin-top' => '5px']
                    ]) ?>
                <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>
                    <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Добавить вопрос</div></div>', ['#'],
                        ['class' => 'add_new_question_button', 'id' => 'buttonAddQuestion']
                    ) ?>
                <?php endif; ?>
            </div>
        </div>

        <?= $this->render('form_questions', [
            'questions' => $questions,
            'mvp' => $mvp,
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
                <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>
                    <?=  Html::a( '<div style="display:flex; align-items: center; padding: 5px 0;"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Добавить респондента</div></div>', ['/responds/get-data-create-form', 'stage' => $model->getStage() , 'id' => $model->getId(), 'isMobile' => true],
                        ['class' => 'link_add_respond_text show_add_respond']
                    ) ?>
                <?php endif; ?>
            </div>

            <div class="col-xs-4">

                <?php if (!User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                        Url::to('/confirm-mvp/get-instruction-step-three'),[
                            'class' => 'link_to_instruction_page_mobile open_modal_instruction_page pull-right',
                            'title' => 'Инструкция', 'style' => ['margin-left' => '10px', 'margin-top' => '5px']
                        ]) ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_green_search.png'), ['#'], [
                        'class' => 'link_show_search_field_mobile show_search_responds pull-right',
                        'title' => 'Поиск респондентов', 'style' => ['margin-top' => '5px']
                    ]) ?>

                <?php else : ?>

                    <?= Html::a(Html::img('@web/images/icons/icon_red_info.png'),
                        Url::to(['/confirm-mvp/get-instruction-step-three'],[
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
$this->registerJsFile('@web/js/confirm_mvp_view.js');
$this->registerJsFile('@web/js/main_expertise.js');
?>
