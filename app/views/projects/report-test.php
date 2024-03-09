<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use app\models\Problems;
use app\models\Gcps;
use app\models\Mvps;

?>

<?php

$this->title = 'Протокол проекта "' . mb_strtolower($project->project_name) . '"';

?>

<div class="table-project-kartik">

    <?

    $gridColumns = [


        [
            'attribute' => 'stages',
            'label' => 'Описание этапа проекта',
            'header' => '<div class="font-header-table" style="font-size: 12px;font-weight: 500;">Описание этапов проекта относительно их взаимосвязи</div>',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'vAlign' => GridView::ALIGN_MIDDLE,
            //'hAlign' => GridView::ALIGN_RIGHT,
            //'options' => ['class' => ''],
            'value' => function ($model) {

                if (($model instanceof Problems) === true) {
                    if ($model->description) {

                        return '<div class="table-kartik-link">' . $model->description . '</div>';
                    }
                }

                if (($model instanceof Gcps) === true) {
                    if ($model->description) {

                        return '<div class="table-kartik-link">' . $model->description . '</div>';
                    }
                }

                if (($model instanceof Mvps) === true) {
                    if ($model->description) {

                        return '<div class="table-kartik-link">' . $model->description . '</div>';
                    }
                }

            },
            'format' => 'html',
            //'hiddenFromExport' => true,
            //'groupedRow' => true, // Группировка по строке
            //'group' => true,  // enable grouping
            //'subGroupOf' => 0 // supplier column index is the parent group
        ],

        [
            'attribute' => 'positive-exist-respond',
            'label' => 'Положит., шт.',
            'header' => '<div class="font-header-table" style="font-size: 12px;font-weight: 500;">Положит.,</div><div class="font-header-table" style="font-size: 12px;font-weight: 500;">шт.</div>',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '90px',
            //'options' => ['colspan' => 1],
            'value' => function ($model) {

                if (($model instanceof Problems) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;
                        if ($responds) {
                            $exist = 0;
                            foreach ($responds as $respond) {

                                if ($respond->interview->status == 1) {
                                    $exist++;
                                }
                            }
                        }

                        return '<div class="table-kartik-link text-center">' . $exist . '</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Gcps) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;
                        if ($responds) {
                            $exist = 0;
                            foreach ($responds as $respond) {

                                if ($respond->interview->status == 1) {
                                    $exist++;
                                }
                            }
                        }

                        return '<div class="table-kartik-link text-center">' . $exist . '</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Mvps) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;
                        if ($responds) {
                            $exist = 0;
                            foreach ($responds as $respond) {

                                if ($respond->interview->status === 1) {
                                    $exist++;
                                }
                                if ($respond->interview->status === 2) {
                                    $exist++;
                                }
                            }
                        }

                        return '<div class="table-kartik-link text-center">' . $exist . '</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }

            },
            'format' => 'html',
        ],


        [
            'attribute' => 'negative-exist-respond',
            'label' => 'Отриц., шт.',
            'header' => '<div class="font-header-table" style="font-size: 12px;font-weight: 500;">Отриц.,</div><div class="font-header-table" style="font-size: 12px;font-weight: 500;">шт.</div>',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '90px',
            //'options' => ['colspan' => 1],
            'value' => function ($model) {

                if (($model instanceof Problems) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;
                        if ($responds) {
                            $exist = 0;
                            foreach ($responds as $respond) {

                                if ($respond->interview->status == '0') {
                                    $exist++;
                                }
                            }
                        }

                        return '<div class="table-kartik-link text-center">' . $exist . '</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Gcps) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;
                        if ($responds) {
                            $exist = 0;
                            foreach ($responds as $respond) {

                                if ($respond->interview->status == '0') {
                                    $exist++;
                                }
                            }
                        }

                        return '<div class="table-kartik-link text-center">' . $exist . '</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Mvps) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;
                        if ($responds) {
                            $exist = 0;
                            foreach ($responds as $respond) {

                                if ($respond->interview->status === 0) {
                                    $exist++;
                                }
                            }
                        }

                        return '<div class="table-kartik-link text-center">' . $exist . '</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }

            },
            'format' => 'html',
        ],



        [
            'attribute' => 'share-positive',
            'label' => 'Результат, %',
            'header' => '<div class="font-header-table" style="font-size: 12px;font-weight: 500;">Результат,</div><div class="font-header-table" style="font-size: 12px;font-weight: 500;">%</div>',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '90px',
            //'options' => ['colspan' => 1],
            'value' => function ($model) {

                if (($model instanceof Problems) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;

                        $sumPositive = 0;
                        foreach ($responds as $respond){
                            if ($respond->interview->status == 1){
                                $sumPositive++;
                            }
                        }

                        if($sumPositive !== 0){

                            $valPositive = round(($sumPositive / count($responds) * 100) *100) / 100;

                        }else {

                            $valPositive = 0;
                        }

                        return '<div class="table-kartik-link text-center">' . $valPositive . '%</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Gcps) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;

                        $sumPositive = 0;
                        foreach ($responds as $respond){
                            if ($respond->interview->status == 1){
                                $sumPositive++;
                            }
                        }

                        if($sumPositive !== 0){

                            $valPositive = round(($sumPositive / count($responds) * 100) *100) / 100;

                        }else {

                            $valPositive = 0;
                        }

                        return '<div class="table-kartik-link text-center">' . $valPositive . '%</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Mvps) === true) {

                    if (!empty($model->confirm)) {

                        $responds = $model->confirm->responds;

                        $sumPositive = 0;
                        foreach ($responds as $respond){
                            if ($respond->interview->status > 0){
                                $sumPositive++;
                            }
                        }

                        if($sumPositive !== 0){

                            $valPositive = round(($sumPositive / count($responds) * 100) *100) / 100;

                        }else {

                            $valPositive = 0;
                        }

                        return '<div class="table-kartik-link text-center">' . $valPositive . '%</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }

            },
            'format' => 'html',
        ],


        [
            'attribute' => 'minimum-positive',
            'label' => 'Порог полож., %',
            'header' => '<div class="font-header-table" style="font-size: 12px;font-weight: 500;">Порог</div><div class="font-header-table" style="font-size: 12px;font-weight: 500;">полож.,%</div>',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '90px',
            //'options' => ['colspan' => 1],
            'value' => function ($model) {

                if (($model instanceof Problems) === true) {

                    if (!empty($model->confirm)) {

                        $minPositive = round(($model->confirm->count_positive / $model->confirm->count_respond * 100) *100) / 100;

                        return '<div class="table-kartik-link text-center">' . $minPositive . '%</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Gcps) === true) {

                    if (!empty($model->confirm)) {

                        $minPositive = round(($model->confirm->count_positive / $model->confirm->count_respond * 100) *100) / 100;

                        return '<div class="table-kartik-link text-center">' . $minPositive . '%</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Mvps) === true) {

                    if (!empty($model->confirm)) {

                        $minPositive = round(($model->confirm->count_positive / $model->confirm->count_respond * 100) *100) / 100;

                        return '<div class="table-kartik-link text-center">' . $minPositive . '%</div>';

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }

            },
            'format' => 'html',
        ],


        [
            'attribute' => 'result',
            'label' => 'Вывод',
            'header' => '<div class="font-header-table" style="font-size: 12px;font-weight: 500;">Вывод</div>',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '90px',
            //'options' => ['colspan' => 1],
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {

                if (($model instanceof Problems) === true) {

                    if (!empty($model->confirm)) {

                        if ($model->exist_confirm !== null) {

                            if ($model->exist_confirm == 1) {
                                return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                            }else {
                                return Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]);
                            }
                        }else {
                            return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                        }

                    }else {
                        return '<div class="text-center"> - </div>';
                    }
                }


                if (($model instanceof Gcps) === true) {

                    if (!empty($model->confirm)) {

                        if ($model->exist_confirm !== null) {

                            if ($model->exist_confirm == 1) {
                                return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                            }else {
                                return Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]);
                            }
                        }else {
                            return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                        }

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }


                if (($model instanceof Mvps) === true) {

                    if (!empty($model->confirm)) {

                        if ($model->exist_confirm !== null) {

                            if ($model->exist_confirm == 1) {
                                return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                            }else {
                                return Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]);
                            }
                        }else {
                            return Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]);
                        }

                    }else {
                        return '<div class="table-kartik-link text-center"> - </div>';
                    }
                }

            },
            'format' => 'html',
        ],


        [
            'attribute' => 'business',
            'label' => 'Бизнес-модель',
            'header' => '<div class="font-header-table" style="font-size: 12px;font-weight: 500;">Бизнес-</div><div class="font-header-table" style="font-size: 12px;font-weight: 500;">модель</div>',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '90px',
            //'options' => ['colspan' => 1],
            'hAlign' => GridView::ALIGN_CENTER,
            'value' => function ($model) {

                if (($model instanceof Mvps) === true) {

                    if ($model->businessModel) {
                        return Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]);
                    }
                }

            },
            'format' => 'html',
        ],


        [
            'attribute' => 'segment-line',
            'label' => 'Сегмент',
            'header' => '<div class="font-header-table" style="font-size: 12px; font-weight: 500;">Сегмент</div>',
            'groupOddCssClass' => 'kv-grouped-row',
            'groupEvenCssClass' => 'kv-grouped-row',
            //'options' => ['colspan' => 1],
            'value' => function ($model) {

                if (($model instanceof Problems) === true) {

                    if (empty($model->id)) {

                        foreach ($model->project->segments as $s => $segment) {
                            if ($model->segment_id == $segment->id){
                                return '<div class="table-kartik-link">Сегмент ' . ($s+1) . ': ' . $model->segment->name . '</div>';
                            }
                        }
                    }else {

                        $arrS = explode('.', $model->description);
                        $arrNumberSegment = explode('ГПС ',$arrS[0]);
                        $numberSegment = $arrNumberSegment[1];

                        return '<div class="table-kartik-link">Сегмент ' . $numberSegment . ': ' . $model->segment->name . '</div>';
                    }
                }

                if (($model instanceof Gcps) === true) {

                    $arrS = explode('.', $model->description);
                    $arrNumberSegment = explode('ГЦП ',$arrS[0]);
                    $numberSegment = $arrNumberSegment[1];

                    return '<div class="table-kartik-link">Сегмент ' . $numberSegment . ': ' . $model->segment->name . '</div>';
                }

                if (($model instanceof Mvps) === true) {

                    $arrS = explode('.', $model->description);
                    $arrNumberSegment = explode('ГMVP ',$arrS[0]);
                    $numberSegment = $arrNumberSegment[1];

                    return '<div class="table-kartik-link">Сегмент ' . $numberSegment . ': ' . $model->segment->name . '</div>';
                }


            },
            'format' => 'html',
            //'hiddenFromExport' => true, // Убрать столбец при скачивании
            'group' => true,  // enable grouping
            'groupedRow' => true, // Группировка по строке
            /*'groupFooter' => function ($model, $key, $index, $widget) {
                return [
                    //'mergeColumns' => [[1,3]], // columns to merge in summary
                    'content' => [             // content to show in each summary cell
                        1 => '<div class="table-kartik-link" style="padding: 0 5px;">Summary' . $model->segment->name . '</div>',
                        //4 => 'привет',
                    ],
                    'options' => ['class' => 'info table-info','style' => 'font-weight:bold;']
                ];
            }*/
        ],




    ];




    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'pjax' => true,
        'id' => 'reportTable',
        'striped' => false,
        'bordered' => true,
        'condensed' => true,
        'summary' => false,
        'hover' => true,

        'panel' => [
            'type' => 'default',
            'heading' => false,
            //'headingOptions' => ['class' => 'style-head-table-kartik-top'],
            'before' => '<div style="font-size: 30px; font-weight: 700; color: #F2F2F2;">Проект: «'. $project->project_name . '» ' . Html::a('Посмотреть описание', ['/projects/view', 'id' => $project->id], ['style' => ['font-size' => '12px', 'color' => '#F2F2F2', 'font-weight' => '300']]) .'</div>',
            'beforeOptions' => ['class' => 'style-head-table-kartik-top']
        ],

        'toolbar' => [
            '{export}',
        ],

        'exportContainer' => ['class' => 'btn-group-sm', 'style' => ['padding' => '5px 5px']],
        //'toggleDataContainer' => ['class' => 'btn-group mr-2'],

        'export'=>[
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK,
            'label' => '<span class="font-header-table" style="font-weight: 700;">Экпорт таблицы</span>',
            'options' => ['title' => false],
        ],

        'columns' => $gridColumns,

        'exportConfig' => [
            GridView::PDF => [

                'filename' => 'Протокол_проекта_«'. $project_filename . '»',

                'config' => [

                    //'marginRight' => 10,
                    //'marginLeft' => 10,
                    //'cssInline' => '.positive-business-model-export{margin-right: 20px;}' .
                    //'.presentation-business-model-export{margin-left: 20px;}',

                    'methods' => [
                        'SetHeader' => ['<div style="color: #3c3c3c;">Протокол проекта «'.$project->project_name.'»</div>||<div style="color: #3c3c3c;">Сгенерировано: ' . date("H:i d.m.Y") . '</div>'],
                        'SetFooter' => ['<div style="color: #3c3c3c;">Страница {PAGENO}</div>'],
                    ],

                    'options' => [
                        //'title' => 'Сводная таблица проекта «'.$project->project_name.'»',
                        //'subject' => Yii::t('kvgrid', 'PDF export generated by kartik-v/yii2-grid extension'),
                        //'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf')
                    ],

                    //'contentBefore' => '',
                    //'contentAfter' => '',
                ],
            ],
            /*GridView::EXCEL => [

            ],
            GridView::HTML => [

            ],*/
        ],

        //'floatHeader'=>true,
        //'floatHeaderOptions'=>['top'=>'50'],
        'headerRowOptions' => ['class' => 'style-head-table-kartik-bottom'],

        'beforeHeader' => [
            [
                'columns' => [
                    ['content' =>  'Наименование этапа', 'options' => ['colspan' => 1, 'class' => 'font-header-table', 'style' => ['padding' => '10px 0']]],
                    ['content' => 'Результаты теста', 'options' => ['colspan' => 5, 'class' => 'font-header-table', 'style' => ['padding' => '10px 0']]],
                    ['content' => '', 'options' => ['colspan' => 1, 'class' => 'font-header-table']],
                ],

                'options' => [
                    'class' => 'style-header-table-kartik',
                ]
            ]
        ],
    ]);

    ?>

</div>

