<?php

use app\models\Projects;
use app\modules\admin\models\form\SearchForm;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Назначение экспертов на проекты';
$this->registerCssFile('@web/css/expertise-tasks-style.css');

/**
 * @var Projects[] $projects
 * @var Pagination $pages
 * @var SearchForm $searchForm
 */

?>


<style>
    .select2-container--krajee-bs3 .select2-selection--multiple {
        font-size: 16px;
        border-radius: 12px;
        border: 1px solid #828282;
        height: 100%;
        padding-bottom: 2px;
        padding-top: 2px;
    }
    .select2-container--krajee-bs3 .select2-selection--multiple .select2-selection__choice,
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 16px;
    }
</style>


<div class="row expertise-tasks">

    <div class="col-md-7" style="margin-bottom: 15px; padding-left: 40px;">

        <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
            'class' => 'link_to_instruction_page open_modal_instruction_page',
            'title' => 'Инструкция', 'onclick' => 'return false'
        ]) ?>

    </div>

    <div class="col-md-2">

        <?= Html::button( 'Поиск проектов',[
            'id' => 'show_search_tasks',
            'class' => 'btn btn-default',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'background' => '#669999',
                'color' => '#FFFFFF',
                'width' => '100%',
                'height' => '40px',
                'font-size' => '24px',
                'border-radius' => '8px',
                'margin-bottom' => '15px'
            ],
        ]) ?>

    </div>

    <div class="col-md-3">

        <?= Html::a( 'Настройки коммуникаций',
            Url::to(['/client/communications/settings']),[
            'class' => 'btn btn-success',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'background' => '#52BE7F',
                'width' => '100%',
                'height' => '40px',
                'font-size' => '24px',
                'border-radius' => '8px',
                'margin-bottom' => '15px'
            ],
        ]) ?>

    </div>

    <div class="col-md-12 search-block">

        <?php $form = ActiveForm::begin([
            'id' => 'search_expertise_tasks',
            'action' => Url::to(['/client/expertise/tasks']),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <?= $form->field($searchForm, 'search', ['template' => '{input}'])
            ->textInput([
                'id' => 'search_tasks',
                'placeholder' => 'Поиск по названию и автору проекта (необходимо ввести не менее 5 символов)',
                'class' => 'style_form_field_respond',
                'minlength' => 5,
                'autocomplete' => 'off'])
            ->label(false) ?>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="col-md-12 expertise-tasks-content">

        <?= $this->render('ajax_search_tasks', [
            'projects' => $projects,
            'pages' => $pages
        ]) ?>

    </div>

</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/expertise_task.js'); ?>
