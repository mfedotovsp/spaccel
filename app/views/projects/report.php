<?php

use app\models\BusinessModel;
use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;

/**
 * @var Segments[] $segments
 */

?>

<div class="report-project">

    <!--Шапка таблицы-->
    <div class="report-project-header">

        <div class="left_part_header">Наименование этапа</div>

        <div class="right_part_header">

            <div class="right_part_header_top">Результаты проведенных тестов</div>

            <div class="right_part_header_bottom">

                <div>Запланировано</div>
                <div>Необходимо</div>
                <div>Положительные</div>
                <div>Отрицательные</div>
                <div>Не опрошены</div>
                <div>Статус</div>
                <div>Бизнес-модель</div>

            </div>
        </div>
    </div>

    <!--Строки сегментов-->
    <?php foreach ($segments as $segment) : ?>

        <!--Если у сегмента существует подтверждение-->
        <?php
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $segment->getProjectId()])
            ->one();

        /** @var $confirmSegment ConfirmSegment */
        $confirmSegment = !$project->getDeletedAt() ?
            $segment->confirm :
            ConfirmSegment::find(false)
                ->andWhere(['segment_id' => $segment->getId()])
                ->one();

        if($confirmSegment) : ?>

        <div class="stage_data_string">

            <div class="column_title_of_segment"><?= $segment->propertyContainer->getProperty('title') ?></div>

            <?php if (mb_strlen($segment->getName()) > 50) : ?>

                <div class="column_description_of_segment column_block_text_max_1800" title="<?= $segment->getName() ?>">
                    <?= Html::a(mb_substr($segment->getName(), 0, 50) . '...', ['/segments/index', 'id' => $segment->getProjectId()],
                        ['class' => 'link_for_description_stage']) ?>
                </div>

            <?php else : ?>

                <div class="column_description_of_segment column_block_text_max_1800">
                    <?= Html::a($segment->getName(), ['/segments/index', 'id' => $segment->getProjectId()],
                        ['class' => 'link_for_description_stage']) ?>
                </div>

            <?php endif; ?>


            <div class="column_description_of_segment column_block_text_min_1800">
                <?= Html::a($segment->getName(), ['/segments/index', 'id' => $segment->getProjectId()],
                    ['class' => 'link_for_description_stage']) ?>
            </div>


            <div class="column_stage_confirm"><?= $confirmSegment->getCountRespond() ?></div>

            <div class="column_stage_confirm"><?= $confirmSegment->getCountPositive() ?></div>

            <div class="column_stage_confirm"><?= $confirmSegment->isExistDesc() ? 1 : $confirmSegment->getCountConfirmMembers() ?></div>

            <div class="column_stage_confirm"><?= ($confirmSegment->getCountDescInterviewsOfModel() - $confirmSegment->getCountConfirmMembers()) ?></div>

            <div class="column_stage_confirm"><?= ($confirmSegment->getCountRespond() - $confirmSegment->getCountDescInterviewsOfModel()) ?></div>

            <div class="column_stage_confirm">

                <?php if ($segment->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                    <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                        ['/confirm-segment/view', 'id' => $confirmSegment->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                <?php elseif ($segment->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                    <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                        ['/confirm-segment/view', 'id' => $confirmSegment->getId()], ['title' => 'Продолжить подтверждение']) ?>

                <?php elseif ($segment->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                    <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                        ['/confirm-segment/view', 'id' => $confirmSegment->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                <?php endif; ?>

            </div>

            <div class="column_stage_confirm"></div>

        </div>

        <!--Если у сегмента не существует подтверждения-->
        <?php else : ?>

        <div class="stage_data_string">

            <div class="column_title_of_segment"><?= $segment->propertyContainer->getProperty('title') ?></div>


            <?php if (mb_strlen($segment->getName()) > 50) : ?>

                <div class="column_description_of_segment column_block_text_max_1800" title="<?= $segment->getName() ?>">
                    <?= Html::a(mb_substr($segment->getName(), 0, 50) . '...', ['/segments/index', 'id' => $segment->getProjectId()],
                        ['class' => 'link_for_description_stage']) ?>
                </div>

            <?php else : ?>

                <div class="column_description_of_segment column_block_text_max_1800">
                    <?= Html::a($segment->getName(), ['/segments/index', 'id' => $segment->getProjectId()],
                        ['class' => 'link_for_description_stage']) ?>
                </div>

            <?php endif; ?>


            <div class="column_description_of_segment column_block_text_min_1800">
                <?= Html::a($segment->getName(), ['/segments/index', 'id' => $segment->getProjectId()],
                    ['class' => 'link_for_description_stage']) ?>
            </div>


            <div class="column_stage_confirm">-</div>
            <div class="column_stage_confirm">-</div>
            <div class="column_stage_confirm">-</div>
            <div class="column_stage_confirm">-</div>
            <div class="column_stage_confirm">-</div>

            <div class="column_stage_confirm">
                <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                    ['/confirm-segment/create', 'id' => $segment->getId()], ['title' => 'Создать подтверждение']) ?>
            </div>

            <div class="column_stage_confirm"></div>

        </div>

        <?php endif; ?>

        <!--Строки проблем сегментов-->
        <?php /** @var $problems Problems[] */
        $problems = !$project->getDeletedAt() ?
            $segment->problems :
            Problems::find(false)
                ->andWhere(['segment_id' => $segment->getId()])
                ->all();

        foreach ($problems as $problem) : ?>

            <!--Если у проблемы существует подтверждение-->
            <?php /** @var $confirmProblem ConfirmProblem */
            $confirmProblem = !$project->getDeletedAt() ?
                $problem->confirm :
                ConfirmProblem::find(false)
                    ->andWhere(['problem_id' => $problem->getId()])
                    ->one();

            if($confirmProblem) : ?>

            <div class="stage_data_string">

                <div class="column_title_of_stage"><?= $problem->propertyContainer->getProperty('title') ?></div>


                <?php if (mb_strlen($problem->getDescription()) > 100) : ?>

                    <div class="column_description_of_stage column_block_text_max_1800" title="<?= $problem->getDescription() ?>">
                        <?= Html::a(mb_substr($problem->getDescription(), 0, 100) . '...', ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php else : ?>

                    <div class="column_description_of_stage column_block_text_max_1800">
                        <?= Html::a($problem->getDescription(), ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php endif; ?>


                <?php if (mb_strlen($problem->getDescription()) > 130) : ?>

                    <div class="column_description_of_stage column_block_text_min_1800" title="<?= $problem->getDescription() ?>">
                        <?= Html::a(mb_substr($problem->getDescription(), 0, 130) . '...', ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php else : ?>

                    <div class="column_description_of_stage column_block_text_min_1800">
                        <?= Html::a($problem->getDescription(), ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php endif; ?>


                <div class="column_stage_confirm"><?= $confirmProblem->getCountRespond() ?></div>

                <div class="column_stage_confirm"><?= $confirmProblem->getCountPositive() ?></div>

                <div class="column_stage_confirm"><?= $confirmProblem->isExistDesc() ? 1 : $confirmProblem->getCountConfirmMembers() ?></div>

                <div class="column_stage_confirm"><?= ($confirmProblem->getCountDescInterviewsOfModel() - $confirmProblem->getCountConfirmMembers()) ?></div>

                <div class="column_stage_confirm"><?= ($confirmProblem->getCountRespond() - $confirmProblem->getCountDescInterviewsOfModel()) ?></div>

                <div class="column_stage_confirm">

                    <?php if ($problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                        <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                            ['/confirm-problem/view', 'id' => $confirmProblem->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                    <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                        <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                            ['/confirm-problem/view', 'id' => $confirmProblem->getId()], ['title' => 'Продолжить подтверждение']) ?>

                    <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                        <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                            ['/confirm-problem/view', 'id' => $confirmProblem->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                    <?php endif; ?>

                </div>

                <div class="column_stage_confirm"></div>

            </div>

            <!--Если у проблемы не существует подтверждения-->
            <?php else : ?>

            <div class="stage_data_string">

                <div class="column_title_of_stage"><?= $problem->propertyContainer->getProperty('title') ?></div>


                <?php if (mb_strlen($problem->getDescription()) > 100) : ?>

                    <div class="column_description_of_stage column_block_text_max_1800" title="<?= $problem->getDescription() ?>">
                        <?= Html::a(mb_substr($problem->getDescription(), 0, 100) . '...', ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php else : ?>

                    <div class="column_description_of_stage column_block_text_max_1800">
                        <?= Html::a($problem->getDescription(), ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php endif; ?>


                <?php if (mb_strlen($problem->getDescription()) > 130) : ?>

                    <div class="column_description_of_stage column_block_text_min_1800" title="<?= $problem->getDescription() ?>">
                        <?= Html::a(mb_substr($problem->getDescription(), 0, 130) . '...', ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php else : ?>

                    <div class="column_description_of_stage column_block_text_min_1800">
                        <?= Html::a($problem->getDescription(), ['/problems/index/', 'id' => $problem->getConfirmSegmentId()],
                            ['class' => 'link_for_description_stage']) ?>
                    </div>

                <?php endif; ?>


                <div class="column_stage_confirm">-</div>
                <div class="column_stage_confirm">-</div>
                <div class="column_stage_confirm">-</div>
                <div class="column_stage_confirm">-</div>
                <div class="column_stage_confirm">-</div>

                <div class="column_stage_confirm">
                    <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                        ['/confirm-problem/create', 'id' => $problem->getId()], ['title' => 'Создать подтверждение']) ?>
                </div>

                <div class="column_stage_confirm"></div>

            </div>

            <?php endif; ?>

            <!--Строки ценностных предложений-->
            <?php /** @var $gcps Gcps[] */
            $gcps = !$project->getDeletedAt() ?
                $problem->gcps :
                Gcps::find(false)
                    ->andWhere(['problem_id' => $problem->getId()])
                    ->all();

            foreach ($gcps as $gcp) : ?>

                <!--Если у ценностного предложения существует подтверждение-->
                <?php /** @var $confirmGcp ConfirmGcp */
                $confirmGcp = !$project->getDeletedAt() ?
                    $gcp->confirm :
                    ConfirmGcp::find(false)
                        ->andWhere(['gcp_id' => $gcp->getId()])
                        ->one();

                if($confirmGcp) : ?>

                    <div class="stage_data_string">

                        <div class="column_title_of_stage"><?= $gcp->propertyContainer->getProperty('title') ?></div>


                        <?php if (mb_strlen($gcp->getDescription()) > 100) : ?>

                            <div class="column_description_of_stage column_block_text_max_1800" title="<?= $gcp->getDescription() ?>">
                                <?= Html::a(mb_substr($gcp->getDescription(), 0, 100) . '...', ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php else : ?>

                            <div class="column_description_of_stage column_block_text_max_1800">
                                <?= Html::a($gcp->getDescription(), ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php endif; ?>


                        <?php if (mb_strlen($gcp->getDescription()) > 130) : ?>

                            <div class="column_description_of_stage column_block_text_min_1800" title="<?= $gcp->getDescription() ?>">
                                <?= Html::a(mb_substr($gcp->getDescription(), 0, 130) . '...', ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php else : ?>

                            <div class="column_description_of_stage column_block_text_min_1800">
                                <?= Html::a($gcp->getDescription(), ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php endif; ?>


                        <div class="column_stage_confirm"><?= $confirmGcp->getCountRespond() ?></div>

                        <div class="column_stage_confirm"><?= $confirmGcp->getCountPositive() ?></div>

                        <div class="column_stage_confirm"><?= $confirmGcp ->isExistDesc() ? 1 : $confirmGcp->getCountConfirmMembers() ?></div>

                        <div class="column_stage_confirm"><?= ($confirmGcp->getCountDescInterviewsOfModel() - $confirmGcp->getCountConfirmMembers()) ?></div>

                        <div class="column_stage_confirm"><?= ($confirmGcp->getCountRespond() - $confirmGcp->getCountDescInterviewsOfModel()) ?></div>

                        <div class="column_stage_confirm">

                            <?php if ($gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                                    ['/confirm-gcp/view', 'id' => $confirmGcp->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                            <?php elseif ($gcp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                    ['/confirm-gcp/view', 'id' => $confirmGcp->getId()], ['title' => 'Продолжить подтверждение']) ?>

                            <?php elseif ($gcp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                                    ['/confirm-gcp/view', 'id' => $confirmGcp->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                            <?php endif; ?>

                        </div>

                        <div class="column_stage_confirm"></div>

                    </div>

                <!--Если у ценностного предложения не существует подтверждения-->
                <?php else : ?>

                    <div class="stage_data_string">

                        <div class="column_title_of_stage"><?= $gcp->propertyContainer->getProperty('title') ?></div>

                        <?php if (mb_strlen($gcp->getDescription()) > 100) : ?>

                            <div class="column_description_of_stage column_block_text_max_1800" title="<?= $gcp->getDescription() ?>">
                                <?= Html::a(mb_substr($gcp->getDescription(), 0, 100) . '...', ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php else : ?>

                            <div class="column_description_of_stage column_block_text_max_1800">
                                <?= Html::a($gcp->getDescription(), ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php endif; ?>


                        <?php if (mb_strlen($gcp->getDescription()) > 130) : ?>

                            <div class="column_description_of_stage column_block_text_min_1800" title="<?= $gcp->getDescription() ?>">
                                <?= Html::a(mb_substr($gcp->getDescription(), 0, 130) . '...', ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php else : ?>

                            <div class="column_description_of_stage column_block_text_min_1800">
                                <?= Html::a($gcp->getDescription(), ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                    ['class' => 'link_for_description_stage']) ?>
                            </div>

                        <?php endif; ?>


                        <div class="column_stage_confirm">-</div>
                        <div class="column_stage_confirm">-</div>
                        <div class="column_stage_confirm">-</div>
                        <div class="column_stage_confirm">-</div>
                        <div class="column_stage_confirm">-</div>

                        <div class="column_stage_confirm">
                            <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                ['/confirm-gcp/create', 'id' => $gcp->getId()], ['title' => 'Создать подтверждение']) ?>
                        </div>

                        <div class="column_stage_confirm"></div>

                    </div>

                <?php endif; ?>

                <!--Строки MVP(продуктов)-->
                <?php /** @var $mvps Mvps[] */
                $mvps = !$project->getDeletedAt() ?
                    $gcp->mvps :
                    Mvps::find(false)
                        ->andWhere(['gcp_id' => $gcp->getId()])
                        ->all();

                foreach ($mvps as $mvp) : ?>

                    <!--Если у MVP существует подтверждение-->
                    <?php /** @var $confirmMvp ConfirmMvp */
                    $confirmMvp = !$project->getDeletedAt() ?
                        $mvp->confirm :
                        ConfirmMvp::find(false)
                            ->andWhere(['mvp_id' => $mvp->getId()])
                            ->one();

                    if($confirmMvp) : ?>

                        <div class="stage_data_string">

                            <div class="column_title_of_stage"><?= $mvp->propertyContainer->getProperty('title') ?></div>


                            <?php if (mb_strlen($mvp->getDescription()) > 100) : ?>

                                <div class="column_description_of_stage column_block_text_max_1800" title="<?= $mvp->getDescription() ?>">
                                    <?= Html::a(mb_substr($mvp->getDescription(), 0, 100) . '...', ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php else : ?>

                                <div class="column_description_of_stage column_block_text_max_1800">
                                    <?= Html::a($mvp->getDescription(), ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php endif; ?>


                            <?php if (mb_strlen($mvp->getDescription()) > 130) : ?>

                                <div class="column_description_of_stage column_block_text_min_1800" title="<?= $mvp->getDescription() ?>">
                                    <?= Html::a(mb_substr($mvp->getDescription(), 0, 130) . '...', ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php else : ?>

                                <div class="column_description_of_stage column_block_text_min_1800">
                                    <?= Html::a($mvp->getDescription(), ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php endif; ?>


                            <div class="column_stage_confirm"><?= $confirmMvp->getCountRespond() ?></div>

                            <div class="column_stage_confirm"><?= $confirmMvp->getCountPositive() ?></div>

                            <div class="column_stage_confirm"><?= $confirmMvp->isExistDesc() ? 1 : $confirmMvp->getCountConfirmMembers() ?></div>

                            <div class="column_stage_confirm"><?= ($confirmMvp->getCountDescInterviewsOfModel() - $confirmMvp->getCountConfirmMembers()) ?></div>

                            <div class="column_stage_confirm"><?= ($confirmMvp->getCountRespond() - $confirmMvp->getCountDescInterviewsOfModel()) ?></div>

                            <div class="column_stage_confirm">

                                <?php if ($mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                    <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                                        ['/confirm-mvp/view', 'id' => $confirmMvp->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                                <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                    <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                        ['/confirm-mvp/view', 'id' => $confirmMvp->getId()], ['title' => 'Продолжить подтверждение']) ?>

                                <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                    <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                                        ['/confirm-mvp/view', 'id' => $confirmMvp->getId()], ['title' => 'Посмотреть подтверждение']) ?>

                                <?php endif; ?>

                            </div>

                            <!--Бизнес модели-->
                            <?php /** @var $businessModel BusinessModel */
                            $businessModel = !$project->getDeletedAt() ?
                                $mvp->businessModel :
                                BusinessModel::find(false)
                                    ->andWhere(['mvp_id' => $mvp->getId()])
                                    ->one();

                            if (!$businessModel && $mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                <div class="column_stage_confirm">
                                    <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                        ['/business-model/index', 'id' => $confirmMvp->getId()], ['title'=> 'Создать бизнес-модель']) ?>
                                </div>

                            <?php elseif ($businessModel) : ?>

                                <div class="column_stage_confirm">
                                    <?= Html::a(Html::img('@web/images/icons/icon-pdf.png', ['style' => ['width' => '20px']]),
                                        ['/business-model/index', 'id' => $confirmMvp->getId()], ['title'=> 'Посмотреть бизнес-модель']) ?>
                                </div>

                            <?php else : ?>

                                <div class="column_stage_confirm"></div>

                            <?php endif; ?>

                        </div>

                    <!--Если у MVP не существует подтверждения-->
                    <?php else : ?>

                        <div class="stage_data_string">

                            <div class="column_title_of_stage"><?= $mvp->propertyContainer->getProperty('title') ?></div>


                            <?php if (mb_strlen($mvp->getDescription()) > 100) : ?>

                                <div class="column_description_of_stage column_block_text_max_1800" title="<?= $mvp->getDescription() ?>">
                                    <?= Html::a(mb_substr($mvp->getDescription(), 0, 100) . '...', ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php else : ?>

                                <div class="column_description_of_stage column_block_text_max_1800">
                                    <?= Html::a($mvp->getDescription(), ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php endif; ?>


                            <?php if (mb_strlen($mvp->getDescription()) > 130) : ?>

                                <div class="column_description_of_stage column_block_text_min_1800" title="<?= $mvp->getDescription() ?>">
                                    <?= Html::a(mb_substr($mvp->getDescription(), 0, 130) . '...', ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php else : ?>

                                <div class="column_description_of_stage column_block_text_min_1800">
                                    <?= Html::a($mvp->getDescription(), ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                        ['class' => 'link_for_description_stage']) ?>
                                </div>

                            <?php endif; ?>


                            <div class="column_stage_confirm">-</div>
                            <div class="column_stage_confirm">-</div>
                            <div class="column_stage_confirm">-</div>
                            <div class="column_stage_confirm">-</div>
                            <div class="column_stage_confirm">-</div>

                            <div class="column_stage_confirm">
                                <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                    ['/confirm-mvp/create', 'id' => $mvp->getId()], ['title' => 'Создать подтверждение']) ?>
                            </div>

                            <div class="column_stage_confirm"></div>

                        </div>

                    <?php endif; ?>

                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endforeach;?>

</div>
