<?php

use app\models\LocationWishList;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var LocationWishList $model
 */

?>

<div class="row container-fluid updateLocationForm">

    <?php $form = ActiveForm::begin([
        'id' => 'updateLocationForm',
        'action' => Url::to(['/admin/location/update', 'id' => $model->getId()]),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]);
    ?>

    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8">

        <?= $form->field($model, 'name', ['template' => '{input}'])
            ->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond',
                'autocomplete' => 'off'])
            ->label(false) ?>

    </div>

    <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
        <?= Html::button('Отмена', [
            'class' => 'btn btn-lg btn-default pull-right cancel-location-update',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'margin-bottom' => '15px',
                'height' => '45px',
                'width' => '100%',
                'border-radius' => '8px',
                'text-transform' => 'uppercase',
                'font-size' => '16px',
                'font-weight' => '700',
            ]
        ]) ?>
    </div>

    <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-lg btn-default pull-right',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'margin-bottom' => '15px',
                'background' => '#7F9FC5',
                'height' => '45px',
                'width' => '100%',
                'border-radius' => '8px',
                'text-transform' => 'uppercase',
                'font-size' => '16px',
                'color' => '#FFFFFF',
                'font-weight' => '700',
            ]
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

