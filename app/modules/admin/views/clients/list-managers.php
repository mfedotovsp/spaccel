<?php

use app\models\CustomerManager;
use app\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;

/**
 * @var CustomerManager $customerManager
 * @var User[] $managers
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
    'id' => 'formChangeManagerToClient',
    'action' => Url::to(['/admin/clients/add-manager']),
    'options' => ['class' => 'g-py-15'],
    'errorCssClass' => 'u-has-error-v1',
    'successCssClass' => 'u-has-success-v1-1',
]); ?>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <?= $form->field($customerManager, 'user_id', [
                'template' => '{input}',
            ])->widget(Select2::class, [
                'data' => ArrayHelper::map($managers,'id','username'),
                'options' => ['id' => 'selectChangeManagerToClient'],
                'disabled' => false,  //Сделать поле неактивным
                'hideSearch' => true, //Скрытие поиска
            ]) ?>
        </div>
        <div class="col-md-2"></div>
    </div>

    <?= $form->field($customerManager, 'client_id')->hiddenInput()->label(false) ?>

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
