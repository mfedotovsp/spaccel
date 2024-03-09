<?php

use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use yii\helpers\Html;
use app\models\Segments;

/**
 * @var Segments[] $models
 * @var ContractorTasks $task
 */

?>

<!--Данные для списка сегментов-->
<?php foreach ($models as $model) : ?>

    <?php
    /** @var $confirm ConfirmSegment */
    $confirm = ConfirmSegment::find(false)
        ->andWhere(['segment_id' => $model->getId()])
        ->one()
    ?>

    <div class="hypothesis_table_desktop">

        <div class="row container-one_hypothesis row_hypothesis-<?= $model->getId() ?>">

            <div class="col-lg-4 pl-5 pr-5">

                <div class="row display-flex align-items-center">

                    <div class="col-lg-1 pb-3">

                        <?php
                        if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {

                            echo '<div class="pl-5 pr-5">' . Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                        }elseif (!$model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                            echo '<div class="pl-5 pr-5">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                        }elseif ($model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                            echo '<div class="pl-5 pr-5">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                        }elseif ($model->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {

                            echo '<div class="pl-5 pr-5">' . Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                        }
                        ?>

                    </div>

                    <div class="col-lg-11">

                        <div class="hypothesis_title pl-15">
                            <?= $model->getName() ?>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-1 text_description_segment text-center pl-25">
                <?php
                if ($model->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) {
                    echo '<div class="">B2C</div>';
                }
                elseif ($model->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2B) {
                    echo '<div class="">B2B</div>';
                }
                ?>
            </div>

            <div class="col-lg-2 text_description_segment text-center" title="<?= $model->getFieldOfActivity() ?>">
                <?= $model->getFieldOfActivity() ?>
            </div>

            <div class="col-lg-2 text_description_segment text-center" title="<?= $model->getSortOfActivity() ?>">
                <?= $model->getSortOfActivity() ?>
            </div>

            <div class="col-lg-2 text-center">
                <?= number_format($model->getMarketVolume(), 0, '', ' ') ?>
            </div>

            <div class="col-lg-1">
                <div class="row pull-right display-flex align-items-center pr-10">
                    <div>

                        <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px']]),
                            ['/contractor/segments/show-all-information', 'id' => $model->getId()], [
                            'class' => 'openAllInformationSegment', 'title' => 'Смотреть описание сегмента',
                        ]) ?>

                        <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                            <?= Html::a(Html::img('/images/icons/recovery_icon.png', ['style' => ['width' => '24px']]),
                                ['/contractor/segments/recovery', 'id' => $model->getId()], ['title' => 'Восстановить'
                            ]) ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="hypothesis_table_mobile">

        <div class="row container-one_hypothesis_mobile row_hypothesis-<?= $model->getId() ?>">

            <div class="col-xs-12">
                <div class="hypothesis_title_mobile">
                    <?= $model->getName() ?>
                </div>
            </div>

            <div class="col-xs-12 mb-5">
                <span class="header_table_hypothesis_mobile">Статус:</span>
                <span class="text_14_table_hypothesis">
                    <?php if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                        echo 'подтвержден';
                    } elseif (!$confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($model->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                        echo 'не подтвержен';
                    } ?>
                </span>
            </div>

            <div class="col-xs-12 mb-5">
                <span class="header_table_hypothesis_mobile">Тип сегмента:</span>
                <span class="text_14_table_hypothesis">
                    <?php if ($model->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) {
                        echo 'B2C';
                    } elseif ($model->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2B) {
                        echo 'B2B';
                    } ?>
                </span>
            </div>

            <div class="col-xs-12 mb-5">
                <span class="header_table_hypothesis_mobile">Платежеспособность, млн руб. в год:</span>
                <span class="text_14_table_hypothesis">
                    <?= number_format($model->getMarketVolume(), 0, '', ' ') ?>
                </span>
            </div>

            <div class="col-xs-12 mb-5">
                <div class="header_table_hypothesis_mobile">Сфера деятельности:</div>
                <div class="text_14_table_hypothesis">
                    <?= $model->getFieldOfActivity() ?>
                </div>
            </div>

            <div class="col-xs-12 mb-5">
                <div class="header_table_hypothesis_mobile">Вид / специализация деятельности:</div>
                <div class="text_14_table_hypothesis">
                    <?= $model->getSortOfActivity() ?>
                </div>
            </div>

            <div class="hypothesis_buttons_mobile">

                <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>

                    <?= Html::a('Смотреть описание', ['/contractor/segments/show-all-information', 'id' => $model->getId()], [
                        'class' => 'btn btn-default openAllInformationSegment',
                        'style' => [
                            'display' => 'flex',
                            'width' => '47%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 1% 10px 2%',
                        ],
                    ]) ?>

                    <?= Html::a('Восстановить сегмент', ['/contractor/segments/recovery', 'id' => $model->getId()], [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'width' => '47%',
                            'height' => '36px',
                            'background' => '#F5A4A4',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 2% 10px 1%',
                        ],
                    ]) ?>

                <?php else: ?>

                    <?= Html::a('Смотреть описание', ['/segments/show-all-information', 'id' => $model->getId()], [
                        'class' => 'btn btn-default openAllInformationSegment',
                        'style' => [
                            'display' => 'flex',
                            'width' => '96%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 2% 10px 2%',
                        ],
                    ]) ?>

                <?php endif; ?>

            </div>

        </div>
    </div>

<?php endforeach;?>


<div class="mt-15" style="display: flex; justify-content: center;">
    <?= Html::a('Вернуться к списку', ['/contractor/segments/list', 'id' => $task->getId()],[
        'id' => 'show_list',
        'class' => 'btn btn-default',
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'justify-content' => 'center',
            'width' => '220px',
            'height' => '40px',
            'font-size' => '18px',
            'border-radius' => '8px',
            'margin-right' => '10px',
            'background' => '#4F4F4F',
            'color' => '#FFFFFF',
        ]
    ]) ?>
</div>
