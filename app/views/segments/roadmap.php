<?php

use app\models\Roadmap;
use yii\helpers\Html;

/**
 * @var Roadmap $roadmap
 */


$segment_name = $roadmap->getProperty('segment_name');
if (mb_strlen($segment_name) > 25) {
    $segment_name = mb_substr($segment_name, 0, 25) . '...';
}


if ($roadmap->getProperty('fact_segment_confirm') != null) {

    if ($roadmap->getProperty('fact_segment_confirm') <= $roadmap->getProperty('plan_segment_confirm')){

        $fact_segment_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_segment_confirm')), ['/confirm-segment/view', 'id' => $roadmap->getProperty('id_confirm_segment')], ['class' => 'roadmap_block_date_link_success']);
    }else {

        $fact_segment_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_segment_confirm')), ['/confirm-segment/view', 'id' => $roadmap->getProperty('id_confirm_segment')], ['class' => 'roadmap_block_date_link_danger']);
    }
}else {
    $fact_segment_confirm = '_ _ _ _ _ _';
}


if ($roadmap->getProperty('fact_gps') != null) {

    if ($roadmap->getProperty('fact_gps') <= $roadmap->getProperty('plan_gps')){

        $fact_gps = Html::a(date('d.m.y',$roadmap->getProperty('fact_gps')), ['/problems/index', 'id' => $roadmap->getProperty('id_page_last_problem')], ['class' => 'roadmap_block_date_link_success']);
    }else {

        $fact_gps = Html::a(date('d.m.y',$roadmap->getProperty('fact_gps')), ['/problems/index', 'id' => $roadmap->getProperty('id_page_last_problem')], ['class' => 'roadmap_block_date_link_danger']);
    }
}else {
    $fact_gps = '_ _ _ _ _ _';
}


if ($roadmap->getProperty('fact_gps_confirm') != null) {

    if ($roadmap->getProperty('fact_gps_confirm') <= $roadmap->getProperty('plan_gps_confirm')){

        $fact_gps_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_gps_confirm')), ['/confirm-problem/view', 'id' => $roadmap->getProperty('id_confirm_problem')], ['class' => 'roadmap_block_date_link_success']);
    }else {

        $fact_gps_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_gps_confirm')), ['/confirm-problem/view', 'id' => $roadmap->getProperty('id_confirm_problem')], ['class' => 'roadmap_block_date_link_danger']);
    }
}else {
    $fact_gps_confirm = '_ _ _ _ _ _';
}


if ($roadmap->getProperty('fact_gcp') != null) {

    if ($roadmap->getProperty('fact_gcp') <= $roadmap->getProperty('plan_gcp')){

        $fact_gcp = Html::a(date('d.m.y',$roadmap->getProperty('fact_gcp')), ['/gcps/index', 'id' => $roadmap->getProperty('id_page_last_gcp')], ['class' => 'roadmap_block_date_link_success']);
    }else {

        $fact_gcp = Html::a(date('d.m.y',$roadmap->getProperty('fact_gcp')), ['/gcps/index', 'id' => $roadmap->getProperty('id_page_last_gcp')], ['class' => 'roadmap_block_date_link_danger']);
    }
}else {
    $fact_gcp = '_ _ _ _ _ _';
}


if ($roadmap->getProperty('fact_gcp_confirm') != null) {

    if ($roadmap->getProperty('fact_gcp_confirm') <= $roadmap->getProperty('plan_gcp_confirm')){

        $fact_gcp_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_gcp_confirm')), ['/confirm-gcp/view', 'id' => $roadmap->getProperty('id_confirm_gcp')], ['class' => 'roadmap_block_date_link_success']);
    }else {

        $fact_gcp_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_gcp_confirm')), ['/confirm-gcp/view', 'id' => $roadmap->getProperty('id_confirm_gcp')], ['class' => 'roadmap_block_date_link_danger']);
    }
}else {
    $fact_gcp_confirm = '_ _ _ _ _ _';
}


if ($roadmap->getProperty('fact_mvp') != null) {

    if ($roadmap->getProperty('fact_mvp') <= $roadmap->getProperty('plan_mvp')){

        $fact_mvp = Html::a(date('d.m.y',$roadmap->getProperty('fact_mvp')), ['/mvps/index', 'id' => $roadmap->getProperty('id_page_last_mvp')], ['class' => 'roadmap_block_date_link_success']);
    }else {

        $fact_mvp = Html::a(date('d.m.y',$roadmap->getProperty('fact_mvp')), ['/mvps/index', 'id' => $roadmap->getProperty('id_page_last_mvp')], ['class' => 'roadmap_block_date_link_danger']);
    }
}else {
    $fact_mvp = '_ _ _ _ _ _';
}


