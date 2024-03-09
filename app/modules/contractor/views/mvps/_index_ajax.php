<?php

use app\models\ContractorTasks;
use app\models\Mvps;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use app\modules\contractor\models\form\FormTaskComplete;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var Mvps[] $models
 * @var ContractorTasks $task
 * @var FormTaskComplete $formTaskComplete
 */

?>


<!--Данные для списка MVP -->
<?php if (count($models) > 0): ?>

    <?php foreach ($models as $model) : ?>

        <div class="hypothesis_table_desktop">
            <div class="row container-one_hypothesis row_hypothesis-<?= $model->getId() ?>">

                <div class="col-lg-1">
                    <div class="row">

                        <div class="col-lg-4" style="padding: 0;">

                            <?php
                            if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {

                                echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                            }elseif (!$model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                                echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                            }elseif ($model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

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

                                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),['/contractor/mvps/get-hypothesis-to-update', 'id' => $model->getId()], [
                                    'class' => 'update-hypothesis',
                                    'title' => 'Редактировать',
                                ]) ?>

                                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),['/contractor/mvps/delete', 'id' => $model->getId()], [
                                    'class' => 'delete_hypothesis',
                                    'title' => 'Удалить',
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
                            echo 'подтвержден';
                        } elseif (!$model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                            echo 'ожидает подтверждения';
                        } elseif ($model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                            echo 'ожидает подтверждения';
                        } elseif ($model->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                            echo 'не подтвержен';
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

                        <?= Html::a('Редактировать', ['/contractor/mvps/get-hypothesis-to-update', 'id' => $model->getId()], [
                            'class' => 'btn btn-default update-hypothesis',
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

                        <?= Html::a('Удалить MVP', ['/contractor/mvps/delete', 'id' => $model->getId()], [
                            'class' => 'btn btn-default delete_hypothesis',
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

                        <div class="pb-10"></div>

                    <?php endif; ?>

                </div>
            </div>
        </div>

    <?php endforeach; ?>

    <div class="block_all_files pl-5 mb-15">

        <?php if (!empty($task->files)): ?>

            <label class="mt-30">Приложенные файлы:</label>

            <?php foreach ($task->files as $file){
                $filename = $file->getFileName();
                if(mb_strlen($filename) > 35){ $filename = mb_substr($file->getFileName(), 0, 35) . '...'; }
                echo '<div style="display: flex; margin: 2px 0; align-items: center;" class="one_block_file-'.$file->getId().'">' .
                    Html::a('<div style="display:flex; width: 100%; justify-content: space-between;"><div>' . $filename . '</div><div>'. Html::img('/images/icons/icon_export.png', ['style' => ['width' => '22px']]) .'</div></div>', ['/contractor/tasks/download', 'id' => $file->getId()], [
                        'title' => 'Скачать файл',
                        'target' => '_blank',
                        'class' => 'btn btn-default prefiles',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'background' => '#E0E0E0',
                            'width' => '320px',
                            'height' => '40px',
                            'text-align' => 'left',
                            'font-size' => '14px',
                            'border-radius' => '8px',
                            'margin-right' => '5px',
                        ]
                    ]) . ' ';

                if (User::isUserContractor(Yii::$app->user->identity['username']) && $task->getStatus() === ContractorTasks::TASK_STATUS_RETURNED) {

                    echo Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px', 'height' => '29px']]), ['/contractor/tasks/delete-file', 'id' => $file->getId()], [
                        'title' => 'Удалить файл',
                        'class' => 'delete_file',
                        'id' => 'delete_file-' . $file->getId(),
                        'style' => ['display' => 'flex', 'margin-left' => '15px'],
                    ]);
                }

                echo '</div>';
            }?>

        <?php endif; ?>
    </div>

    <?php if (User::isUserContractor(Yii::$app->user->identity['username']) && in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>

        <div class="mt-15 buttonShowTaskCompleteForm" style="display: flex; justify-content: center;">

            <?= Html::button('Завершить задание',[
                'class' => 'btn btn-default showTaskFormComplete',
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

        <div class="mt-15 blockTaskCompleteForm">

            <?php $form = ActiveForm::begin([
                'action' => Url::to(['/contractor/tasks/complete', 'id' => $task->getId()]),
                'id' => 'completeTaskForm',
                'options' => ['class' => 'g-py-15'],
                'errorCssClass' => 'u-has-error-v1',
                'successCssClass' => 'u-has-success-v1-1',
            ]); ?>

            <div class="row" style="margin-bottom: 15px;">
                <?= $form->field($formTaskComplete, 'comment', [
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

            <div class="row mobile-mt-20">
                <div class="">
                    <div class="add_files">
                        <div class="pl-5 text-center mt--5">
                            <label class="">Прикрепить файлы</label>
                            <span  class="pl-5 mt--5" style="color: #BDBDBD;">
                                    (максимум 5 файлов - png, jpg, jpeg, pdf, txt, doc, docx, xls)
                                </span>
                        </div>
                        <div class="error_files_count text-danger display-none pl-5 text-center mt--5">
                            Превышено максимальное количество файлов для загрузки.
                        </div>
                        <div class="pl-5 display-flex justify-content-center ml-50">
                            <?= $form->field($formTaskComplete, 'files[]', ['template' => "{label}\n{input}"])->fileInput(['multiple' => true])->label(false) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-15" style="display: flex; justify-content: center;">

                <?= Html::button('Отмена', [
                    'class' => 'btn btn-lg hiddenTaskFormComplete',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'width' => '150px',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                        'margin-right' => '10px',
                        'background' => '#4F4F4F',
                        'color' => '#FFFFFF',
                    ],
                ]) ?>

                <?= Html::submitButton('Завершить задание', [
                    'id' => 'submitTaskComplete',
                    'class' => 'btn btn-lg btn-success',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'background' => '#52BE7F',
                        'width' => '220px',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                    ],
                ]) ?>

            </div>

            <?php ActiveForm::end(); ?>
        </div>

    <?php endif; ?>

<?php else: ?>

    <h4 class="text-center">Ценностные предложения отсутствуют ...</h4>

<?php endif; ?>