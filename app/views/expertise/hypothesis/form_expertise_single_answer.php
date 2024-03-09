<?php

use app\models\forms\expertise\FormExpertiseSingleAnswer;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\models\Expertise;
use app\models\ExpertType;

/**
 * @var FormExpertiseSingleAnswer $model
 * @var string $stage
 * @var int $stageId
 */

?>

<div><span class="bolder">Тип деятельности:</span> <?= ExpertType::getListTypes()[$model->getExpertise()->getTypeExpert()] ?></div>
<hr>

<?php if ((array)$model->getAnswerOptions()) : ?>

<?php $form = ActiveForm::begin([
    'id' => 'expertise_create_form',
    'options' => ['class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]); ?>

    <?= $form->field($model, 'checkbox')->checkBoxList($model->getAnswerOptions(), [
        'item' => static function($index, $label, $name, $checked, $value) {
            !empty($checked) ? $checked = 'checked' : $checked = '';
            return "<label class='checkbox col-md-12' style='font-weight: normal; margin-left: 5px;'><input class='checkbox-expertise' type='checkbox' $checked name='$name' value='$value'>$label</label>";
        }
    ]) ?>

    <div class="row" style="margin-bottom: 15px;">
        <?= $form->field($model, 'comment', [
            'template' => '<div class="col-md-12" style="padding-left: 20px;">{label}</div><div class="col-md-12">{input}</div>'
        ])->textarea([
            'rows' => 2,
            'required' => true,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>
    </div>

    <div class="row">
        <div class="col-md-12" style="display: flex; justify-content: space-between; margin-top: 10px; margin-bottom: 10px;">

            <?= Html::a('Назад', ['/expertise/get-list', 'stage' => $stage, 'stageId' => $stageId],[
                'id' => 'get_list_expertise',
                'class' => 'btn btn-default link-get-list-expertise',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'width' => '180px',
                    'height' => '40px',
                    'font-size' => '24px',
                    'border-radius' => '8px',
                    'margin-right' => '10px'
                ]
            ]) ?>

            <?= Html::submitButton('Сохранить', [
                'id' => 'save_expertise',
                'class' => 'btn btn-default submit-expertise',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#669999',
                    'color' => '#FFFFFF',
                    'width' => '180px',
                    'height' => '40px',
                    'font-size' => '24px',
                    'border-radius' => '8px',
                    'margin-right' => '10px'
                ]
            ]) ?>

            <?php if ($model->getExpertise()->getCompleted() !== Expertise::COMPLETED) : ?>
                <?= Html::submitButton('Завершить', [
                    'id' => 'completed_expertise',
                    'class' => 'btn btn-success submit-expertise',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'background' => '#52BE7F',
                        'width' => '180px',
                        'height' => '40px',
                        'font-size' => '24px',
                        'border-radius' => '8px',
                    ]
                ]) ?>
            <?php endif; ?>

        </div>
    </div>

<?php ActiveForm::end(); ?>

<?php else : ?>

<p class="bolder">Для данного типа деятельности отсутствует экспертиза на данном этапе.</p>

<?php endif; ?>
