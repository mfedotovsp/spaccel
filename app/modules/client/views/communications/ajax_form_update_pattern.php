<?php

use app\modules\admin\models\form\FormUpdateCommunicationPattern;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\CommunicationTypes;

/**
 * @var FormUpdateCommunicationPattern $formPattern
 * @var array $selection_project_access_period
 */

?>

<div class="col-md-12 form-edit-pattern">
    <div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'update_pattern',
            'action' => Url::to([
                '/client/communications/update-pattern',
                'communicationType' => $formPattern->getCommunicationType(),
                'id' => $formPattern->getId()
            ]),
            'options' => ['class' => 'g-py-15'],
            'errorCssClass' => 'u-has-error-v1',
            'successCssClass' => 'u-has-success-v1-1',
        ]); ?>

        <?= $form->field($formPattern, 'description', [
            'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
        ])->textarea([
            'rows' => 1,
            'maxlength' => true,
            'class' => 'style_form_field_respond form-control',
            'placeholder' => '',
        ]) ?>

        <?php if ($formPattern->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) : ?>

            <?= $form->field($formPattern, 'project_access_period', [
                'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-2" style="margin-bottom: 15px;">{input}</div>',
            ])->widget(Select2::class, [
                'data' => $selection_project_access_period,
                'options' => ['id' => 'selection_project_access_period_update-'.$formPattern->getId()],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>

        <?php endif; ?>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
                    <?= Html::a('Отмена', ['/client/communications/cancel-edit-pattern', 'id' => $formPattern->getId()],[
                        'class' => 'btn btn-default cancel-edit-pattern',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'width' => '100%',
                            'height' => '40px',
                            'font-size' => '24px',
                            'border-radius' => '8px',
                            'margin-bottom' => '15px',
                        ]
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <?= Html::submitButton('Сохранить', [
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
                            'margin-bottom' => '15px',
                        ]
                    ]) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

