<?php

use app\models\ContractorTasks;
use yii\helpers\Html;

/**
 * @var $tasks ContractorTasks[]
 */

?>

<div class="row container-fluid bolder" style="display: flex; align-items: center; border-bottom: 1px solid #cccccc; padding-top: 10px; padding-bottom: 10px; margin-bottom: 10px;">
    <div class="col-md-3">Исполнитель</div>
    <div class="col-md-4">Описание</div>
    <div class="col-md-5">
        <div class="row" style="display: flex; align-items: center;">
            <div class="col-md-5">Деятельность</div>
            <div class="col-md-3">Статус</div>
            <div class="col-md-4">Создано</div>
        </div>
    </div>
</div>

<?php foreach ($tasks as $task): ?>

    <div class="row container-fluid" style="display: flex; align-items: center; border-bottom: 1px solid #cccccc; padding-top: 5px; padding-bottom: 10px;">
        <div class="col-md-3">
            <?= $task->contractor->getUsername() ?>
        </div>
        <div class="col-md-4">
            <?= $task->getDescription() ?>
        </div>
        <div class="col-md-5">
            <div class="row" style="display:flex; align-items: center;">
                <div class="col-md-5"><?= $task->activity->getTitle() ?></div>
                <div class="col-md-3"><?= Html::a($task->getStatusToString(), $task->getLinkToTaskPage()) ?></div>
                <div class="col-md-4"><?= date('d.m.Y', $task->getCreatedAt()) ?></div>
            </div>
        </div>
    </div>

<?php endforeach; ?>


