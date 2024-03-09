<?php

use app\models\ContractorTasks;
use app\models\forms\FormComment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var $tasks ContractorTasks[]
 * @var $formComment FormComment
 */

?>

<div class="row headers_data_notifications">
    <div class="col-md-2">Создано</div>
    <div class="col-md-2">Деятельность</div>
    <div class="col-md-2">Этап проекта</div>
    <div class="col-md-5">Описание</div>
    <div class="col-md-1 text-center">Статус</div>
</div>

<?php foreach ($tasks as $task): ?>

    <div class="row line_data_notifications">
        <div class="col-md-2">
            <?= date('d.m.Y H:i:s', $task->getCreatedAt()) ?>
        </div>
        <div class="col-md-2">
            <?= $task->activity->getTitle() ?>
        </div>
        <div class="col-md-2">
            <?= $task->getStageLink() ?>
        </div>
        <div class="col-md-5">
            <?= $task->getDescription() ?>
        </div>
        <div class="col-md-1 text-center">
            <div><?= $task->getStatusToString() ?></div>
            <div style="display:flex; justify-content: center;">
                <?= Html::button('Подробнее', [
                    'class' => 'openTaskHistory',
                    'id' => 'openTaskHistory-'.$task->getId(),
                    'title' => 'Смотреть историю статусов',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'color' => '#FFFFFF',
                        'background' => '#52BE7F',
                        'width' => '110px',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                    ]
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row line_data_notifications_mobile pt-15 pb-15 pl-15 pr-15">

        <div>
            <span class="bolder">Создано:</span>
            <span><?= date('d.m.Y H:i',$task->getCreatedAt()) ?></span>
        </div>

        <div>
            <span class="bolder">Деятельность:</span>
            <span><?= $task->activity->getTitle() ?></span>
        </div>

        <div>
            <span class="bolder">Этап проекта:</span>
            <span><?= $task->getStageLink() ?></span>
        </div>

        <div>
            <span class="bolder">Статус:</span>
            <span><?= $task->getStatusToString() ?></span>
        </div>

        <div>
            <span class="bolder">Описание:</span>
            <span><?= $task->getDescription() ?></span>
        </div>

        <div class="display-flex justify-content-center mt-15">
            <?= Html::button('Подробнее', [
                'class' => 'openTaskHistory',
                'id' => 'openTaskHistory-'.$task->getId(),
                'title' => 'Смотреть историю статусов',
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

    <div class="row container-fluid pt-15 pb-15 blockTaskHistory blockTaskHistory-<?= $task->getId() ?>" style="display: none; background-color: #E0E0E0; border-top: 1px solid #cccccc;">

        <?php if ($histories = $task->histories): ?>

            <div>
                <div class="text-center">
                    <h4>История изменения статусов</h4>
                </div>
                <div class="row container-fluid bolder pt-10 pb-10" style="display: flex; align-items: center;">
                    <div class="col-xs-4 col-md-2">Дата</div>
                    <div class="col-xs-4 col-md-4">Изменение статуса</div>
                    <div class="col-xs-4 col-md-6">Комментарий</div>
                </div>

                <?php foreach ($histories as $history): ?>

                    <div class="row container-fluid pt-10 pb-10" style="display: flex; align-items: center;">
                        <div class="col-xs-4 col-md-2"><?= date('d.m.Y H:i:s', $history->getCreatedAt()) ?></div>
                        <div class="col-xs-4 col-md-4">
                            <span class="text-danger"><?= ContractorTasks::statusToString($history->getOldStatus()) ?> >>> </span>
                            <span class="text-success"><?= ContractorTasks::statusToString($history->getNewStatus()) ?></span>
                        </div>
                        <div class="col-xs-4 col-md-6"><?= $history->getComment() ?></div>
                    </div>

                <?php endforeach; ?>
            </div>

            <div class="pt-15 blockChangeTaskStatusCustomForm">

                <?php if (in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>

                    <div class="text-center pb-10">
                        <h4>Изменить статус</h4>
                    </div>

                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/tasks/change-status', 'taskId' => $task->getId(), 'newStatus' => '']),
                        'class' => 'changeTaskStatusForm',
                        'id' => 'changeTaskStatusCustomForm',
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                    <div class="row" style="margin-bottom: 15px;">
                        <?= $form->field($formComment, 'comment', [
                            'template' => '<div class="col-md-3"></div><div class="col-md-6">{input}</div><div class="col-md-3"></div>'
                        ])->textarea([
                            'rows' => 1,
                            'required' => true,
                            'maxlength' => true,
                            'class' => 'style_form_field_respond form-control',
                            'placeholder' => 'Напишите комментарий',
                            'autocomplete' => 'off'
                        ])->label(false) ?>
                    </div>

                    <div style="display:flex; justify-content: center;">
                        <?= Html::submitButton('Отозвать задание', [
                            'class' => 'btn btn-lg btn-danger changeStatusSubmit',
                            'id' => 'changeStatus-'.$task->getId().'-'.ContractorTasks::TASK_STATUS_REJECTED,
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#d9534f',
                                'width' => '180px',
                                'height' => '40px',
                                'font-size' => '18px',
                                'border-radius' => '8px',
                            ],
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                <?php elseif ($task->getStatus() === ContractorTasks::TASK_STATUS_COMPLETED): ?>

                    <div class="text-center pb-10">
                        <h4>Изменить статус</h4>
                    </div>

                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/tasks/change-status', 'taskId' => $task->getId(), 'newStatus' => '']),
                        'class' => 'changeTaskStatusForm',
                        'id' => 'changeTaskStatusCustomForm',
                        'options' => ['class' => 'g-py-15'],
                        'errorCssClass' => 'u-has-error-v1',
                        'successCssClass' => 'u-has-success-v1-1',
                    ]); ?>

                        <div class="row" style="margin-bottom: 15px;">
                            <?= $form->field($formComment, 'comment', [
                                'template' => '<div class="col-md-3"></div><div class="col-md-6">{input}</div><div class="col-md-3"></div>'
                            ])->textarea([
                                'rows' => 1,
                                'required' => true,
                                'maxlength' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Напишите комментарий',
                                'autocomplete' => 'off'
                            ])->label(false) ?>
                        </div>

                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-xs-1 col-md-3"></div>
                            <div class="col-xs-6 col-md-3">
                                <div style="display:flex; justify-content: center;">
                                    <?= Html::submitButton('Отправить в доработку', [
                                        'class' => 'btn btn-lg btn-danger changeStatusSubmit',
                                        'id' => 'changeStatus-'.$task->getId().'-'.ContractorTasks::TASK_STATUS_RETURNED,
                                        'style' => [
                                            'display' => 'flex',
                                            'align-items' => 'center',
                                            'justify-content' => 'center',
                                            'background' => '#d9534f',
                                            'width' => '220px',
                                            'height' => '40px',
                                            'font-size' => '18px',
                                            'border-radius' => '8px',
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-xs-4 col-md-3">
                                <div style="display:flex; justify-content: center;">
                                    <?= Html::submitButton('Готово', [
                                        'class' => 'btn btn-lg btn-success changeStatusSubmit',
                                        'id' => 'changeStatus-'.$task->getId().'-'.ContractorTasks::TASK_STATUS_READY,
                                        'style' => [
                                            'display' => 'flex',
                                            'align-items' => 'center',
                                            'justify-content' => 'center',
                                            'background' => '#52BE7F',
                                            'width' => '160px',
                                            'height' => '40px',
                                            'font-size' => '18px',
                                            'border-radius' => '8px',
                                            'margin-left' => '20px',
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-xs-1 col-md-3"></div>
                        </div>

                    <?php ActiveForm::end(); ?>

                <?php endif; ?>
            </div>

        <?php else: ?>

            <div class="text-center">
                <h4>История изменения статусов отстутствует...</h4>
            </div>

        <div class="blockChangeTaskStatusCustomForm">

            <?php $form = ActiveForm::begin([
                'action' => Url::to(['/tasks/change-status', 'taskId' => $task->getId(), 'newStatus' => '']),
                'class' => 'changeTaskStatusForm',
                'id' => 'changeTaskStatusCustomForm',
                'options' => ['class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

            <div class="row" style="margin-bottom: 15px;">
                <?= $form->field($formComment, 'comment', [
                    'template' => '<div class="col-md-3"></div><div class="col-md-6">{input}</div><div class="col-md-3"></div>'
                ])->textarea([
                    'rows' => 1,
                    'required' => true,
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => 'Напишите комментарий',
                    'autocomplete' => 'off'
                ])->label(false) ?>
            </div>

            <div style="display:flex; justify-content: center;">
                <?= Html::submitButton('Отозвать задание', [
                    'class' => 'btn btn-lg btn-danger changeStatusSubmit',
                    'id' => 'changeStatus-'.$task->getId().'-'.ContractorTasks::TASK_STATUS_REJECTED,
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'background' => '#d9534f',
                        'width' => '180px',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                    ],
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

        <?php endif; ?>

    </div>

<?php endforeach; ?>
