<?php

use app\models\ConfirmGcp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\forms\FormCreateConfirmMvp;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Подтверждение MVP';
$this->registerCssFile('@web/css/confirm-mvp-create-style.css');

/**
 * @var FormCreateConfirmMvp $model
 * @var Mvps $mvp
 * @var ConfirmGcp $confirmGcp
 * @var Gcps $gcp
 * @var ConfirmProblem $confirmProblem
 * @var Problems $problem
 * @var ConfirmSegment $confirmSegment
 * @var Segments $segment
 * @var Projects $project
 */

?>
<div class="confirm-mvp-create">

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
            <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
        </div>
    </div>

    <div class="header-title-confirm-hypothesis-mobile">
        <div style="overflow: hidden; max-width: 90%;">Продукт: <?= $gcp->getTitle() ?></div>
    </div>

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
            <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
        </div>
    </div>

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


    <div class="block-link-create-interview row">

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 1</div><div class="link_create_interview-text_right">Заполнить исходные данные подтверждения</div></div>', [
            'class' => 'link_create_interview link_active_create_interview col-xs-12 col-lg-4',
        ]) ?>

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 2</div><div class="link_create_interview-text_right">Сформировать список вопросов</div></div>', [
            'class' => 'link_create_interview link_passive_create_interview col-xs-12 col-lg-4 show_modal_next_step_error',
        ]) ?>

        <?= Html::button('<div class="link_create_interview-block_text"><div class="link_create_interview-text_left">Шаг 3</div><div class="link_create_interview-text_right">Заполнить информацию о респондентах и интервью</div></div>', [
            'class' => 'link_create_interview link_passive_create_interview col-xs-12 col-lg-4 show_modal_next_step_error',
        ]) ?>

    </div>


    <div class="confirm-create-button-instruction-mobile">
        <?= Html::a('Инструкция',
            ['/confirm-mvp/get-instruction-step-one'],[
                'class' => 'btn btn-default open_modal_instruction_page',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#F5A4A4',
                    'color' => '#ffffff',
                    'width' => '180px',
                    'height' => '40px',
                    'font-size' => '16px',
                    'text-transform' => 'uppercase',
                    'font-weight' => '700',
                    'padding-top' => '9px',
                    'border-radius' => '8px',
                    'margin-top' => '28px'
                ]
            ]) ?>
    </div>


    <div class="row">

        <div class="container-fluid container-data">

            <div class="row row_header_data">

                <div class="col-md-12" style="padding: 5px 0 0 0;">
                    <?= Html::a('Исходные данные подтверждения' . Html::img('/images/icons/icon_report_next.png'), ['/confirm-mvp/get-instruction-step-one'],[
                        'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
                    ]) ?>
                </div>

            </div>

            <div class="container-fluid">

                <div class="content-view-data-confirm">

                    <div class="row">
                        <div class="col-md-12">Цель проекта</div>
                        <div class="col-md-12"><?= $project->getPurposeProject() ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">Приветствие в начале встречи</div>
                        <div class="col-md-12"><?= $confirmSegment->getGreetingInterview() ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">Информация о вас для респондентов</div>
                        <div class="col-md-12"><?= $confirmSegment->getViewInterview() ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">Причина и тема (что побудило) для проведения исследования</div>
                        <div class="col-md-12"><?= $confirmSegment->getReasonInterview() ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">Формулировка минимально жизнеспособного продукта, который проверяем</div>
                        <div class="col-md-12"><?= $mvp->getDescription() ?></div>
                    </div>

                </div>

                <?php $form = ActiveForm::begin([
                    'id' => 'new_confirm_mvp',
                    'action' => Url::to(['/confirm-mvp/save-confirm', 'id' => $mvp->getId()]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]);
                ?>


                <div class="row desktop-pt-5 desktop-pb-5 mobile-mt-10">

                    <?= $form->field($model, 'count_respond', [
                        'template' => '<div class="col-xs-12 col-sm-9 col-md-10 pl-20">{label}</div><div class="col-xs-12 col-sm-3 col-md-2">{input}</div>'
                    ])->label('<div style="padding-top: 5px;">Количество личных респондентов, подтвердивших ЦП</div><div class="font-size-12 color-grey">Респондентов, подтвердивших ЦП и прикрепленных к исполнителям, можно добавить при создании задания</div>')
                        ->textInput([
                            'type' => 'number',
                            'readonly' => true,
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'id' => 'confirm_count_respond',
                            'autocomplete' => 'off'
                        ]) ?>

                </div>

                <div class="row desktop-pt-5 desktop-pb-5 mobile-mt-20">

                    <?php $btnContent = '<div class="changeBtnContent">Добавить новых респондентов</div><div style="font-weight: 400;font-size: 13px;">(Общее количечество респондентов - от 1 до 100)</div>'; ?>

                    <?= $form->field($model, 'add_count_respond', [
                        'template' => '<div class="col-xs-12 col-sm-9 col-md-10 pl-20">{label}</div><div class="col-xs-12 col-sm-3 col-md-2">{input}</div>'
                    ])->label(Html::button($btnContent, [
                        'id' => 'switch_add_count_respond',
                        'class' => 'btn btn-default',
                        'style' => [
                            'font-weight' => '700',
                            'line-height' => '18px',
                            'margin-top' => '-10px',
                            'background' => '#E0E0E0',
                            'padding' => '0 7px',
                            'width' => '300px',
                            'height' => '50px',
                            'font-size' => '16px',
                            'border-radius' => '8px',
                        ]
                    ])
                    )->textInput([
                        'type' => 'number',
                        'readonly' => true,
                        'class' => 'style_form_field_respond form-control',
                        'id' => 'confirm_add_count_respond',
                        'autocomplete' => 'off'
                    ]) ?>

                </div>

                <div class="row mobile-mt-10">

                    <?= $form->field($model, 'count_positive', [
                        'template' => '<div class="col-xs-12 col-sm-9 col-md-10 pl-20">{label}</div><div class="col-xs-12 col-sm-3 col-md-2">{input}</div>'
                    ])->label('<div style="padding-top: 5px;">Необходимое количество респондентов, подтверждающих MVP</div>')
                        ->textInput([
                            'type' => 'number',
                            'required' => true,
                            'class' => 'style_form_field_respond form-control',
                            'id' => 'confirm_count_positive',
                            'autocomplete' => 'off'
                        ]) ?>

                </div>


                <div class="form-group row">
                    <div class="col-md-12" style="display:flex;justify-content: center;">
                        <?= Html::submitButton('Сохранить', [
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#7F9FC5',
                                'color' => '#ffffff',
                                'width' => '180px',
                                'height' => '40px',
                                'font-size' => '16px',
                                'text-transform' => 'uppercase',
                                'font-weight' => '700',
                                'padding-top' => '9px',
                                'border-radius' => '8px',
                                'margin-top' => '28px'
                            ]
                        ]) ?>
                    </div>
                </div>


                <?php
                ActiveForm::end();
                ?>

            </div>

        </div>
    </div>

</div>


<?php
// Модальное окно - Запрет на следующий шаг
Modal::begin([
    'options' => ['id' => 'next_step_error', 'class' => 'next_step_error'],
    'size' => 'modal-md',
    'header' => '<h3 class="text-center" style="color: #F2F2F2; padding: 0 30px;">Данный этап не доступен</h3>',
]); ?>

<h4 class="text-center" style="color: #F2F2F2; padding: 0 30px;">
    Пройдите последовательно этапы подтверждения продукта (MVP). Далее переходите к генерации бизнес-модели.
</h4>

<?php Modal::end(); ?>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/confirm_mvp_create.js'); ?>
