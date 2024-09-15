<?php

use app\models\BusinessModel;
use app\models\Projects;
use app\models\StatusConfirmHypothesis;
use kartik\export\ExportMenu;
use PhpOffice\PhpSpreadsheet\Style\Border;
use yii\data\ArrayDataProvider;

/**
 * @var ArrayDataProvider $dataProvider
 * @var Projects $project
 * @var string $dataStringProject
 * @var array $cellsMerged
 * @var string $project_filename
 */

$this->title = 'Экспорт в Excel';

// Материалы:
// https://yiiframework.ru/forum/viewtopic.php?t=45441
// https://docs.krajee.com/kartik-export-exportmenu#$groupedRowStyle-detail
// https://topic.alibabacloud.com/a/detailed-description-of-how-to-export-an-excel-table-in-the-yii2-framework-yii2excel_1_34_32678228.html

?>

<div class="project-result-export-to-excel">

    <?php

    $gridColumns = [

        [
            'attribute' => 'project',
            'label' => 'Проект',
            'header' => 'Описание проекта',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => '',
        ],

        [
            'attribute' => 'segments',
            'label' => 'Сегменты',
            'header' => 'Сегменты',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                return $model->segment->getName();
            },
        ],

        [
            'attribute' => 'segment_result',
            'label' => 'Результат',
            'header' => 'Результат',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->segment->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                    return 'Сегмент подтвержден';
                }
                if ($model->segment->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                    return 'Сегмент не подтвержден';
                }

                return '';
            },
        ],

        [
            'attribute' => 'segment_responds',
            'label' => 'Выборка',
            'header' => 'Выборка',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->segment->confirm && $model->segment->confirm->isExistDesc()) {
                    return 1;
                }
                if (in_array($model->segment->getExistConfirm(), [
                    StatusConfirmHypothesis::COMPLETED,
                    StatusConfirmHypothesis::NOT_COMPLETED
                ], true)) {
                    return count($model->segment->confirm->responds);
                }

                return '';
            },
        ],

        [
            'attribute' => 'segment_responds_confirm',
            'label' => 'Положительно',
            'header' => 'Положительно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->segment->confirm && $model->segment->confirm->isExistDesc()) {
                    return 1;
                }
                if (in_array($model->segment->getExistConfirm(), [
                    StatusConfirmHypothesis::COMPLETED,
                    StatusConfirmHypothesis::NOT_COMPLETED
                ], true)) {
                    return $model->segment->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],

        [
            'attribute' => 'segment_responds_not_confirm',
            'label' => 'Отрицательно',
            'header' => 'Отрицательно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if (in_array($model->segment->getExistConfirm(), [
                    StatusConfirmHypothesis::COMPLETED,
                    StatusConfirmHypothesis::NOT_COMPLETED
                ], true)) {
                    return count($model->segment->confirm->responds) - $model->segment->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],

        [
            'attribute' => 'problems',
            'label' => 'Проблемы',
            'header' => 'Проблемы',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->problem->description) {
                    return $model->problem->title . ': ' . $model->problem->description;
                }
                return '';
            },
        ],

        [
            'attribute' => 'problem_result',
            'label' => 'Результат',
            'header' => 'Результат',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->problem->description && $model->problem->confirm) {
                    if ($model->problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                        return 'Проблема подтверждена';
                    }
                    if ($model->problem->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                        return 'Проблема не подтверждена';
                    }
                }
                return '';
            },
        ],

        [
            'attribute' => 'problem_responds',
            'label' => 'Выборка',
            'header' => 'Выборка',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->problem->confirm && $model->problem->confirm->isExistDesc()) {
                    return 1;
                }
                if ($model->problem->description && $model->problem->confirm && in_array($model->problem->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return count($model->problem->confirm->responds);
                }

                return '';
            },
        ],

        [
            'attribute' => 'problem_responds_confirm',
            'label' => 'Положительно',
            'header' => 'Положительно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->problem->confirm && $model->problem->confirm->isExistDesc()) {
                    return 1;
                }
                if ($model->problem->description && $model->problem->confirm && in_array($model->problem->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return $model->problem->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],

        [
            'attribute' => 'problem_responds_not_confirm',
            'label' => 'Отрицательно',
            'header' => 'Отрицательно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->problem->description && $model->problem->confirm && in_array($model->problem->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return count($model->problem->confirm->responds) - $model->problem->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],

        [
            'attribute' => 'gcps',
            'label' => 'Ценностные предложения',
            'header' => 'Ценностные предложения',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->gcp->description) {
                    return $model->gcp->title . ': ' . $model->gcp->description;
                }
                return '';
            },
        ],

        [
            'attribute' => 'gcp_result',
            'label' => 'Результат',
            'header' => 'Результат',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->gcp->description && $model->gcp->confirm) {
                    if ($model->gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                        return 'ЦП подтверждено';
                    }
                    if ($model->problem->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                        return 'ЦП не подтверждено';
                    }
                }
                return '';
            },
        ],

        [
            'attribute' => 'gcp_responds',
            'label' => 'Выборка',
            'header' => 'Выборка',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->gcp->confirm && $model->gcp->confirm->isExistDesc()) {
                    return 1;
                }
                if ($model->gcp->description && $model->gcp->confirm && in_array($model->gcp->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return count($model->gcp->confirm->responds);
                }

                return '';
            },
        ],

        [
            'attribute' => 'gcp_responds_confirm',
            'label' => 'Положительно',
            'header' => 'Положительно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->gcp->confirm && $model->gcp->confirm->isExistDesc()) {
                    return 1;
                }
                if ($model->gcp->description && $model->gcp->confirm && in_array($model->gcp->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return $model->gcp->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],

        [
            'attribute' => 'gcp_responds_not_confirm',
            'label' => 'Отрицательно',
            'header' => 'Отрицательно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->gcp->description && $model->gcp->confirm && in_array($model->gcp->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return count($model->gcp->confirm->responds) - $model->gcp->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],

        [
            'attribute' => 'mvps',
            'label' => 'MVP-продукты',
            'header' => 'MVP-продукты',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->mvp->description) {
                    return $model->mvp->title . ': ' . $model->mvp->description;
                }
                return '';
            },
        ],

        [
            'attribute' => 'mvp_result',
            'label' => 'Результат',
            'header' => 'Результат',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->mvp->description && $model->mvp->confirm) {
                    if ($model->mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                        return 'MVP подтверждено';
                    }
                    if ($model->mvp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                        return 'MVP не подтверждено';
                    }
                }
                return '';
            },
        ],

        [
            'attribute' => 'mvp_responds',
            'label' => 'Выборка',
            'header' => 'Выборка',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->mvp->confirm && $model->mvp->confirm->isExistDesc()) {
                    return 1;
                }
                if ($model->mvp->description && $model->mvp->confirm && in_array($model->mvp->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return count($model->mvp->confirm->responds);
                }

                return '';
            },
        ],

        [
            'attribute' => 'mvp_responds_confirm',
            'label' => 'Положительно',
            'header' => 'Положительно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->mvp->confirm && $model->mvp->confirm->isExistDesc()) {
                    return 1;
                }
                if ($model->mvp->description && $model->mvp->confirm && in_array($model->mvp->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return $model->mvp->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],

        [
            'attribute' => 'mvp_responds_not_confirm',
            'label' => 'Отрицательно',
            'header' => 'Отрицательно',
            'groupOddCssClass' => 'kv',
            'groupEvenCssClass' => 'kv',
            'value' => static function (BusinessModel $model) {
                if ($model->mvp->description && $model->mvp->confirm && in_array($model->mvp->getExistConfirm(), [
                        StatusConfirmHypothesis::COMPLETED,
                        StatusConfirmHypothesis::NOT_COMPLETED
                    ], true)) {
                    return count($model->mvp->confirm->responds) - $model->mvp->confirm->getCountConfirmMembers();
                }

                return '';
            },
        ],
    ];

    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'autoXlFormat'=> false,
        'columns' => $gridColumns,
        'showConfirmAlert' => false,
        'autoWidth' => false,
        'onRenderSheet' => static function($sheet, $widget) use ($dataStringProject, $cellsMerged) {
            $sheet->getColumnDimension('A')->setWidth(50);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(12);
            $sheet->getColumnDimension('E')->setWidth(17);
            $sheet->getColumnDimension('F')->setWidth(17);
            $sheet->getColumnDimension('G')->setWidth(40);
            $sheet->getColumnDimension('H')->setWidth(25);
            $sheet->getColumnDimension('I')->setWidth(12);
            $sheet->getColumnDimension('J')->setWidth(17);
            $sheet->getColumnDimension('K')->setWidth(17);
            $sheet->getColumnDimension('L')->setWidth(40);
            $sheet->getColumnDimension('M')->setWidth(25);
            $sheet->getColumnDimension('N')->setWidth(12);
            $sheet->getColumnDimension('O')->setWidth(17);
            $sheet->getColumnDimension('P')->setWidth(17);
            $sheet->getColumnDimension('Q')->setWidth(40);
            $sheet->getColumnDimension('R')->setWidth(25);
            $sheet->getColumnDimension('S')->setWidth(12);
            $sheet->getColumnDimension('T')->setWidth(17);
            $sheet->getColumnDimension('U')->setWidth(17);

            if ($cellsMerged['project']) {
                $sheet->mergeCells('A' . $cellsMerged['project'][0] . ':A' . $cellsMerged['project'][1]);
            }
            $sheet->setCellValue("A2", $dataStringProject);

            if ($cellsMerged['segments']) {
                foreach ($cellsMerged['segments'] as $cellsMergedSegment) {
                    $sheet->mergeCells('B' . $cellsMergedSegment[0] . ':B' . $cellsMergedSegment[1]);
                    $sheet->mergeCells('C' . $cellsMergedSegment[0] . ':C' . $cellsMergedSegment[1]);
                    $sheet->mergeCells('D' . $cellsMergedSegment[0] . ':D' . $cellsMergedSegment[1]);
                    $sheet->mergeCells('E' . $cellsMergedSegment[0] . ':E' . $cellsMergedSegment[1]);
                    $sheet->mergeCells('F' . $cellsMergedSegment[0] . ':F' . $cellsMergedSegment[1]);
                }
            }

            if ($cellsMerged['problems']) {
                foreach ($cellsMerged['problems'] as $cellsMergedProblem) {
                    $sheet->mergeCells('G' . $cellsMergedProblem[0] . ':G' . $cellsMergedProblem[1]);
                    $sheet->mergeCells('H' . $cellsMergedProblem[0] . ':H' . $cellsMergedProblem[1]);
                    $sheet->mergeCells('I' . $cellsMergedProblem[0] . ':I' . $cellsMergedProblem[1]);
                    $sheet->mergeCells('J' . $cellsMergedProblem[0] . ':J' . $cellsMergedProblem[1]);
                    $sheet->mergeCells('K' . $cellsMergedProblem[0] . ':K' . $cellsMergedProblem[1]);
                }
            }

            if ($cellsMerged['gcps']) {
                foreach ($cellsMerged['gcps'] as $cellsMergedGcp) {
                    $sheet->mergeCells('L' . $cellsMergedGcp[0] . ':L' . $cellsMergedGcp[1]);
                    $sheet->mergeCells('M' . $cellsMergedGcp[0] . ':M' . $cellsMergedGcp[1]);
                    $sheet->mergeCells('N' . $cellsMergedGcp[0] . ':N' . $cellsMergedGcp[1]);
                    $sheet->mergeCells('O' . $cellsMergedGcp[0] . ':O' . $cellsMergedGcp[1]);
                    $sheet->mergeCells('P' . $cellsMergedGcp[0] . ':P' . $cellsMergedGcp[1]);
                }
            }

            $sheet->setTitle('Итоговая таблица проекта');
        },
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_EXCEL => false,
        ],
        'boxStyleOptions' => [
            ExportMenu::FORMAT_EXCEL_X => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_NONE,
                    ],
                    'inside' => [
                    'borderStyle' => Border::BORDER_NONE,
                    ],
                ],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ]
            ],
        ],
        'exportContainer' => [
            'class' => 'btn-group mr-2'
        ],
        'dropdownOptions' => [
            'label' => 'Export',
            'class' => 'btn btn-secondary',
        ],
        'filename' => $project_filename,
    ]);
?>

</div>

<div class="notification_result_export">
    <h3>Подождите...отдаем файл на скачивание...</h3>
</div>

    <!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/project_result_export_excel.js'); ?>
