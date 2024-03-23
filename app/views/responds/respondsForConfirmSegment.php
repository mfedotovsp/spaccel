<?php

use app\models\ConfirmSegment;
use app\models\ContractorTasks;
use app\models\EnableExpertise;
use app\models\InterviewConfirmSegment;
use app\models\Problems;
use app\models\ProjectCommunications;
use app\models\RespondsSegment;
use app\models\Segments;
use app\models\StageExpertise;
use app\models\StatusConfirmHypothesis;
use app\modules\contractor\models\form\FormTaskComplete;
use yii\helpers\Html;
use app\models\User;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var RespondsSegment[] $responds
 * @var ConfirmSegment $confirm
 * @var bool $isMobile
 * @var bool $isOnlyNotDelete
 * @var bool $isModuleContractor
 * @var int|null $currentTaskId
 */

?>

<!--Список респондентов-->
<div class="block_all_responds">

    <?php if ($isModuleContractor && count($responds) === 0): ?>
        <div class="text-center mt-15 font-size-18">У вас нет добавленных респондентов</div>
    <?php endif; ?>

    <?php foreach ($responds as $respond): ?>

        <?php /** @var ContractorTasks|null $task */
        $task = !$respond->getTaskId() ? null : ContractorTasks::findOne($respond->getTaskId()); ?>

        <?php if (!$isMobile): ?>

            <div class="row container-one_respond" style="margin: 3px 0; padding: 0;">

                <div class="col-md-3" style="display:flex; align-items: center;">

                    <div style="padding-right: 10px; padding-bottom: 3px;">

                        <?php
                        /** @var $interview InterviewConfirmSegment */
                        $interview = $isOnlyNotDelete ?
                            $respond->interview :
                            InterviewConfirmSegment::find(false)
                                ->andWhere(['respond_id' => $respond->getId()])
                                ->one();

                        if ($interview) {
                            if ($interview->getStatus() === 1) {
                                echo  Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px',]]);
                            }
                            elseif ($interview->getStatus() === 0) {
                                echo  Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px',]]);
                            }
                        }
                        else {
                            echo  Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px',]]);
                        }
                        ?>

                    </div>

                    <div class="" style="overflow: hidden; max-height: 60px; padding: 5px 0;">

                        <?php if (!$isModuleContractor && $isOnlyNotDelete && User::isUserSimple(Yii::$app->user->identity['username']) &&
                            $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE &&
                            !$respond->getContractorId()) : ?>

                            <?=  Html::a($respond->getName(), ['#'], [
                                'id' => "respond_name-" . $respond->getId(),
                                'class' => 'container-respond_name_link showRespondUpdateForm',
                                'title' => 'Редактировать данные респондента',
                            ]) ?>

                        <?php elseif ($task && $isOnlyNotDelete && User::isUserContractor(Yii::$app->user->identity['username']) &&
                            $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE && $respond->getContractorId() === Yii::$app->user->getId() &&
                            in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)) : ?>

                            <?=  Html::a($respond->getName(), ['#'], [
                                'id' => "respond_name-" . $respond->getId(),
                                'class' => 'container-respond_name_link showRespondUpdateForm',
                                'title' => 'Редактировать данные респондента',
                            ]) ?>

                        <?php else : ?>

                            <?php if (!User::isUserContractor(Yii::$app->user->identity['username']) && $respond->getContractorId()): ?>

                                <?php $contractorName = (User::findOne($respond->getContractorId()))->getUsername() ?>

                                <?=  Html::a('<div class="font-size-12"><span class="bolder">Исполнитель: </span>' . $contractorName. '</div><div>' . $respond->getName() . '</div>', ['#'], [
                                    'id' => "respond_name-" . $respond->getId(),
                                    'class' => 'container-respond_name_link showRespondUpdateForm',
                                    'title' => 'Данные респондента',
                                ]) ?>

                            <?php else : ?>

                                <?=  Html::a($respond->getName(), ['#'], [
                                    'id' => "respond_name-" . $respond->getId(),
                                    'class' => 'container-respond_name_link showRespondUpdateForm',
                                    'title' => 'Данные респондента',
                                ]) ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="col-md-2" style="font-size: 14px; padding: 0 10px 0 0; overflow: hidden; max-height: inherit;" title="<?= $respond->getInfoRespond() ?>">
                    <?= $respond->getInfoRespond() ?>
                </div>

                <div class="col-md-2" style="font-size: 14px; padding: 0 5px 0 0; overflow: hidden; max-height: inherit;" title="<?= $respond->getPlaceInterview() ?>">
                    <?= $respond->getPlaceInterview() ?>
                </div>

                <div class="col-md-1">

                    <?php
                    if ($respond->getDatePlan()) {

                        echo '<div class="text-center" style="padding: 0 5px; margin-left: -10px;">' . date("d.m.y", $respond->getDatePlan()) . '</div>';
                    }
                    ?>

                </div>

                <div class="col-md-1">

                    <?php
                    if ($interview){

                        $date_fact = date("d.m.y", $interview->getUpdatedAt());
                        echo '<div class="text-center" style="margin-left: -10px;">' . Html::encode($date_fact) . '</div>';

                    } elseif (!$isModuleContractor && $isOnlyNotDelete && !empty($respond->getInfoRespond()) && !empty($respond->getPlaceInterview()) && $respond->getDatePlan()
                        && User::isUserSimple(Yii::$app->user->identity['username']) && !$respond->getContractorId()){

                        echo '<div class="text-center" style="margin-left: -10px;">' . Html::a(
                                Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]),
                                ['/responds/data-availability', 'stage' => $confirm->getStage(), 'id' => $confirm->getId()], [
                                'id' => 'respond_descInterview_form-' . $respond->getId(),
                                'class' => 'showDescInterviewCreateForm',
                                'title' => 'Добавить интервью'
                            ]) .
                            '</div>';

                    } elseif ($task && $isOnlyNotDelete && ($task->getId() === $currentTaskId) && !empty($respond->getInfoRespond()) && !empty($respond->getPlaceInterview()) && $respond->getDatePlan()
                        && User::isUserContractor(Yii::$app->user->identity['username']) && $respond->getContractorId() === Yii::$app->user->getId() &&
                        in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)){

                        echo '<div class="text-center" style="margin-left: -10px;">' . Html::a(
                                Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]),
                                ['/responds/data-availability', 'stage' => $confirm->getStage(), 'id' => $confirm->getId()], [
                                'id' => 'respond_descInterview_form-' . $respond->getId(),
                                'class' => 'showDescInterviewCreateForm',
                                'title' => 'Добавить интервью'
                            ]) .
                            '</div>';
                    } ?>

                </div>

                <?php if ($interview) : ?>
                    <div class="col-md-2" style="font-size: 14px; padding: 0; overflow: hidden; max-height: inherit;" title="<?= $interview->getResult() ?>">
                        <?= $interview->getResult() ?>
                    </div>
                <?php else : ?>
                    <div class="col-md-2"></div>
                <?php endif; ?>

                <?php if (!$isModuleContractor && $isOnlyNotDelete && User::isUserSimple(Yii::$app->user->identity['username']) &&
                    $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE
                    && !$respond->getContractorId()) : ?>

                    <div class="col-md-1" style="text-align: right;">

                        <?php
                        if ($respond->interview) {

                            echo Html::a(Html::img('/images/icons/update_warning_vector.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]), ['#'], [
                                'id' => 'descInterview_form-' . $respond->interview->getId(),
                                'class' => 'showDescInterviewUpdateForm',
                                'title' => 'Редактировать результаты интервью',
                            ]);
                        }

                        echo Html::a(Html::img('/images/icons/icon_delete.png',
                            ['style' => ['width' => '24px']]), ['#'], [
                            'id' => 'link_respond_delete-' . $respond->getId(),
                            'class' => 'showDeleteRespondModal',
                            'title' => 'Удалить респондента',
                        ]);
                        ?>

                    </div>

                <?php elseif ($task && $isOnlyNotDelete && ($task->getId() === $currentTaskId) && User::isUserContractor(Yii::$app->user->identity['username']) &&
                    $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE && $respond->getContractorId() === Yii::$app->user->getId() &&
                    in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)) : ?>

                    <div class="col-md-1" style="text-align: right;">

                        <?php
                        if ($respond->interview) {

                            echo Html::a(Html::img('/images/icons/update_warning_vector.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]), ['#'], [
                                'id' => 'descInterview_form-' . $respond->interview->getId(),
                                'class' => 'showDescInterviewUpdateForm',
                                'title' => 'Редактировать результаты интервью',
                            ]);
                        }

                        echo Html::a(Html::img('/images/icons/icon_delete.png',
                            ['style' => ['width' => '24px']]), ['#'], [
                            'id' => 'link_respond_delete-' . $respond->getId(),
                            'class' => 'showDeleteRespondModal',
                            'title' => 'Удалить респондента',
                        ]);
                        ?>

                    </div>

                <?php else : ?>

                    <div class="col-md-1" style="text-align: center;">

                        <?php
                        if ($interview) {

                            echo Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px']]), ['#'], [
                                'id' => 'descInterview_form-' . $interview->getId(),
                                'class' => 'showDescInterviewUpdateForm',
                                'title' => 'Результаты опроса',
                            ]);
                        }
                        ?>

                    </div>

                <?php endif; ?>

            </div>

        <?php else: ?>

            <div class="hypothesis_table_mobile" style="margin-bottom: 5px;">
                <div class="row container-one_hypothesis_mobile row_hypothesis-<?= $respond->getId() ?>">

                    <div class="col-xs-12">

                        <?php if (!User::isUserContractor(Yii::$app->user->identity['username']) && $respond->getContractorId()): ?>

                            <?php $contractorName = (User::findOne($respond->getContractorId()))->getUsername() ?>

                            <div class="font-size-12">
                                <span class="bolder">Исполнитель: </span>
                                <?= $contractorName ?>
                            </div>

                        <?php endif; ?>

                        <div class="hypothesis_title_mobile">
                            <?= $respond->getName() ?>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <span class="header_table_hypothesis_mobile">Статус</span>
                        <span class="text_14_table_hypothesis">
                            <?php
                            /** @var $interview InterviewConfirmSegment */
                            $interview = $isOnlyNotDelete ?
                                $respond->interview :
                                InterviewConfirmSegment::find(false)
                                    ->andWhere(['respond_id' => $respond->getId()])
                                    ->one();

                            if ($interview) {
                                if ($interview->getStatus() === 1) {
                                    echo 'соответствует сегменту';
                                }
                                elseif ($interview->getStatus() === 0) {
                                    echo 'не соответствует сегменту';
                                }
                            }
                            else {
                                echo 'ожидает проведения интервью';
                            }
                            ?>
                        </span>
                    </div>

                    <?php if ($respond->getEmail()): ?>
                        <div class="col-xs-12">
                            <span class="header_table_hypothesis_mobile">Email</span>
                            <span class="text_14_table_hypothesis">
                                <?= $respond->getEmail() ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ($respond->getInfoRespond()): ?>
                        <div class="col-xs-12">
                            <div class="header_table_hypothesis_mobile">Данные респондента (кто, откуда, чем занят)</div>
                            <div class="text_14_table_hypothesis">
                                <?= $respond->getInfoRespond() ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($respond->getPlaceInterview()): ?>
                        <div class="col-xs-12">
                            <div class="header_table_hypothesis_mobile">Место проведения интервью</div>
                            <div class="text_14_table_hypothesis">
                                <?= $respond->getPlaceInterview() ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($interview): ?>
                        <div class="col-xs-12">
                            <div class="header_table_hypothesis_mobile">Варианты проблем</div>
                            <div class="text_14_table_hypothesis">
                                <?= $interview->getResult() ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($respond->getDatePlan()): ?>
                        <div class="col-xs-6">
                            <div class="header_table_hypothesis_mobile">Плановая дата</div>
                            <div class="text_14_table_hypothesis">
                                <?= date('d.m.Y', $respond->getDatePlan()) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($interview): ?>
                        <div class="col-xs-6">
                            <div class="header_table_hypothesis_mobile">Фактическая дата</div>
                            <div class="text_14_table_hypothesis">
                                <?= date('d.m.Y', $interview->getUpdatedAt()) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-xs-6"></div>
                    <?php endif; ?>

                    <?php if (!$isModuleContractor && $isOnlyNotDelete && User::isUserSimple(Yii::$app->user->identity['username']) &&
                        $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE &&
                        !$respond->getContractorId()) : ?>

                        <div class="hypothesis_buttons_mobile">

                            <?php if ($respond->interview): ?>

                                <?= Html::a('Редактировать интервью', ['#'], [
                                    'id' => 'descInterview_form-' . $respond->interview->getId(),
                                    'class' => 'btn btn-default showDescInterviewUpdateForm',
                                    'style' => [
                                        'display' => 'flex',
                                        'width' => '96%',
                                        'height' => '36px',
                                        'background' => '#52BE7F',
                                        'color' => '#FFFFFF',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'border-radius' => '0',
                                        'border' => '1px solid #ffffff',
                                        'font-size' => '18px',
                                        'margin' => '10px 2% 0% 2%',
                                    ]
                                ]) ?>

                            <?php elseif(!empty($respond->getInfoRespond()) && !empty($respond->getPlaceInterview()) && $respond->getDatePlan()): ?>

                                <?= Html::a('Добавить интервью', ['/responds/data-availability', 'stage' => $confirm->getStage(), 'id' => $confirm->getId()], [
                                    'id' => 'respond_descInterview_form-' . $respond->getId(),
                                    'class' => 'btn btn-default showDescInterviewCreateForm',
                                    'style' => [
                                        'display' => 'flex',
                                        'width' => '96%',
                                        'height' => '36px',
                                        'background' => '#52BE7F',
                                        'color' => '#FFFFFF',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'border-radius' => '0',
                                        'border' => '1px solid #ffffff',
                                        'font-size' => '18px',
                                        'margin' => '10px 2% 0% 2%',
                                    ]
                                ])?>

                            <?php endif; ?>

                        </div>

                        <div class="hypothesis_buttons_mobile">

                            <?= Html::a('Редактировать', ['#'], [
                                'id' => "respond_name-" . $respond->getId(),
                                'class' => 'btn btn-default showRespondUpdateForm',
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
                                    'margin' => '10px 1% 0% 2%',
                                ],
                            ]) ?>

                            <?= Html::a('Удалить респондента', ['#'], [
                                'id' => 'link_respond_delete-' . $respond->getId(),
                                'class' => 'btn btn-default showDeleteRespondModal',
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
                                    'margin' => '10px 2% 0% 1%',
                                ],
                            ]) ?>

                        </div>

                    <?php elseif ($task && $isOnlyNotDelete && ($task->getId() === $currentTaskId) && User::isUserContractor(Yii::$app->user->identity['username']) &&
                        $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE && $respond->getContractorId() === Yii::$app->user->getId() &&
                        in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)) : ?>


                        <div class="hypothesis_buttons_mobile">

                            <?php if ($respond->interview): ?>

                                <?= Html::a('Редактировать интервью', ['#'], [
                                    'id' => 'descInterview_form-' . $respond->interview->getId(),
                                    'class' => 'btn btn-default showDescInterviewUpdateForm',
                                    'style' => [
                                        'display' => 'flex',
                                        'width' => '96%',
                                        'height' => '36px',
                                        'background' => '#52BE7F',
                                        'color' => '#FFFFFF',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'border-radius' => '0',
                                        'border' => '1px solid #ffffff',
                                        'font-size' => '18px',
                                        'margin' => '10px 2% 0% 2%',
                                    ]
                                ]) ?>

                            <?php elseif(!empty($respond->getInfoRespond()) && !empty($respond->getPlaceInterview()) && $respond->getDatePlan()): ?>

                                <?= Html::a('Добавить интервью', ['/responds/data-availability', 'stage' => $confirm->getStage(), 'id' => $confirm->getId()], [
                                    'id' => 'respond_descInterview_form-' . $respond->getId(),
                                    'class' => 'btn btn-default showDescInterviewCreateForm',
                                    'style' => [
                                        'display' => 'flex',
                                        'width' => '96%',
                                        'height' => '36px',
                                        'background' => '#52BE7F',
                                        'color' => '#FFFFFF',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'border-radius' => '0',
                                        'border' => '1px solid #ffffff',
                                        'font-size' => '18px',
                                        'margin' => '10px 2% 0% 2%',
                                    ]
                                ])?>

                            <?php endif; ?>

                        </div>

                        <div class="hypothesis_buttons_mobile">

                            <?= Html::a('Редактировать', ['#'], [
                                'id' => "respond_name-" . $respond->getId(),
                                'class' => 'btn btn-default showRespondUpdateForm',
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
                                    'margin' => '10px 1% 0% 2%',
                                ],
                            ]) ?>

                            <?= Html::a('Удалить респондента', ['#'], [
                                'id' => 'link_respond_delete-' . $respond->getId(),
                                'class' => 'btn btn-default showDeleteRespondModal',
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
                                    'margin' => '10px 2% 0% 1%',
                                ],
                            ]) ?>

                        </div>

                    <?php else: ?>

                        <?php if ($interview): ?>

                            <div class="hypothesis_buttons_mobile">

                                <?= Html::a('Смотреть данные интервью', ['#'], [
                                    'id' => 'descInterview_form-' . $interview->getId(),
                                    'class' => 'btn btn-default showDescInterviewUpdateForm',
                                    'style' => [
                                        'display' => 'flex',
                                        'width' => '96%',
                                        'height' => '36px',
                                        'background' => '#52BE7F',
                                        'color' => '#FFFFFF',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'border-radius' => '0',
                                        'border' => '1px solid #ffffff',
                                        'font-size' => '18px',
                                        'margin' => '10px 2% 0% 2%',
                                    ]
                                ]) ?>

                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>

        <?php endif; ?>

    <?php  endforeach;?>

