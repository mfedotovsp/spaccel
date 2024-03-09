<?php

use app\models\RatesPlan;
use yii\helpers\Html;

$this->title = 'Тарифные планы';
$this->registerCssFile('@web/css/rates-planes-style.css');

/**
 * @var RatesPlan[] $ratesPlans
 */

?>

<div class="container-fluid">

    <div class="row hi-line-page">
        <div class="col-md-6" style="margin-top: 35px; padding-left: 25px;">
            <?= Html::a('Тарифные планы' . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>
        </div>
        <div class="col-md-6" style="margin-top: 30px;">
            <?=  Html::a( '<div class="new_rates_plan_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Новый тарифный план</div></div>', ['/admin/rates-plans/get-form-create'],
                ['id' => 'showRatesPlanToCreate', 'class' => 'new_rates_plan_link_plus pull-right']
            ) ?>
        </div>
    </div>

    <div class="row" style="display:flex; align-items: center; padding: 30px 0 15px 0; font-weight: 700;">

        <div class="col-md-2" style="padding-left: 30px;">
            Наименование тарифа
        </div>

        <div class="col-md-5">
            Описание тарифа
        </div>

        <div class="col-md-3 text-center">
            Максимальное количество проектантов / трекеров
        </div>

        <div class="col-md-2 text-center">
            Дата создания
        </div>

    </div>

    <div class="row block_all_rates_plans">

        <?php foreach ($ratesPlans as $ratesPlan) : ?>

            <div class="row container-one_rates_plan">

                <div class="col-md-2" style="padding-left: 25px;">
                    <?= $ratesPlan->getName() ?>
                </div>

                <div class="col-md-5" style="padding-left: 10px;">
                    <?= $ratesPlan->getDescription() ?>
                </div>

                <div class="col-md-3 text-center">
                    <?= $ratesPlan->getMaxCountProjectUser() . ' проектантов / ' . $ratesPlan->getMaxCountTracker() . ' трекера(-ов)' ?>
                </div>

                <div class="col-md-2 text-center">
                    <?= date('d.m.Y', $ratesPlan->getCreatedAt()) ?>
                </div>

            </div>

        <?php endforeach; ?>

    </div>
</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/rates_plans.js'); ?>
