<?php

use app\models\ContractorProject;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Поиск и выбор исполнителей проектов';
$this->registerCssFile('@web/css/contractors-index-style.css');

/**
 * @var $formSearch ContractorProject
 * @var $projectOptions array
 * @var $activityOptions array
 */

?>

<style>
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 20px;
        height: 45px;
        padding: 8px 30px 15px 15px;
        border-radius: 8px;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }
</style>

<div class="contractors-index">

    <div class="row" style="margin-top: 35px; margin-bottom: 35px; padding-left: 25px; padding-right: 25px;">

        <div class="col-md-12">
            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>

    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'searchContractorsForm',
        'action' => Url::to(['/contractors/get-list']),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

        <div class="row" style="margin-bottom: 15px;">
            <?= $form->field($formSearch, 'activityId', [
                'template' => '<div class="col-md-12">{input}</div>',
            ])->widget(Select2::class, [
                'data' => $activityOptions,
                'options' => ['id' => 'selectActivityOptions', 'placeholder' => 'Выберите вид деятельности'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>
        </div>

        <div class="row" style="margin-bottom: 15px;">
            <?= $form->field($formSearch, 'projectId', [
                'template' => '<div class="col-md-12">{input}</div>',
            ])->widget(Select2::class, [
                'data' => $projectOptions,
                'options' => ['id' => 'selectProjectOptions', 'placeholder' => 'Выберите проект'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>
        </div>

        <div class="form-group row">
            <div class="col-md-12" style="display:flex;justify-content: center;">
                <?= Html::submitButton('Применить', [
                    'id' => 'submit_add_ContractorProject',
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

    <?php ActiveForm::end(); ?>

    <div class="container-fluid headers-contractor-ajax-list">

        <div class="row" style="display:flex; align-items: center; padding: 30px 0 15px 0; font-weight: 700;">

            <div class="col-md-5" style="padding-left: 30px;">
                Логин
            </div>

            <div class="col-md-3">
                Виды деятельности
            </div>

            <div class="col-md-2">
                Проектная деятельность
            </div>

            <div class="col-md-2"></div>

        </div>

    </div>

    <div class="row block_all_contractors"></div>

</div>

<!--Подключение скриптов-->
<?php
$this->registerJsFile('@web/js/contractors_add.js');
?>
