<?php

use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\EnableExpertise;
use app\models\forms\FormCreateQuestion;
use app\models\forms\FormUpdateConfirmProblem;
use app\models\Problems;
use app\models\Projects;
use app\models\QuestionsConfirmProblem;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use app\models\User;

$this->title = 'Подтверждение гипотезы проблемы сегмента';
$this->registerCssFile('@web/css/confirm-problem-add_questions-style.css');

/**
 * @var FormUpdateConfirmProblem $formUpdateConfirmProblem
 * @var QuestionsConfirmProblem[] $questions
 * @var FormCreateQuestion $newQuestion
 * @var array $queryQuestions
 * @var ConfirmProblem $model
 * @var Problems $problem
 * @var ConfirmSegment $confirmSegment
 * @var Segments $segment
 * @var Projects $project
 * @var int $countContractorResponds
 */

?>


<div class="confirm-problem-add-questions">


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


    <div class="block-link-create-interview row tab">

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 1</div><div class="link_create_interview-text_right">Заполнить исходные данные подтверждения</div></div>', [
            'class' => 'tablinks link_create_interview col-xs-12 col-lg-4',
            'onclick' => "openCity(event, 'step_one')"
        ]) ?>

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 2</div><div class="link_create_interview-text_right">Сформировать список вопросов</div></div>', [
            'class' => 'tablinks link_create_interview col-xs-12 col-lg-4',
            'onclick' => "openCity(event, 'step_two')",
            'id' => "defaultOpen",
        ]) ?>

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 3</div><div class="link_create_interview-text_right">Заполнить информацию о респондентах и интервью</div></div>', [
            'class' => 'link_create_interview link_passive_create_interview col-xs-12 col-lg-4 show_modal_next_step_error',
        ]) ?>

    </div>


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


            <div class="col-xs-12">

                <?= Html::a( 'Далее', ['/confirm-problem/view', 'id' => $model->getId()],[
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'background' => '#52BE7F',
                        'width' => '180px',
                        'height' => '40px',
                        'font-size' => '24px',
                        'border-radius' => '8px',
                    ],
                    'class' => 'btn btn-lg btn-success pull-right',
                ]) ?>

            </div>
        </div>
    </div>
</div>


<div class="confirm-hypothesis-add-questions-mobile">

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
            <?php else: ?>
                <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
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

        <div class="row">
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
                <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                    Url::to(['/confirm-problem/view', 'id' => $model->getId()])) ?>
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

</div>


<?php
// Модальное окно - Запрет на следующий шаг
Modal::begin([
    'options' => ['id' => 'next_step_error', 'class' => 'next_step_error'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center" style="color: #F2F2F2; padding: 0 30px;">Данный этап не доступен</h3>',
]);
?>

<h4 class="text-center" style="color: #F2F2F2; padding: 0 30px;">
    Пройдите последовательно этапы подтверждения гипотезы проблемы сегмента. Далее переходите к разработке гипотез ценностных предложений.
</h4>

<?php Modal::end(); ?>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/confirm_problem_add_questions.js'); ?>
