<?php

use app\models\BusinessModel;
use app\models\Projects;
use app\models\StatusConfirmHypothesis;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * @var ArrayDataProvider $dataProvider
 * @var Projects $project
 * @var string $project_filename
 */

?>

<div class="project-result-export">

    <?php

    $gridColumns = [

        [
            'attribute' => 'segments',
            'label' => 'Сегмент',
            'header' => 'Наименование',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '180px',
            'value' => static function (BusinessModel $model) {

                return $model->segment->name;
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
            'group' => true,  // enable grouping
        ],

        [
            'attribute' => 'status_segment',
            'label' => 'Статус',
            'header' => 'Статус',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '60px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (($model->segment->exist_confirm === StatusConfirmHypothesis::COMPLETED) && ($model->segment->time_confirm !== null)) {
                    return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->segment->exist_confirm === StatusConfirmHypothesis::NOT_COMPLETED) {
                    return Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->segment && $model->segment->exist_confirm === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
            'group' => true,  // enable grouping
            'subGroupOf' => 0 // supplier column index is the parent group
        ],

        [
            'attribute' => 'date_segment',
            'label' => 'Дата генер.',
            'header' => 'Дата генер.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if ($model->segment->created_at) {
                    return date('d.m.y', $model->segment->created_at);
                }

                return '__.__.__';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
            'group' => true,  // enable grouping
            'subGroupOf' => 0 // supplier column index is the parent group
        ],

        [
            'attribute' => 'date_confirm_segment',
            'label' => 'Дата подтв.',
            'header' => 'Дата подтв.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (($model->segment->time_confirm !== null)) {
                    return date('d.m.y', $model->segment->time_confirm);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
            'group' => true,  // enable grouping
            'subGroupOf' => 0 // supplier column index is the parent group
        ],

        [
            'attribute' => 'gps',
            'label' => 'Обознач.',
            'header' => 'Обознач.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (empty($model->problem) && $model->segment->exist_confirm === StatusConfirmHypothesis::COMPLETED) {
                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                if ($model->problem->title) {
                    return $model->problem->title;
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'status_gps',
            'label' => 'Статус',
            'header' => 'Статус',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '60px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (($model->problem->exist_confirm === StatusConfirmHypothesis::COMPLETED) && ($model->problem->time_confirm !== null)) {
                    return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->problem->exist_confirm === StatusConfirmHypothesis::NOT_COMPLETED) {
                    return Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->problem && $model->problem->exist_confirm === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'date_gps',
            'label' => 'Дата генер.',
            'header' => 'Дата генер.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if ($model->problem->created_at) {
                    return date('d.m.y', $model->problem->created_at);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'date_confirm_gps',
            'label' => 'Дата подтв.',
            'header' => 'Дата подтв.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (($model->problem->time_confirm !== null)) {
                    return date('d.m.y', $model->problem->time_confirm);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'gcp',
            'label' => 'Обознач.',
            'header' => 'Обознач.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (empty($model->problem->gcps) && $model->problem->exist_confirm === StatusConfirmHypothesis::COMPLETED) {
                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                if ($model->gcp->title) {
                    return $model->gcp->title;
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'status_gcp',
            'label' => 'Статус',
            'header' => 'Статус',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '60px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (($model->gcp->exist_confirm === StatusConfirmHypothesis::COMPLETED) && ($model->gcp->time_confirm !== null)) {
                    return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->gcp->exist_confirm === StatusConfirmHypothesis::NOT_COMPLETED) {
                    return Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->gcp && $model->gcp->exist_confirm === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'date_gcp',
            'label' => 'Дата генер.',
            'header' => 'Дата генер.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if ($model->gcp->created_at) {
                    return date('d.m.y', $model->gcp->created_at);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'date_confirm_gcp',
            'label' => 'Дата подтв.',
            'header' => 'Дата подтв.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (($model->gcp->time_confirm !== null)) {
                    return date('d.m.y', $model->gcp->time_confirm);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'mvp',
            'label' => 'Обознач.',
            'header' => 'Обознач.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if (empty($model->gcp->mvps) && $model->gcp->exist_confirm === StatusConfirmHypothesis::COMPLETED) {
                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                if ($model->mvp->title) {
                    return $model->mvp->title;
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'status_mvp',
            'label' => 'Статус',
            'header' => 'Статус',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '60px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if ($model->mvp->exist_confirm === StatusConfirmHypothesis::COMPLETED) {
                    return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->mvp->exist_confirm === StatusConfirmHypothesis::NOT_COMPLETED) {
                    return Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]);
                }

                if ($model->mvp && $model->mvp->exist_confirm === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'date_mvp',
            'label' => 'Дата генер.',
            'header' => 'Дата генер.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if ($model->mvp->created_at) {
                    return date('d.m.y', $model->mvp->created_at);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'date_confirm_mvp',
            'label' => 'Дата подтв.',
            'header' => 'Дата подтв.',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '90px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if ($model->mvp->time_confirm !== null) {
                    return date('d.m.y', $model->mvp->time_confirm);
                }

                return '';
            },
            'format' => 'html',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],

        [
            'attribute' => 'businessModel',
            'label' => 'Статус',
            'header' => 'Статус',
            'headerOptions' => ['style' => ['text-align' => 'center', 'font-size' => '12px', 'font-weight' => 'normal']],
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'width' => '130px',
            'contentOptions' => ['style' => ['text-align' => 'center']],
            'value' => static function (BusinessModel $model) {

                if ($model->mvp->exist_confirm === StatusConfirmHypothesis::COMPLETED){
                    if ($model->id) {
                        return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                    }

                    return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                }

                return '';
            },
            'format' => 'raw',
            'hidden' => true, //Скрыть столбец со станицы, при этом при скачивании он будет виден
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
        ],
    ];

    /*Widget Kartik GridView*/

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'striped' => false,
        'bordered' => false,
        'condensed' => true,
        'summary' => false,
        'hover' => true,
        'panel' => ['type' => 'default', 'heading' => false, 'footer' => false, 'after' => false],
        'toolbar' => ['{export}'],
        'exportContainer' => ['class' => 'btn-group-sm', 'style' => ['padding' => '5px 5px']],
        'export' => ['showConfirmAlert' => false, 'target' => GridView::TARGET_SELF, 'label' => 'Экпорт таблицы', 'options' => ['class' => 'button_result_export', 'title' => false]],
        'columns' => $gridColumns,
        'exportConfig' => [

            GridView::PDF => [
                'filename' => 'Сводная_таблица_проекта_«'. $project_filename . '»',
                'config' => [
                    'marginRight' => 10,
                    'marginLeft' => 10,
                    'methods' => [
                        'SetHeader' => ['<div style="color: #3c3c3c;">Сводная таблица проекта «'.$project->getProjectName().'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
                        'SetFooter' => ['<div style="color: #3c3c3c;">Страница {PAGENO}</div>'],
                    ],
                ],
            ],
        ],

        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => 'Сегмент', 'options' => ['colspan' => 4, 'style' => ['padding-top' => '10px', 'padding-bottom' => '10px', 'text-align' => 'center']]],
                    ['content' => 'Проблема сегмента', 'options' => ['colspan' => 4, 'style' => ['padding-top' => '10px', 'padding-bottom' => '10px', 'text-align' => 'center']]],
                    ['content' => 'Ценностное предложение', 'options' => ['colspan' => 4, 'style' => ['padding-top' => '10px', 'padding-bottom' => '10px', 'text-align' => 'center']]],
                    ['content' => 'Гипотеза MVP (продукт)', 'options' => ['colspan' => 4, 'style' => ['padding-top' => '10px', 'padding-bottom' => '10px', 'text-align' => 'center']]],
                    ['content' => 'Бизнес-модель', 'options' => ['colspan' => 1, 'style' => ['padding-top' => '10px', 'padding-bottom' => '10px', 'text-align' => 'center']]],
                ],
            ]
        ],
    ]);

    ?>

</div>

<div class="notification_result_export">
    <h3>Подождите...отдаем файл на скачивание...</h3>
</div>

    <!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/project_result_export.js'); ?>