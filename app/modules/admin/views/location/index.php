<?php

use app\models\LocationWishList;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Локации B2B компаний';
$this->registerCssFile('@web/css/wish-list-location-style.css');

/**
 * @var LocationWishList[] $models
 * @var LocationWishList $modelCreate
 */

?>

<div class="container-fluid">
    <div class="row hi-line-page">
        <div class="col-md-9" style="margin-top: 35px; padding-left: 25px;">
            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>

        <div class="col-md-3 " style="margin-top: 30px;">
            <?=  Html::a( '<div class="new_location_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новая локация</div></div>', ['#'],
                ['id' => 'showLocationToCreate', 'class' => 'new_location_link_plus pull-right']
            ) ?>
        </div>
    </div>
</div>

<div class="row container-fluid form_create_location">

    <?php $form = ActiveForm::begin([
        'id' => 'createLocationForm',
        'action' => Url::to(['/admin/location/create']),
        'options' => ['class' => 'g-py-15'],
        'errorCssClass' => 'u-has-error-v1',
        'successCssClass' => 'u-has-success-v1-1',
    ]); ?>

    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">

        <?= $form->field($modelCreate, 'name', ['template' => '{input}'])
            ->textInput([
                'maxlength' => true,
                'required' => true,
                'class' => 'style_form_field_respond',
                'autocomplete' => 'off'])
            ->label(false) ?>

    </div>

    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
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

<div class="wish-list-locations">


        <div class="headers-locations">
            Наименование локации
        </div>

    <div class="row container-fluid data-locations">
        <?= $this->render('index_ajax', ['models' => $models]) ?>
    </div>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/locations_page.js'); ?>