if ($roadmap->getProperty('fact_mvp_confirm') != null) {

    if ($roadmap->getProperty('fact_mvp_confirm') <= $roadmap->getProperty('plan_mvp_confirm')){

        $fact_mvp_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_mvp_confirm')), ['/confirm-mvp/view', 'id' => $roadmap->getProperty('id_confirm_mvp')], ['class' => 'roadmap_block_date_link_success']);
    }else {

        $fact_mvp_confirm = Html::a(date('d.m.y',$roadmap->getProperty('fact_mvp_confirm')), ['/confirm-mvp/view', 'id' => $roadmap->getProperty('id_confirm_mvp')], ['class' => 'roadmap_block_date_link_danger']);
    }
}else {
    $fact_mvp_confirm = '_ _ _ _ _ _';
}

?>


<div class="content_roadmap">

    <div class="roadmap_row_header">

        <div class="roadmap_block_stage">Сегмент</div>

        <div class="roadmap_block_stage text-center">
            <div>Генерация ГЦС</div>
            <div>Дата создания</div>
        </div>

        <div class="roadmap_block_stage text-center">
            <div>Подтверждение ГЦС</div>
            <div>
                <div>План</div>
                <div>Факт</div>
            </div>
        </div>

        <div class="roadmap_block_stage text-center">
            <div>Генерация ГПС</div>
            <div>
                <div>План</div>
                <div>Факт</div>
            </div>
        </div>

        <div class="roadmap_block_stage text-center">
            <div>Подтверждение ГПС</div>
            <div>
                <div>План</div>
                <div>Факт</div>
            </div>
        </div>

        <div class="roadmap_block_stage text-center">
            <div>Разработка ГЦП</div>
            <div>
                <div>План</div>
                <div>Факт</div>
            </div>
        </div>

        <div class="roadmap_block_stage text-center">
            <div>Подтверждение ГЦП</div>
            <div>
                <div>План</div>
                <div>Факт</div>
            </div>
        </div>

        <div class="roadmap_block_stage text-center">
            <div>Разработка ГMVP</div>
            <div>
                <div>План</div>
                <div>Факт</div>
            </div>
        </div>

        <div class="roadmap_block_stage text-center">
            <div>Подтверждение ГMVP</div>
            <div>
                <div>План</div>
                <div>Факт</div>
            </div>
        </div>

    </div>


    <div class="roadmap_row_dates" style="width: 1120px;">

        <div class="roadmap_block_name_segment">
            <?= $segment_name ?>
        </div>

        <div class="roadmap_block_date_segment">
            <?= date('d.m.y',$roadmap->getProperty('created_at')); ?>
        </div>

        <div class="roadmap_block_date">

            <div>
                <?= date('d.m.y',$roadmap->getProperty('plan_segment_confirm')); ?>
            </div>

            <div>
                <?= $fact_segment_confirm ?>
            </div>

        </div>

        <div class="roadmap_block_date">

            <div>
                <?= date('d.m.y',$roadmap->getProperty('plan_gps')); ?>
            </div>

            <div>
                <?= $fact_gps ?>
            </div>

        </div>

        <div class="roadmap_block_date">

            <div>
                <?= date('d.m.y',$roadmap->getProperty('plan_gps_confirm')); ?>
            </div>

            <div>
                <?= $fact_gps_confirm ?>
            </div>

        </div>

        <div class="roadmap_block_date">

            <div>
                <?= date('d.m.y',$roadmap->getProperty('plan_gcp')); ?>
            </div>

            <div>
                <?= $fact_gcp ?>
            </div>

        </div>

        <div class="roadmap_block_date">

            <div>
                <?= date('d.m.y',$roadmap->getProperty('plan_gcp_confirm')); ?>
            </div>

            <div>
                <?= $fact_gcp_confirm ?>
            </div>

        </div>

        <div class="roadmap_block_date">

            <div>
                <?= date('d.m.y',$roadmap->getProperty('plan_mvp')); ?>
            </div>

            <div>
                <?= $fact_mvp ?>
            </div>

        </div>

        <div class="roadmap_block_date">

            <div>
                <?= date('d.m.y',$roadmap->getProperty('plan_mvp_confirm')); ?>
            </div>

            <div>
                <?= $fact_mvp_confirm ?>
            </div>

        </div>

    </div>

</div>