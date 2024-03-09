<?php

use app\models\Roadmap;
use yii\helpers\Html;

/**
 * @var Roadmap[] $roadmaps
 */

$this->title = 'Трэкшн-карта проекта';

?>

<div class="row">
    <div class="col-xs-12 header-title-mobile"><?= $this->title ?></div>
</div>

<?php if ($roadmaps) : ?>

    <div class="container-fluid roadmap-mobile">

        <?php foreach ($roadmaps as $roadmap): ?>

            <?php $segment_name = $roadmap->getProperty('segment_name');

            if ($roadmap->getProperty('fact_segment_confirm') !== null) {

                if ($roadmap->getProperty('fact_segment_confirm') <= $roadmap->getProperty('plan_segment_confirm')){

                    $fact_segment_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_segment_confirm')), ['/confirm-segment/view', 'id' => $roadmap->getProperty('id_confirm_segment')], ['class' => 'roadmap_block_date_link_success']);
                }else {

                    $fact_segment_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_segment_confirm')), ['/confirm-segment/view', 'id' => $roadmap->getProperty('id_confirm_segment')], ['class' => 'roadmap_block_date_link_danger']);
                }
            }else {
                $fact_segment_confirm = '_ _ _ _ _ _';
            }


            if ($roadmap->getProperty('fact_gps') !== null) {

                if ($roadmap->getProperty('fact_gps') <= $roadmap->getProperty('plan_gps')){

                    $fact_gps = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gps')), ['/problems/index', 'id' => $roadmap->getProperty('id_page_last_problem')], ['class' => 'roadmap_block_date_link_success']);
                }else {

                    $fact_gps = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gps')), ['/problems/index', 'id' => $roadmap->getProperty('id_page_last_problem')], ['class' => 'roadmap_block_date_link_danger']);
                }
            }else {
                $fact_gps = '_ _ _ _ _ _';
            }


            if ($roadmap->getProperty('fact_gps_confirm') !== null) {

                if ($roadmap->getProperty('fact_gps_confirm') <= $roadmap->getProperty('plan_gps_confirm')){

                    $fact_gps_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gps_confirm')), ['/confirm-problem/view', 'id' => $roadmap->getProperty('id_confirm_problem')], ['class' => 'roadmap_block_date_link_success']);
                }else {

                    $fact_gps_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gps_confirm')), ['/confirm-problem/view', 'id' => $roadmap->getProperty('id_confirm_problem')], ['class' => 'roadmap_block_date_link_danger']);
                }
            }else {
                $fact_gps_confirm = '_ _ _ _ _ _';
            }


            if ($roadmap->getProperty('fact_gcp') !== null) {

                if ($roadmap->getProperty('fact_gcp') <= $roadmap->getProperty('plan_gcp')){

                    $fact_gcp = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gcp')), ['/gcps/index', 'id' => $roadmap->getProperty('id_page_last_gcp')], ['class' => 'roadmap_block_date_link_success']);
                }else {

                    $fact_gcp = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gcp')), ['/gcps/index', 'id' => $roadmap->getProperty('id_page_last_gcp')], ['class' => 'roadmap_block_date_link_danger']);
                }
            }else {
                $fact_gcp = '_ _ _ _ _ _';
            }


            if ($roadmap->getProperty('fact_gcp_confirm') !== null) {

                if ($roadmap->getProperty('fact_gcp_confirm') <= $roadmap->getProperty('plan_gcp_confirm')){

                    $fact_gcp_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gcp_confirm')), ['/confirm-gcp/view', 'id' => $roadmap->getProperty('id_confirm_gcp')], ['class' => 'roadmap_block_date_link_success']);
                }else {

                    $fact_gcp_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_gcp_confirm')), ['/confirm-gcp/view', 'id' => $roadmap->getProperty('id_confirm_gcp')], ['class' => 'roadmap_block_date_link_danger']);
                }
            }else {
                $fact_gcp_confirm = '_ _ _ _ _ _';
            }


            if ($roadmap->getProperty('fact_mvp') !== null) {

                if ($roadmap->getProperty('fact_mvp') <= $roadmap->getProperty('plan_mvp')){

                    $fact_mvp = Html::a(date('d.m.Y',$roadmap->getProperty('fact_mvp')), ['/mvps/index', 'id' => $roadmap->getProperty('id_page_last_mvp')], ['class' => 'roadmap_block_date_link_success']);
                }else {

                    $fact_mvp = Html::a(date('d.m.Y',$roadmap->getProperty('fact_mvp')), ['/mvps/index', 'id' => $roadmap->getProperty('id_page_last_mvp')], ['class' => 'roadmap_block_date_link_danger']);
                }
            }else {
                $fact_mvp = '_ _ _ _ _ _';
            }


            if ($roadmap->getProperty('fact_mvp_confirm') !== null) {

                if ($roadmap->getProperty('fact_mvp_confirm') <= $roadmap->getProperty('plan_mvp_confirm')){

                    $fact_mvp_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_mvp_confirm')), ['/confirm-mvp/view', 'id' => $roadmap->getProperty('id_confirm_mvp')], ['class' => 'roadmap_block_date_link_success']);
                }else {

                    $fact_mvp_confirm = Html::a(date('d.m.Y',$roadmap->getProperty('fact_mvp_confirm')), ['/confirm-mvp/view', 'id' => $roadmap->getProperty('id_confirm_mvp')], ['class' => 'roadmap_block_date_link_danger']);
                }
            }else {
                $fact_mvp_confirm = '_ _ _ _ _ _';
            }
            ?>

            <div class="row roadmap-mobile-header-segment">
                <div class="col-xs-8"><?= $segment_name ?></div>
                <div class="col-xs-4">
                    Создан <?= date('d.m.Y', $roadmap->getProperty('created_at')) ?>
                </div>
            </div>

            <div class="row roadmap-mobile-header-column">
                <div class="col-xs-4"></div>
                <div class="col-xs-4 text-center">План</div>
                <div class="col-xs-4 text-center">Факт</div>
            </div>

            <div class="row roadmap-mobile-simple-column">
                <div class="col-xs-4">Подтверждение ГЦС</div>
                <div class="col-xs-4 text-center">
                    <?= date('d.m.Y', $roadmap->getProperty('plan_segment_confirm')) ?>
                </div>
                <div class="col-xs-4 text-center"><?= $fact_segment_confirm ?></div>
            </div>

            <div class="row roadmap-mobile-simple-column">
                <div class="col-xs-4">Генерация ГПС</div>
                <div class="col-xs-4 text-center">
                    <?= date('d.m.Y', $roadmap->getProperty('plan_gps')) ?>
                </div>
                <div class="col-xs-4 text-center"><?= $fact_gps ?></div>
            </div>

            <div class="row roadmap-mobile-simple-column">
                <div class="col-xs-4">Подтверждение ГПС</div>
                <div class="col-xs-4 text-center">
                    <?= date('d.m.Y',$roadmap->getProperty('plan_gps_confirm')) ?>
                </div>
                <div class="col-xs-4 text-center"><?= $fact_gps_confirm ?></div>
            </div>

            <div class="row roadmap-mobile-simple-column">
                <div class="col-xs-4">Разработка ГЦП</div>
                <div class="col-xs-4 text-center">
                    <?= date('d.m.Y',$roadmap->getProperty('plan_gcp')) ?>
                </div>
                <div class="col-xs-4 text-center"><?= $fact_gcp ?></div>
            </div>

            <div class="row roadmap-mobile-simple-column">
                <div class="col-xs-4">Подтверждение ГЦП</div>
                <div class="col-xs-4 text-center">
                    <?= date('d.m.Y',$roadmap->getProperty('plan_gcp_confirm')) ?>
                </div>
                <div class="col-xs-4 text-center"><?= $fact_gcp_confirm ?></div>
            </div>

            <div class="row roadmap-mobile-simple-column">
                <div class="col-xs-4">Разработка MVP</div>
                <div class="col-xs-4 text-center">
                    <?= date('d.m.Y',$roadmap->getProperty('plan_mvp')) ?>
                </div>
                <div class="col-xs-4 text-center"><?= $fact_mvp ?></div>
            </div>

            <div class="row roadmap-mobile-simple-column">
                <div class="col-xs-4">Подтверждение MVP</div>
                <div class="col-xs-4 text-center">
                    <?= date('d.m.Y',$roadmap->getProperty('plan_mvp_confirm')) ?>
                </div>
                <div class="col-xs-4 text-center"><?= $fact_mvp_confirm ?></div>
            </div>

        <?php endforeach; ?>
    </div>

<?php else: ?>
    <h3 class="text-center">Пока нет сегментов...</h3>
<?php endif; ?>

<div class="row">
    <div class="col-md-12" style="display:flex;justify-content: center;">
        <?= Html::button('Закрыть', [
            'onclick' => 'javascript:history.back()',
            'class' => 'btn button-close-result-mobile'
        ]) ?>
    </div>
</div>