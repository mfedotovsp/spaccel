<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Ввод внешних данных';

?>

<div class="col-xs-12" style="margin-top: 35px;">
    <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
        'class' => 'link_to_instruction_page open_modal_instruction_page',
        'title' => 'Инструкция', 'onclick' => 'return false'
    ]) ?>
</div>

<div class="col-xs-12">
    <?= Html::a( 'Списки запросов B2B компаний',
        Url::to(['/admin/wish-list/index']),[
            'class' => 'btn btn-success',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'background' => '#52BE7F',
                'width' => '100%',
                'max-width' => '350px',
                'min-width' => '350px',
                'height' => '40px',
                'font-size' => '24px',
                'border-radius' => '8px',
                'margin-top' => '35px',
            ],
        ]) ?>
</div>