</div>

<?php if (!User::isUserContractor(Yii::$app->user->identity['username']) && !$isModuleContractor): ?>

    <div class="row container-fluid confirm-view-bottom-report-desktop" style="position: absolute; bottom: 0; width: 100%;">

        <div class="col-md-12" style="color: #4F4F4F; font-size: 16px; display: flex; justify-content: space-between; padding: 10px 20px; border-radius: 12px; border: 2px solid #707F99; align-items: center; margin-top: 30px;">

            <div class="" style="padding: 0;">
                Необходимо респондентов: <?= $confirm->getCountPositive() ?>
            </div>

            <div class="" style="padding: 0;">
                Внесено респондентов: <?= $confirm->getCountRespondsOfModel() ?>
            </div>

            <div class="" style="padding: 0;">
                <?= Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px',]]) ?>
                Соответствуют сегменту: <?= $confirm->getCountConfirmMembers() ?>
            </div>

            <div class="" style="padding: 0;">
                <?= Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px',]]) ?>
                Не соответствуют сегменту: <?= ($confirm->getCountDescInterviewsOfModel() - $confirm->getCountConfirmMembers()) ?>
            </div>

            <div class="" style="padding: 0;">
                <?= Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px',]]) ?>
                Не опрошены: <?= ($confirm->getCountRespond() - $confirm->getCountDescInterviewsOfModel()) ?>
            </div>

            <div style="display:flex; align-items:center; padding: 0;">

                <?php if ($confirm->getEnableExpertise() === EnableExpertise::ON) : ?>

                    <?php if ($isOnlyNotDelete && User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                        <?php if (ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $confirm->hypothesis->getProjectId())) : ?>

                            <?= Html::a('Экспертиза',['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::CONFIRM_SEGMENT], 'stageId' => $confirm->getId()], [
                                'class' => 'link-get-list-expertise btn btn-lg btn-default',
                                'title' => 'Экспертиза',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#707F99',
                                    'color' => '#FFFFFF',
                                    'width' => '140px',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                    'margin-right' => '10px',
                                ],
                            ]) ?>

                        <?php endif; ?>

                    <?php else : ?>

                        <?= Html::a('Экспертиза',['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::CONFIRM_SEGMENT], 'stageId' => $confirm->getId()], [
                            'class' => 'link-get-list-expertise btn btn-lg btn-default',
                            'title' => 'Смотреть экспертизу',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#707F99',
                                'color' => '#FFFFFF',
                                'width' => '140px',
                                'height' => '40px',
                                'font-size' => '24px',
                                'border-radius' => '8px',
                                'margin-right' => '10px',
                            ],
                        ]) ?>

                    <?php endif; ?>

                <?php endif; ?>

                <?php if ($isOnlyNotDelete): ?>

                    <?php if (User::isUserSimple(Yii::$app->user->identity['username']) && $confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                        <?php
                        $existTasksNotCompleted = ContractorTasks::find()
                            ->andWhere(['type' => StageExpertise::CONFIRM_SEGMENT])
                            ->andWhere(['hypothesis_id' => $confirm->getId()])
                            ->andWhere(['in', 'status', [
                                ContractorTasks::TASK_STATUS_NEW,
                                ContractorTasks::TASK_STATUS_PROCESS,
                                ContractorTasks::TASK_STATUS_COMPLETED,
                                ContractorTasks::TASK_STATUS_RETURNED
                            ]])
                            ->exists();

                        if (!$existTasksNotCompleted && $confirm->getButtonMovingNextStage()) : ?>

                            <?= Html::a( 'Далее', ['/confirm-segment/moving-next-stage', 'id' => $confirm->getId()],[
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#52BE7F',
                                    'width' => '140px',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                ],
                                'class' => 'btn btn-lg btn-success',
                                'id' => 'button_MovingNextStage',
                            ]) ?>

                        <?php else : ?>

                            <?php if (!$existTasksNotCompleted && (($confirm->getCountRespond() - $confirm->getCountDescInterviewsOfModel()) === 0)) : ?>

                                <?= Html::a( 'Далее', ['/confirm-segment/moving-next-stage', 'id' => $confirm->getId()],[
                                    'style' => [
                                        'display' => 'flex',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'background' => '#eb5757',
                                        'color' => '#FFFFFF',
                                        'width' => '140px',
                                        'height' => '40px',
                                        'font-size' => '24px',
                                        'border-radius' => '8px',
                                    ],
                                    'class' => 'btn btn-lg btn-default',
                                    'id' => 'button_MovingNextStage',
                                ]) ?>

                            <?php else : ?>

                                <?= Html::a( 'Далее', ['/confirm-segment/moving-next-stage', 'id' => $confirm->getId()],[
                                    'style' => [
                                        'display' => 'flex',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'background' => '#E0E0E0',
                                        'color' => '#FFFFFF',
                                        'width' => '140px',
                                        'height' => '40px',
                                        'font-size' => '24px',
                                        'border-radius' => '8px',
                                    ],
                                    'class' => 'btn btn-lg btn-default',
                                    'id' => 'button_MovingNextStage',
                                ]) ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    <?php else : ?>

                        <?php if ($confirm->hypothesis->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                            <?= Html::a( 'Далее', ['/problems/index', 'id' => $confirm->getId()],[
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#52BE7F',
                                    'width' => '140px',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                ],
                                'class' => 'btn btn-lg btn-success',
                            ]) ?>

                        <?php else : ?>

                            <?= Html::a( 'Далее', ['#'],[
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#E0E0E0',
                                    'color' => '#FFFFFF',
                                    'width' => '140px',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                ],
                                'class' => 'btn btn-lg btn-default',
                                'onclick' => 'return false',
                            ]) ?>

                        <?php endif; ?>

                    <?php endif; ?>

                <?php else: ?>

                    <?php
                    $problemExist = Problems::find(false)
                        ->andWhere(['segment_id' => $confirm->getSegmentId()])
                        ->exists();

                    if ($problemExist): ?>

                        <?= Html::a( 'Далее', ['/problems/index', 'id' => $confirm->getId()],[
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#52BE7F',
                                'width' => '140px',
                                'height' => '40px',
                                'font-size' => '24px',
                                'border-radius' => '8px',
                            ],
                            'class' => 'btn btn-lg btn-success',
                        ]) ?>

                    <?php else : ?>

                        <?= Html::a( 'Далее', ['#'],[
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'background' => '#E0E0E0',
                                'color' => '#FFFFFF',
                                'width' => '140px',
                                'height' => '40px',
                                'font-size' => '24px',
                                'border-radius' => '8px',
                            ],
                            'class' => 'btn btn-lg btn-default',
                            'onclick' => 'return false',
                        ]) ?>

                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>

        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>
            <?php /** @var Segments $hypothesis */
            $hypothesis = Segments::find(false)
                ->andWhere(['id' => $confirm->getSegmentId()])
                ->one(); ?>

            <div class="col-md-12" style="color: #4F4F4F; font-size: 16px; display: flex; justify-content: space-around; padding: 10px 20px; border-radius: 12px; border: 2px solid #707F99; align-items: center; margin-top: 10px;">
                <?php if (!$hypothesis->getDeletedAt() && $hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE): ?>
                    <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '22px']]) . '</div><div class="pl-10">Добавить задание исполнителю</div></div>', [
                        '/tasks/get-task-create', 'projectId' => $hypothesis->getProjectId(), 'stage' => StageExpertise::CONFIRM_SEGMENT, 'stageId' => $confirm->getId()],
                        ['id' => 'showFormContractorTaskCreate', 'class' => 'new_hypothesis_link_small_plus pull-left']
                    ) ?>
                <?php endif; ?>
                <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img('/images/icons/icon_view.png', ['style' => ['width' => '24px']]) . '</div><div class="pl-10">Задания исполнителям</div></div>', [
                    '/tasks/get-tasks', 'projectId' => $hypothesis->getProjectId(), 'stage' => StageExpertise::CONFIRM_SEGMENT, 'stageId' => $confirm->getId()],
                    ['id' => 'showContractorTasksGet', 'class' => 'new_hypothesis_link_small_plus pull-left']
                ) ?>
            </div>
        <?php endif; ?>

    </div>


    <div class="confirm-view-bottom-report-mobile">
        <div class="row container-fluid " style="color: #4F4F4F; font-size: 14px; font-weight: 700; padding: 10px 20px; border-radius: 12px; border: 2px solid #707F99; margin: 0;">

            <div class="col-xs-12 text-center" style="font-size: 16px; text-transform: uppercase; margin-bottom: 10px;">Результаты интервью:</div>

            <div class="col-xs-12" style="padding: 0;">
                Необходимо респондентов: <?= $confirm->getCountPositive() ?>
            </div>

            <div class="col-xs-12" style="padding: 0;">
                Внесено респондентов: <?= $confirm->getCountRespondsOfModel() ?>
            </div>

            <div class="col-xs-12" style="padding: 0;">
                Соответствуют сегменту: <?= $confirm->getCountConfirmMembers() ?>
            </div>

            <div class="col-xs-12" style="padding: 0;">
                Не соответствуют сегменту: <?= ($confirm->getCountDescInterviewsOfModel() - $confirm->getCountConfirmMembers()) ?>
            </div>

            <div class="col-xs-12" style="padding: 0;">
                Не опрошены: <?= ($confirm->getCountRespond() - $confirm->getCountDescInterviewsOfModel()) ?>
            </div>

            <div class="col-xs-12 hypothesis_buttons_mobile" style="padding: 0;">

                <?php if ($confirm->getEnableExpertise() === EnableExpertise::ON) : ?>

                    <?php if ($isOnlyNotDelete && User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                        <?php if (ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $confirm->hypothesis->getProjectId())) : ?>

                            <?= Html::a('Экспертиза',['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::CONFIRM_SEGMENT], 'stageId' => $confirm->getId()], [
                                'class' => 'link-get-list-expertise btn btn-lg btn-default',
                                'style' => [
                                    'display' => 'flex',
                                    'width' => '96%',
                                    'height' => '36px',
                                    'background' => '#52BE7F',
                                    'color' => '#FFFFFF',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'border-radius' => '0',
                                    'border' => '1px solid #ffffff',
                                    'font-size' => '18px',
                                    'margin-top' => '10px'
                                ]
                            ]) ?>

                        <?php endif; ?>

                    <?php else : ?>

                        <?= Html::a('Смотреть экспертизу',['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::CONFIRM_SEGMENT], 'stageId' => $confirm->getId()], [
                            'class' => 'link-get-list-expertise btn btn-lg btn-default',
                            'style' => [
                                'display' => 'flex',
                                'width' => '96%',
                                'height' => '36px',
                                'background' => '#52BE7F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin-top' => '10px'
                            ]
                        ]) ?>

                    <?php endif; ?>

                <?php endif; ?>
            </div>

        </div>
    </div>

<?php elseif (User::isUserContractor(Yii::$app->user->identity['username'])): ?>

    <?php /** @var ContractorTasks|null $contractorTask */
    $contractorTask = null;
    $formTaskComplete = new FormTaskComplete();
    $isCompleteInterviews = false;
    if (count($responds) > 0) {
        $contractorTask = $responds[0]->getTaskId() ? ContractorTasks::findOne($responds[0]->getTaskId()) : null;
        $countInterviews = 0;
        foreach ($responds as $respond) {
            if ($respond->interview) {
                $countInterviews++;
            }
        }
        $isCompleteInterviews = count($responds) === $countInterviews;
    }
    ?>

    <?php if ($contractorTask && $isCompleteInterviews && (int)$_GET['id'] === $contractorTask->getId() && in_array($contractorTask->getStatus(), [ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)): ?>

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
                'action' => Url::to(['/contractor/tasks/complete', 'id' => $contractorTask->getId()]),
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

            <div class="row mb-15" style="display: flex; justify-content: center;">
                <?= Html::submitButton('Завершить задание', [
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

<?php endif; ?>
