<?php

use app\models\SortForm;
use app\modules\admin\models\form\SearchForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$this->title ?: $this->title = 'Портфель проектов';

/**
 * @var SortForm $sortModel
 * @var array $show_count_projects
 * @var SearchForm $searchModel
 * @var bool $pageClientProjects
 */

?>

<style>
    .select2-container--krajee .select2-selection {
        font-size: 20px;
        height: 45px;
        padding: 8px 30px 15px 15px;
        border-radius: 8px;
    }
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }
</style>

<div class="admin-projects-result">

    <div class="row" style="display:flex; align-items: center;">

        <div class="col-md-4" style=" padding-left: 25px;">
            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>

        <div class="col-md-4 mt-15">

            <?php $formSearch = ActiveForm::begin([
                'options' => ['class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]) ?>

                <?= $formSearch->field($searchModel, 'search', ['template' => '{input}'])
                    ->textInput([
                    'id' => 'search_project_name',
                    'placeholder' => 'Поиск проекта',
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'autocomplete' => 'off'])
                    ->label(false) ?>

            <?php ActiveForm::end(); ?>

        </div>

        <div class="col-md-4">
            <div class="row pull-right select_count_projects">

                <div class="col-md-4" style="padding: 0;">
                    <div class="pull-right" style="padding-top: 5px; font-size: 18px;">Показывать:</div>
                </div>

                <div class="col-md-8" style="">

                    <?php $form = ActiveForm::begin([
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]) ?>

                        <?= $form->field($sortModel, 'field',
                            ['template' => '<div style="padding-top: 15px;">{input}</div>'])
                            ->widget(Select2::class, [
                                'data' => $show_count_projects,
                                'options' => ['id' => 'field_count_projects',],
                                'hideSearch' => true, //Скрытие поиска
                            ]) ?>

                    <?php ActiveForm::end(); ?>

                </div>

            </div>
        </div>
    </div>


    <div class="containerHeaderDataOfTableResultProject">
        <div class="headerDataOfTableResultProject">
            <div class="blocks_for_double_header_level">

                <div class="one_block_for_double_header_level">

                    <div class="text-center stage">Сегмент</div>

                    <div class="columns_stage">
                        <div class="text-center column_segment_name">Наименование</div>
                        <div class="text-center regular_column">Статус</div>
                        <div class="text-center regular_column">Дата генер.</div>
                        <div class="text-center regular_column">Дата подтв.</div>
                    </div>

                </div>


                <div class="one_block_for_double_header_level">

                    <div class="text-center stage">Проблемы сегмента</div>

                    <div class="columns_stage">
                        <div class="text-center first_regular_column_of_stage">Обознач.</div>
                        <div class="text-center regular_column">Статус</div>
                        <div class="text-center regular_column">Дата генер.</div>
                        <div class="text-center regular_column">Дата подтв.</div>
                    </div>

                </div>


                <div class="one_block_for_double_header_level">

                    <div class="text-center stage">Ценностные предложения</div>

                    <div class="columns_stage">
                        <div class="text-center regular_column first_regular_column_of_stage">Обознач.</div>
                        <div class="text-center regular_column">Статус</div>
                        <div class="text-center regular_column">Дата генер.</div>
                        <div class="text-center regular_column">Дата подтв.</div>
                    </div>

                </div>


                <div class="one_block_for_double_header_level">

                    <div class="text-center stage">MVP (продукт)</div>

                    <div class="columns_stage">
                        <div class="text-center first_regular_column_of_stage">Обознач.</div>
                        <div class="text-center regular_column">Статус</div>
                        <div class="text-center regular_column">Дата генер.</div>
                        <div class="text-center regular_column">Дата подтв.</div>
                    </div>

                </div>

            </div>

            <div class="blocks_for_single_header_level text-center">
                <div class="">Бизнес-модель</div>
            </div>

        </div>
    </div>


    <div class="allContainersDataOfTableResultProject">

    </div>

</div>


<!--Подключение скриптов-->
<?php if (!$pageClientProjects) : ?>
    <?php $this->registerJsFile('@web/js/admin_project_portfolio_index.js'); ?>
<?php else : ?>
    <?php $this->registerJsFile('@web/js/admin_client_project_portfolio_index.js'); ?>
<?php endif; ?>