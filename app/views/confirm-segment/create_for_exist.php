<?php

use app\models\ConfirmSource;
use app\models\forms\FormCreateConfirmSegment;
use app\models\ProblemVariant;
use app\models\Projects;
use app\models\Segments;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Подтверждение гипотезы целевого сегмента';
$this->registerCssFile('@web/css/confirm-segments-create-style.css');

/**
 * @var FormCreateConfirmSegment $model
 * @var Segments $segment
 * @var Projects $project
 */

?>

<div class="segment-confirm-create">

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
        <div class="item-stage active"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
    </div>

    <div class="arrow_links_router_mobile">
        <div class="arrow_link_router_mobile_left">
            <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                Url::to(['/segments/index', 'id' => $project->getId()])) ?>
        </div>
        <div class="text-stage">2/9. Подтверждение гипотез целевых сегментов</div>
        <div class="arrow_link_router_mobile_right">
            <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
        </div>
    </div>

    <div class="header-title-confirm-hypothesis-mobile">
        <div style="overflow: hidden; max-width: 90%;">Сегмент: <?= $segment->getName() ?></div>
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


        <div class="active_navigation_block navigation_block">
            <div class="stage_number">2</div>
            <div>Подтверждение гипотез целевых сегментов</div>
        </div>

        <div class="no_transition_navigation_block navigation_block">
            <div class="stage_number">3</div>
            <div>Генерация гипотез проблем сегментов</div>
        </div>

        <div class="no_transition_navigation_block navigation_block">
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
            <span>Сегмент:</span>
            <?= $segment->getName() ?>
        </div>

        <?= Html::a('Данные сегмента', ['/segments/show-all-information', 'id' => $segment->getId()], [
            'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-2 openAllInformationSegment link_in_the_header',
        ]) ?>

        <?= Html::a('Трэкшн карта сегмента', ['/segments/show-roadmap', 'id' => $segment->getId()], [
            'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-2 openRoadmapSegment link_in_the_header text-center',
        ]) ?>

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

                <div class="row pt-20 pb-5 pl-5">

                    <div class="col-md-12 bolder">
                        Цель проекта
                    </div>

                    <div class="col-md-12">
                        <?= $project->getPurposeProject() ?>
                    </div>

                </div>

                <?php
                $form = ActiveForm::begin([
                    'id' => 'new_confirm_segment',
                    'action' => Url::to(['/confirm-segment/save-confirm', 'id' => $segment->getId(), 'existDesc' => true]),
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

                <div class="row mt-15">
                    <div class="col-md-12 pl-20 bolder font-size-18">
                        Выявленные проблемы <span class="color-red">*</span>
                    </div>
                </div>

                <div class="container-problem-variants">
                    <?php if (!$model->problemVariants): ?>
                        <div class="item-variants p-0">
                            <div class="row row-variant row-variant-form-create-0 mb-5">

                                <?= $form->field($model, "problemVariants[0][description]", [
                                    'template' => '<div class="col-md-12 mt-15">{input}</div>'
                                ])->textarea([
                                    'rows' => 2,
                                    'maxlength' => 2000,
                                    'placeholder' => '',
                                    'class' => 'style_form_field_respond form-control',
                                    'id' => 'problemVariants-0',
                                ])->label(false) ?>

                            </div>
                        </div>
                    <?php else: ?>
                        <div class="item-variants p-0">
                            <?php foreach ($model->problemVariants as $key => $value): ?>

                                <div class="row row-variant row-variant-form-create-<?= $key ?> mb-5">

                                        <?= $form->field($model, "problemVariants[".$key."][description]", [
                                            'template' => '<div class="col-md-12 mt-15">{input}</div>'
                                        ])->textarea([
                                            'rows' => 2,
                                            'maxlength' => 2000,
                                            'placeholder' => '',
                                            'class' => 'style_form_field_respond form-control',
                                            'id' => 'problemVariants-'.$key,
                                        ])->label(false) ?>

                                    <?php if ($key > 0): ?>
                                        <div class="col-md-12 mt-15">

                                            <?= Html::button('Удалить проблему', [
                                                'id' => 'remove-problem-variant-form-'.$key,
                                                'class' => 'remove-problem-variant btn btn-default remove_problem_variant_for_create',
                                                'style' => [
                                                    'display' => 'flex',
                                                    'align-items' => 'center',
                                                    'justify-content' => 'center',
                                                    'background' => '#707F99',
                                                    'color' => '#FFFFFF',
                                                    'width' => '200px',
                                                    'height' => '40px',
                                                    'font-size' => '16px',
                                                    'text-transform' => 'uppercase',
                                                    'font-weight' => '700',
                                                    'padding-top' => '9px',
                                                    'border-radius' => '8px',
                                                ]
                                            ]) ?>
                                        </div>
                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?= Html::button('Добавить проблему', [
                    'id' => 'add_problem_variant_form',
                    'class' => 'btn btn-default add_problem_variant_form mt-15',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'color' => '#FFFFFF',
                        'justify-content' => 'center',
                        'background' => '#52BE7F',
                        'width' => '200px',
                        'height' => '40px',
                        'text-align' => 'left',
                        'font-size' => '16px',
                        'text-transform' => 'uppercase',
                        'font-weight' => '700',
                        'padding-top' => '9px',
                        'border-radius' => '8px',
                        'margin-right' => '5px',
                    ]
                ]) ?>

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

    <div class="form_problem_variants display-none">

        <?php
        $form = ActiveForm::begin([
            'id' => 'form_problem_variants'
        ]); ?>

        <div class="form_problem_variants_inputs">

            <div class="row mb-5 row-variant row-variant-">

                <?= $form->field(new ProblemVariant(), "[0]description", [
                    'template' => '<div class="col-md-12 mt-15">{input}</div>'
                ])->textarea([
                    'rows' => 2,
                    'maxlength' => 2000,
                    'placeholder' => '',
                    'class' => 'style_form_field_respond form-control',
                    'id' => 'problemVariants-',
                ])->label(false) ?>

                <div class="col-md-12 mt-15">

                    <?= Html::button('Удалить проблему', [
                        'id' => 'remove-problem-variant-',
                        'class' => 'remove-problem-variant btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'background' => '#707F99',
                            'color' => '#FFFFFF',
                            'width' => '200px',
                            'height' => '40px',
                            'font-size' => '16px',
                            'text-transform' => 'uppercase',
                            'font-weight' => '700',
                            'padding-top' => '9px',
                            'border-radius' => '8px',
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/confirm_segment_create.js'); ?>
