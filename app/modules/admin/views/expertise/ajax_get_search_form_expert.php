<?php

use app\models\Projects;
use app\modules\admin\models\form\SearchFormExperts;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use app\models\ExpertType;

/**
 * @var Projects $project
 * @var SearchFormExperts $searchFormExperts
 */

?>

<style>
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 16px;
        padding: 3px 8px;
        border-radius: 12px;
        border: 1px solid #828282;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
</style>


<div class="search-experts">

    <div class="row search-form-experts">

        <?php $form = ActiveForm::begin([
            'id' => 'search_form_experts-'.$project->getId(),
            'action' => Url::to(['/admin/expertise/search-experts', 'project_id' => $project->getId()]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <div class="col-md-12">

            <label for="SearchFormExperts[name]">Логин эксперта</label>
            <?= $form->field($searchFormExperts, 'name', ['template' => '{input}'])
                ->textInput([
                    'class' => 'style_form_field_respond',
                    'autocomplete' => 'off'])
                ->label(false) ?>

            <label for="SearchFormExperts[scope_professional_competence]">Сфера профессиональной компетенции</label>
            <?= $form->field($searchFormExperts, 'scope_professional_competence', ['template' => '{input}'])
                ->textInput([
                    'class' => 'style_form_field_respond',
                    'autocomplete' => 'off'])
                ->label(false) ?>

            <label for="SearchFormExperts[keywords]">Ключевые слова</label>
            <?= $form->field($searchFormExperts, 'keywords', ['template' => '{input}'])
                ->textInput([
                    'class' => 'style_form_field_respond',
                    'autocomplete' => 'off'])
                ->label(false) ?>

            <?= $form->field($searchFormExperts, 'type', [
                'template' => '<label>Тип экспертной деятельности</label><div>{input}</div>'
            ])->widget(Select2::class, [
                'value' => [],
                'data' => ExpertType::getListTypes(),
                'options' => [
                    'id' => 'types-expert-'.$project->getId(),
                    'multiple' => true,
                ],
                'toggleAllSettings' => [
                    'selectLabel' => '<i class="fas fa-check-circle"></i> Выбрать все',
                    'unselectLabel' => '<i class="fas fa-times-circle"></i> Убрать все',
                    'selectOptions' => ['class' => 'text-success'],
                    'unselectOptions' => ['class' => 'text-danger'],
                ],
            ]) ?>

            <div class="submit_search_experts">
                <?= Html::submitButton('Применить', [
                    'id' => 'search_submit_experts-'.$project->getId(),
                    'class' => 'btn btn-default search_submit_experts',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'color' => '#FFFFFF',
                        'background' => '#707F99',
                        'width' => '140px',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                    ]
                ]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <!--Блок для вывода результата поиска экспертов-->
    <div id="result_search-<?= $project->getId() ?>" class="result-search-experts row" style="margin: 20px 5px;"></div>

</div>
