<?php

use app\models\ClientRatesPlan;
use app\models\RatesPlan;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;

/**
 * @var RatesPlan[] $ratesPlans
 * @var ClientRatesPlan $clientRatesPlan
 */

?>

<style>
    .select2-container--krajee-bs3 .select2-selection {
        font-size: 16px;
        height: 40px;
        padding-left: 15px;
        padding-top: 8px;
        padding-bottom: 15px;
        border-radius: 12px;
    }
    .select2-container--krajee-bs3 .select2-selection--single .select2-selection__arrow {
        height: 39px;
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'formChangeRatesPlanToClient',
    'action' => Url::to(['/admin/rates-plans/create-client-rates-plan']),
    'options' => ['class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]); ?>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <?= $form->field($clientRatesPlan, 'rates_plan_id', [
                'template' => '{input}',
            ])->widget(Select2::class, [
                'data' => ArrayHelper::map($ratesPlans,'id','name'),
                'options' => [
                    'id' => 'selectChangeRatesPlanToClient',
                    'placeholder' => 'Выберите тарифный план'
                ],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>
        </div>
        <div class="col-md-2"></div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'ClientRatesPlan[date_start]',
                'value' => $clientRatesPlan->getDateStart() ? date('d.m.Y', $clientRatesPlan->getDateStart()) : null,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy',
                    'startDate' => (new DateTime('Now'))->format('d-m-Y')
                ],
                'options' => [
                    'id' => "client_rates_plan_date_start",
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату начала',
                ]
            ]) ?>
        </div>
        <div class="col-md-2"></div>
    </div>

    <div class="row" style="margin-top: 15px;">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <?= DatePicker::widget([
                'type' => 2,
                'removeButton' => false,
                'name' => 'ClientRatesPlan[date_end]',
                'value' => $clientRatesPlan->getDateEnd() ? date('d.m.Y', $clientRatesPlan->getDateEnd()) : null,
                'readonly' => true,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                    'startDate' => (new DateTime('tomorrow'))->format('d-m-Y')
                ],
                'options' => [
                    'id' => "client_rates_plan_date_end",
                    'class' => 'text-center style_form_field_respond form-control',
                    'style' => ['padding-right' => '20px'],
                    'placeholder' => 'Выберите дату окончания',
                ]
            ]) ?>
        </div>
        <div class="col-md-2"></div>
    </div>

    <?= $form->field($clientRatesPlan, 'client_id')->hiddenInput()->label(false) ?>

    <div class="row" style="display:flex; justify-content: center;">
        <?= Html::submitButton('Сохранить', [
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
            'class' => 'btn btn-lg btn-success',
        ]) ?>
    </div>

<?php ActiveForm::end(); ?>
