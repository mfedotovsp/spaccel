<?php

use app\models\ConfirmGcp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ConfirmSource;
use app\models\forms\FormCreateConfirmDescription;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Подтверждение MVP';
$this->registerCssFile('@web/css/confirm-mvp-create-style.css');

/**
 * @var FormCreateConfirmDescription $model
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


    <div class="row">

        <div class="container-fluid container-data">

            <div class="row row_header_data">

                <div class="col-md-12" style="padding: 5px 0 0 0;">
                    <?= Html::a('Подтверждение (при наличии необходимой информации)', ['#'],[
                        'class' => 'link_to_instruction_page', 'onclick' => 'return false;',
                        'style' => ['cursor' => 'default']
                    ]) ?>
                </div>

            </div>

            <div class="container-fluid">

                <div class="content-view-data-confirm mb-15">

                    <div class="row">
                        <div class="col-md-12">Цель проекта</div>
                        <div class="col-md-12"><?= $project->getPurposeProject() ?></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">Формулировка минимально жизнеспособного продукта, который проверяем</div>
                        <div class="col-md-12"><?= $mvp->getDescription() ?></div>
                    </div>

                </div>

                <?php

                $form = ActiveForm::begin([
                    'id' => 'new_confirm_mvp',
                    'action' => Url::to(['/confirm-mvp/save-confirm', 'id' => $mvp->getId(), 'existDesc' => true]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]);

                ?>

                <div class="row pt-5 pb-5">

                    <?php $placeholder = 'Проведены исследования обзоров. Сделаны выводы, что значимой гипотезой выявлена гипотеза "А". Обоснованием может служить следующая информация: ...' ?>

                    <?= $form->field($model, 'description[description]', [
                        'template' => '<div class="col-md-12 pl-20">{label} <span class="color-red bolder">*</span></div><div class="col-md-12">{input}</div>'
                    ])->textarea([
                        'rows' => 3,
                        'maxlength' => true,
                        'placeholder' => $placeholder,
                        'class' => 'style_form_field_respond form-control',
                    ])->label('Добавьте имеющуюся информацию') ?>

                </div>

                <div class="row pt-5 pb-5">
                    <div class="col-md-12">
                        <?= $form->field($model, 'selectSources', [
                            'template' => '<div class="pl-10">{label} <span class="color-red bolder">*</span></div><div>{input}</div>',
                        ])->widget(Select2::class, [
                            'data' => ConfirmSource::dataSelect(),
                            'options' => [
                                'id' => 'select-confirm-source-' . $segment->getId(),
                                'class' => 'select-confirm-source',
                                'multiple' => true,
                                'required' => true,
                            ],
                            'disabled' => false,  //Сделать поле неактивным
                            'hideSearch' => true, //Скрытие поиска
                        ])->label('Выберите источники информации') ?>
                    </div>
                </div>

                <?php foreach (ConfirmSource::dataSelect() as $key => $value): ?>
                    <?php if (in_array($key, $model->selectSources)): ?>

                        <div class="row pt-5 pb-5 select-source-option select-confirm-source-option-<?= $key ?>">
                            <?= $form->field($model, 'confirmSources['.$key.'][comment]', [
                                'template' => '<div class="col-md-12 pl-20">{label} <span class="color-red bolder">*</span></div><div class="col-md-12">{input}</div>'
                            ])->textarea([
                                'rows' => 3,
                                'maxlength' => 2000,
                                'placeholder' => '',
                                'class' => 'style_form_field_respond form-control',
                            ])->label($value) ?>

                            <div class="add_files col-md-12 pl-20 mt-15">
                                <div style="margin-top: -5px; padding-left: 5px;">
                                    <label>Прикрепленные файлы</label>
                                    <p style="margin-top: -5px; color: #BDBDBD;">
                                        (максимум 5 файлов - png, jpg, jpeg, pdf, txt, doc, docx, xls)
                                    </p>
                                </div>
                                <div class="pl-5">
                                    <?= $form->field($model, 'files['.$key.'][]', [
                                        'template' => "{label}\n{input}"
                                    ])->fileInput([
                                        'id' => 'sourceFiles-'.$key,
                                        'multiple' => true,
                                        'accept' => 'text/plain, application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, image/x-png, image/jpeg'
                                    ])->label(false) ?>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>

                        <div class="row pt-5 pb-5 display-none select-source-option select-confirm-source-option-<?= $key ?>">
                            <?= $form->field($model, 'confirmSources['.$key.'][comment]', [
                                'template' => '<div class="col-md-12 pl-20">{label} <span class="color-red bolder">*</span></div><div class="col-md-12">{input}</div>'
                            ])->textarea([
                                'rows' => 3,
                                'maxlength' => 2000,
                                'placeholder' => '',
                                'class' => 'style_form_field_respond form-control',
                            ])->label($value) ?>

                            <div class="add_files col-md-12 pl-20 mt-15">
                                <div style="margin-top: -5px; padding-left: 5px;">
                                    <label>Прикрепленные файлы</label>
                                    <p style="margin-top: -5px; color: #BDBDBD;">
                                        (максимум 5 файлов - png, jpg, jpeg, pdf, txt, doc, docx, xls)
                                    </p>
                                </div>
                                <div class="pl-5">
                                    <?= $form->field($model, 'files['.$key.'][]', [
                                        'template' => "{label}\n{input}"
                                    ])->fileInput([
                                        'id' => 'sourceFiles-'.$key,
                                        'multiple' => true,
                                        'accept' => 'text/plain, application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, image/x-png, image/jpeg'
                                    ])->label(false) ?>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="row errors text-danger pt-20 pb-5 pl-20 text-center"></div>
                <div class="error_files_count text-danger mt-5 pl-5 text-center display-none">
                    Превышено максимальное количество файлов для загрузки.
                </div>

                <div class="form-group row">
                    <div class="col-md-12" style="display:flex;justify-content: center;">
                        <?= Html::submitButton('Сохранить', [
                            'id' => 'save_create_form',
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


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/confirm_mvp_create.js'); ?>
