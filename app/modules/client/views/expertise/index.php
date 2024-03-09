<?php

use yii\helpers\Html;

$this->title = 'Разделы экпертизы';

?>

<div class="col-xs-12" style="margin-top: 35px;">
    <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
        'class' => 'link_to_instruction_page open_modal_instruction_page',
        'title' => 'Инструкция', 'onclick' => 'return false'
    ]) ?>
</div>

<div class="col-xs-12">
    <ul class="menu-expertise">
        <li><?= Html::a( 'Назначения экспертов на проекты', ['/client/expertise/tasks']) ?></li>
        <li><?= Html::a( 'Сводная таблица назначений экспертов на проекты', ['/client/expertise/result-tasks']) ?></li>
    </ul>
</div>

<style>
    .menu-expertise {
        padding: 15px;
    }
    .menu-expertise a {
        font-size: 18px;
        color: #4F4F4F;
        transition: all .3s ease;
    }
    .menu-expertise a:hover {
        color: #333333;
    }
    .menu-expertise a:focus {
        color: #333333;
    }
</style>
