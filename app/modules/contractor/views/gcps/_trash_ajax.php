<?php

use app\models\ConfirmGcp;
use app\models\ContractorTasks;
use app\models\Gcps;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use yii\helpers\Html;

/**
 * @var Gcps[] $models
 * @var ContractorTasks $task
 */

?>


<!--Данные для списка ценностных предложений-->
<?php foreach ($models as $model) : ?>

    <?php
    /** @var $confirm ConfirmGcp */
    $confirm = ConfirmGcp::find(false)
        ->andWhere(['gcp_id' => $model->getId()])
        ->one()
    ?>

    <div class="hypothesis_table_desktop">
        <div class="row container-one_hypothesis row_hypothesis-<?= $model->getId() ?>">
            <div class="col-lg-1">
                <div class="row">
                    <div class="col-lg-4" style="padding: 0;">

                        <?php if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {

                            echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                        }elseif (!$confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                            echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                        }elseif ($confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                            echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                        }elseif ($model->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {

                            echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                        }
                        ?>

                    </div>

                    <div class="col-lg-8 hypothesis_title" style="padding: 0 0 0 5px;">

                        <?= $model->getTitle() ?>

                    </div>
                </div>
            </div>

            <div class="col-lg-8 text_description_problem" title="<?= $model->getDescription() ?>">
                <?= $model->getDescription() ?>
            </div>

            <div class="col-lg-1 text-center">
                <?= date("d.m.y", $model->getCreatedAt()) ?>
            </div>

            <div class="col-lg-1 text-center">
                <?php if ($model->getTimeConfirm()) : ?>
                    <?= date("d.m.y", $model->getTimeConfirm()) ?>
                <?php endif; ?>
            </div>

            <div class="col-lg-1">
                <div class="row pull-right display-flex align-items-center pr-10">
                    <div>
                        <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>
                            <?= Html::a(Html::img('/images/icons/recovery_icon.png', ['style' => ['width' => '24px']]),
                                ['/contractor/gcps/recovery', 'id' => $model->getId()], ['title' => 'Восстановить'
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
                    <?= $model->getTitle() ?>
                </div>
            </div>

            <div class="col-xs-12">
                <span class="header_table_hypothesis_mobile">Статус:</span>
                <span class="text_14_table_hypothesis">
                    <?php if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                        echo 'подтверждена';
                    } elseif (!$confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($model->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                        echo 'не подтвержена';
                    } ?>
                </span>
            </div>

            <div class="col-xs-12">
                <span class="header_table_hypothesis_mobile">Описание:</span>
                <span class="text_14_table_hypothesis">
                    <?= $model->getDescription() ?>
                </span>
            </div>

            <div class="col-xs-12">
                <span class="header_table_hypothesis_mobile">Дата создания:</span>
                <span class="text_14_table_hypothesis">
                    <?= date('d.m.Y', $model->getCreatedAt()) ?>
                </span>
            </div>

            <?php if ($model->getTimeConfirm()): ?>
                <div class="col-xs-12 mb-5">
                    <span class="header_table_hypothesis_mobile">Дата подтверждения:</span>
                    <span class="text_14_table_hypothesis">
                        <?= date('d.m.Y', $model->getTimeConfirm()) ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="hypothesis_buttons_mobile">

                <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>

                    <?= Html::a('Восстановить проблему', ['/contractor/gcps/recovery', 'id' => $model->getId()], [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'width' => '96%',
                            'height' => '36px',
                            'background' => '#F5A4A4',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 2% 10px 2%',
                        ],
                    ]) ?>

                <?php else: ?>

                    <div class="pb-10"></div>

                <?php endif; ?>

            </div>

        </div>
    </div>

<?php endforeach; ?>


<div class="mt-15" style="display: flex; justify-content: center;">
    <?= Html::a('Вернуться к списку', ['/contractor/gcps/list', 'id' => $task->getId()],[
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
