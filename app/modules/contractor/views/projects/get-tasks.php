<?php

use app\models\ContractorTasks;
use yii\helpers\Html;

/**
 * @var $tasks ContractorTasks[]
 */

?>

<div class="row container-fluid bolder headers_data_communications">
    <div class="col-md-6">
        <div class="row" style="display:flex; align-items: center;">
            <div class="col-md-2">Создано</div>
            <div class="col-md-2">Статус</div>
            <div class="col-md-4">Деятельность</div>
            <div class="col-md-4">Этап проекта</div>
        </div>
    </div>
    <div class="col-md-5">Описание</div>
    <div class="col-md-1"></div>
</div>

<?php foreach ($tasks as $task): ?>

    <div class="row line_data_communications">
        <div class="col-md-6">
            <div class="row" style="display: flex; align-items: center;">
                <div class="col-md-2">
                    <?= date('d.m.Y H:i:s', $task->getCreatedAt()) ?>
                </div>
                <div class="col-md-2">
                    <?= $task->getStatusToString() ?>
                </div>
                <div class="col-md-4">
                    <?= $task->activity->getTitle() ?>
                </div>
                <div class="col-md-4">
                    <?= $task->getNameStage() ?>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <?= $task->getDescription() ?>
        </div>
        <div class="col-md-1">
            <?= Html::a('>>>', $task->getStageUrl(), [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'width' => '70px',
                    'height' => '40px',
                    'font-size' => '18px',
                    'border-radius' => '8px',
                    'margin-right' => '10px',
                    'background' => '#E0E0E0',
                    'font-weight' => '700',
                    'color' => '#4F4F4F',
                ]
            ]) ?>
        </div>
    </div>

    <div class="line_data_communications_mobile pt-15 pb-15">

        <div>
            <span class="bolder">Создано:</span>
            <span><?= date('d.m.Y H:i',$task->getCreatedAt()) ?></span>
        </div>

        <div>
            <span class="bolder">Статус:</span>
            <span><?= $task->getStatusToString() ?></span>
        </div>

        <div>
            <span class="bolder">Деятельность:</span>
            <span><?= $task->activity->getTitle() ?></span>
        </div>

        <div>
            <span class="bolder">Этап проекта:</span>
            <span><?= $task->getNameStage() ?></span>
        </div>

        <div>
            <span class="bolder">Описание:</span>
            <span><?= $task->getDescription() ?></span>
        </div>

        <div class="display-flex justify-content-center mt-15">
            <?= Html::a('Перейти к заданию', $task->getStageUrl(), [
                'class' => 'btn btn-default',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'width' => '300px',
                    'height' => '30px',
                    'font-size' => '16px',
                    'border-radius' => '8px',
                    'margin-right' => '10px',
                    'background' => '#E0E0E0',
                    'color' => '#4F4F4F',
                ]
            ]) ?>
        </div>
    </div>

<?php endforeach; ?>


