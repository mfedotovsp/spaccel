<?php

use app\models\BusinessModel;
use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;

/**
 * @var Segments[] $segments
 */

$this->title = 'Протокол проекта';

?>

<div class="row">
    <div class="col-xs-12 header-title-mobile"><?= $this->title ?></div>
</div>

<?php if ($segments): ?>

    <div class="row container-fluid report-mobile">

        <?php foreach ($segments as $segment): ?>

            <div class="col-xs-12 one-report-mobile">

                <?php
                /** @var $confirmSegment ConfirmSegment */
                $confirmSegment = ConfirmSegment::find(false)
                    ->andWhere(['segment_id' => $segment->getId()])
                    ->one();

                if($confirmSegment) : ?>

                    <?php if ($segment->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                        <div class="row report-mobile-header-segment report-mobile-bg-green">
                            <div class="col-xs-8">
                                <?= Html::a($segment->propertyContainer->getProperty('title')
                                    . ' - ' . $segment->getName(), ['/confirm-segment/view', 'id' => $confirmSegment->getId()],
                                    ['class' => 'link-stage-report-mobile white']) ?>
                            </div>
                            <div class="col-xs-4">
                                Создан <?= date('d.m.Y', $segment->getCreatedAt()) ?>
                            </div>
                        </div>

                    <?php elseif ($segment->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                        <div class="row report-mobile-header-segment report-mobile-bg-grey">
                            <div class="col-xs-8">
                                <?= Html::a($segment->propertyContainer->getProperty('title')
                                    . ' - ' . $segment->getName(), ['/confirm-segment/view', 'id' => $confirmSegment->getId()],
                                    ['class' => 'link-stage-report-mobile white']) ?>
                            </div>
                            <div class="col-xs-4">
                                Создан <?= date('d.m.Y', $segment->getCreatedAt()) ?>
                            </div>
                        </div>

                    <?php elseif ($segment->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                        <div class="row report-mobile-header-segment report-mobile-bg-red">
                            <div class="col-xs-8">
                                <?= Html::a($segment->propertyContainer->getProperty('title')
                                    . ' - ' . $segment->getName(), ['/confirm-segment/view', 'id' => $confirmSegment->getId()],
                                    ['class' => 'link-stage-report-mobile white']) ?>
                            </div>
                            <div class="col-xs-4">
                                Создан <?= date('d.m.Y', $segment->getCreatedAt()) ?>
                            </div>
                        </div>

                    <?php endif; ?>

                    <div class="row">
                        <div class="col-xs-12 report-mobile-header-columns">
                            <div>План</div>
                            <div>Надо</div>
                            <div>Положит.</div>
                            <div>Отрицат.</div>
                            <div>Не опрошены</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 report-mobile-value-columns">
                            <div><?= $confirmSegment->getCountRespond() ?></div>
                            <div><?= $confirmSegment->getCountPositive() ?></div>
                            <div><?= $confirmSegment->isExistDesc() ? 1 : $confirmSegment->getCountConfirmMembers() ?></div>
                            <div><?= ($confirmSegment->getCountDescInterviewsOfModel() - $confirmSegment->getCountConfirmMembers()) ?></div>
                            <div><?= ($confirmSegment->getCountRespond() - $confirmSegment->getCountDescInterviewsOfModel()) ?></div>
                        </div>
                    </div>

                <!--Если у сегмента не существует подтверждения-->
                <?php else : ?>

                    <div class="row report-mobile-header-segment report-mobile-bg-grey">
                        <div class="col-xs-8">
                            <?= Html::a($segment->propertyContainer->getProperty('title')
                                . ' - ' . $segment->getName(), ['/segments/index', 'id' => $segment->getProjectId()],
                                ['class' => 'link-stage-report-mobile white']) ?>
                        </div>
                        <div class="col-xs-4">
                            Создан <?= date('d.m.Y', $segment->getCreatedAt()) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 report-mobile-header-columns">
                            <div>План</div>
                            <div>Надо</div>
                            <div>Положит.</div>
                            <div>Отрицат.</div>
                            <div>Не опрошены</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 report-mobile-value-columns">
                            <div>-</div>
                            <div>-</div>
                            <div>-</div>
                            <div>-</div>
                            <div>-</div>
                        </div>
                    </div>

                <?php endif; ?>

                <!--Строки проблем сегментов-->
                <?php
                /** @var $problems Problems[] */
                $problems = Problems::find(false)
                    ->andWhere(['segment_id' => $segment->getId()])
                    ->all();

                foreach ($problems as $problem) : ?>

                    <!--Если у проблемы существует подтверждение-->
                    <?php
                    /** @var $confirmProblem ConfirmProblem */
                    $confirmProblem = ConfirmProblem::find(false)
                        ->andWhere(['problem_id' => $problem->getId()])
                        ->one();

                    if($confirmProblem) : ?>

                        <?php if ($problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                            <div class="row report-mobile-header-hypothesis report-mobile-bg-green">
                                <div class="col-xs-12">
                                    <?= Html::a($problem->propertyContainer->getProperty('title')
                                        . ' - ' . $problem->getDescription(), ['/confirm-problem/view', 'id' => $confirmProblem->getId()],
                                        ['class' => 'link-stage-report-mobile white']) ?>
                                </div>
                            </div>

                        <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                            <div class="row report-mobile-header-hypothesis report-mobile-bg-grey">
                                <div class="col-xs-12">
                                    <?= Html::a($problem->propertyContainer->getProperty('title')
                                        . ' - ' . $problem->getDescription(), ['/confirm-problem/view', 'id' => $confirmProblem->getId()],
                                        ['class' => 'link-stage-report-mobile white']) ?>
                                </div>
                            </div>

                        <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                            <div class="row report-mobile-header-hypothesis report-mobile-bg-red">
                                <div class="col-xs-12">
                                    <?= Html::a($problem->propertyContainer->getProperty('title')
                                        . ' - ' . $problem->getDescription(), ['/confirm-problem/view', 'id' => $confirmProblem->getId()],
                                        ['class' => 'link-stage-report-mobile white']) ?>
                                </div>
                            </div>

                        <?php endif; ?>

                        <div class="row">
                            <div class="col-xs-12 report-mobile-header-columns">
                                <div>План</div>
                                <div>Надо</div>
                                <div>Положит.</div>
                                <div>Отрицат.</div>
                                <div>Не опрошены</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 report-mobile-value-columns">
                                <div><?= $confirmProblem->getCountRespond() ?></div>
                                <div><?= $confirmProblem->getCountPositive() ?></div>
                                <div><?= $confirmProblem->isExistDesc() ? 1 : $confirmProblem->getCountConfirmMembers() ?></div>
                                <div><?= ($confirmProblem->getCountDescInterviewsOfModel() - $confirmProblem->getCountConfirmMembers()) ?></div>
                                <div><?= ($confirmProblem->getCountRespond() - $confirmProblem->getCountDescInterviewsOfModel()) ?></div>
                            </div>
                        </div>

                    <!--Если у проблемы не существует подтверждение-->
                    <?php else: ?>

                        <div class="row report-mobile-header-hypothesis report-mobile-bg-grey">
                            <div class="col-xs-12">
                                <?= Html::a($problem->propertyContainer->getProperty('title')
                                    . ' - ' . $problem->getDescription(), ['/problems/index', 'id' => $problem->getBasicConfirmId()],
                                    ['class' => 'link-stage-report-mobile white']) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 report-mobile-header-columns">
                                <div>План</div>
                                <div>Надо</div>
                                <div>Положит.</div>
                                <div>Отрицат.</div>
                                <div>Не опрошены</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 report-mobile-value-columns">
                                <div>-</div>
                                <div>-</div>
                                <div>-</div>
                                <div>-</div>
                                <div>-</div>
                            </div>
                        </div>

                    <?php endif; ?>

                    <!--Строки ценностных предложений-->
                    <?php
                    /** @var $gcps Gcps[] */
                    $gcps = Gcps::find(false)
                        ->andWhere(['problem_id' => $problem->getId()])
                        ->all();

                    foreach ($gcps as $gcp) : ?>

                        <!--Если у ЦП существует подтверждение-->
                        <?php
                        /** @var $confirmGcp ConfirmGcp */
                        $confirmGcp = ConfirmGcp::find(false)
                            ->andWhere(['gcp_id' => $gcp->getId()])
                            ->one();

                        if($confirmGcp) : ?>

                            <?php if ($gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                <div class="row report-mobile-header-hypothesis report-mobile-bg-green">
                                    <div class="col-xs-12">
                                        <?= Html::a($gcp->propertyContainer->getProperty('title')
                                            . ' - ' . $gcp->getDescription(), ['/confirm-gcp/view', 'id' => $confirmGcp->getId()],
                                            ['class' => 'link-stage-report-mobile white']) ?>
                                    </div>
                                </div>

                            <?php elseif ($gcp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                <div class="row report-mobile-header-hypothesis report-mobile-bg-grey">
                                    <div class="col-xs-12">
                                        <?= Html::a($gcp->propertyContainer->getProperty('title')
                                            . ' - ' . $gcp->getDescription(), ['/confirm-gcp/view', 'id' => $confirmGcp->getId()],
                                            ['class' => 'link-stage-report-mobile white']) ?>
                                    </div>
                                </div>

                            <?php elseif ($gcp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                <div class="row report-mobile-header-hypothesis report-mobile-bg-red">
                                    <div class="col-xs-12">
                                        <?= Html::a($gcp->propertyContainer->getProperty('title')
                                            . ' - ' . $gcp->getDescription(), ['/confirm-gcp/view', 'id' => $confirmGcp->getId()],
                                            ['class' => 'link-stage-report-mobile white']) ?>
                                    </div>
                                </div>

                            <?php endif; ?>

                            <div class="row">
                                <div class="col-xs-12 report-mobile-header-columns">
                                    <div>План</div>
                                    <div>Надо</div>
                                    <div>Положит.</div>
                                    <div>Отрицат.</div>
                                    <div>Не опрошены</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 report-mobile-value-columns">
                                    <div><?= $confirmGcp->getCountRespond() ?></div>
                                    <div><?= $confirmGcp->getCountPositive() ?></div>
                                    <div><?= $confirmGcp->isExistDesc() ? 1 : $confirmGcp->getCountConfirmMembers() ?></div>
                                    <div><?= ($confirmGcp->getCountDescInterviewsOfModel() - $confirmGcp->getCountConfirmMembers()) ?></div>
                                    <div><?= ($confirmGcp->getCountRespond() - $confirmGcp->getCountDescInterviewsOfModel()) ?></div>
                                </div>
                            </div>

                            <!--Если у ЦП не существует подтверждение-->
                        <?php else: ?>

                            <div class="row report-mobile-header-hypothesis report-mobile-bg-grey">
                                <div class="col-xs-12">
                                    <?= Html::a($gcp->propertyContainer->getProperty('title')
                                        . ' - ' . $gcp->getDescription(), ['/gcps/index', 'id' => $gcp->getBasicConfirmId()],
                                        ['class' => 'link-stage-report-mobile white']) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 report-mobile-header-columns">
                                    <div>План</div>
                                    <div>Надо</div>
                                    <div>Положит.</div>
                                    <div>Отрицат.</div>
                                    <div>Не опрошены</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 report-mobile-value-columns">
                                    <div>-</div>
                                    <div>-</div>
                                    <div>-</div>
                                    <div>-</div>
                                    <div>-</div>
                                </div>
                            </div>

                        <?php endif; ?>

                        <!--Строки mvps-->
                        <?php
                        /** @var $mvps Mvps[] */
                        $mvps = Mvps::find(false)
                            ->andWhere(['gcp_id' => $gcp->getId()])
                            ->all();

                        foreach ($mvps as $mvp) : ?>

                            <!--Если у MVP существует подтверждение-->
                            <?php
                            /** @var $confirmMvp ConfirmMvp */
                            $confirmMvp = ConfirmMvp::find(false)
                                ->andWhere(['mvp_id' => $mvp->getId()])
                                ->one();

                            if($confirmMvp) : ?>

                                <?php if ($mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                    <?php
                                    $businessModel = BusinessModel::find(false)
                                        ->andWhere(['mvp_id' => $mvp->getId()])
                                        ->one();

                                    if ($businessModel): ?>
                                        <div class="row report-mobile-header-mvp">
                                            <div class="col-xs-9 report-mobile-bg-green">
                                                <?= Html::a($mvp->propertyContainer->getProperty('title')
                                                    . ' - ' . $mvp->getDescription(), ['/confirm-mvp/view', 'id' => $confirmMvp->getId()],
                                                    ['class' => 'link-stage-report-mobile white']) ?>
                                            </div>
                                            <div class="col-xs-3 report-mobile-bg-green">
                                                <?= Html::a('Бизнес-модель', ['/business-model/index', 'id' => $confirmMvp->getId()],
                                                    ['class' => 'link-stage-report-mobile white']) ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="row report-mobile-header-mvp">
                                            <div class="col-xs-9 report-mobile-bg-green">
                                                <?= Html::a($mvp->propertyContainer->getProperty('title')
                                                    . ' - ' . $mvp->getDescription(), ['/confirm-mvp/view', 'id' => $confirmMvp->getId()],
                                                    ['class' => 'link-stage-report-mobile white']) ?>
                                            </div>
                                            <div class="col-xs-3 report-mobile-bg-grey">
                                                <?= Html::a('Бизнес-модель', ['/business-model/index', 'id' => $confirmMvp->getId()],
                                                    ['class' => 'link-stage-report-mobile white']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                    <div class="row report-mobile-header-hypothesis report-mobile-bg-grey">
                                        <div class="col-xs-12">
                                            <?= Html::a($mvp->propertyContainer->getProperty('title')
                                                . ' - ' . $mvp->getDescription(), ['/confirm-mvp/view', 'id' => $confirmMvp->getId()],
                                                ['class' => 'link-stage-report-mobile white']) ?>
                                        </div>
                                    </div>

                                <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                    <div class="row report-mobile-header-hypothesis report-mobile-bg-red">
                                        <div class="col-xs-12">
                                            <?= Html::a($mvp->propertyContainer->getProperty('title')
                                                . ' - ' . $mvp->getDescription(), ['/confirm-mvp/view', 'id' => $confirmMvp->getId()],
                                                ['class' => 'link-stage-report-mobile white']) ?>
                                        </div>
                                    </div>

                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-xs-12 report-mobile-header-columns">
                                        <div>План</div>
                                        <div>Надо</div>
                                        <div>Положит.</div>
                                        <div>Отрицат.</div>
                                        <div>Не опрошены</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 report-mobile-value-columns">
                                        <div><?= $confirmMvp->getCountRespond() ?></div>
                                        <div><?= $confirmMvp->getCountPositive() ?></div>
                                        <div><?= $confirmMvp->isExistDesc() ? 1 : $confirmMvp->getCountConfirmMembers() ?></div>
                                        <div><?= ($confirmMvp->getCountDescInterviewsOfModel() - $confirmMvp->getCountConfirmMembers()) ?></div>
                                        <div><?= ($confirmMvp->getCountRespond() - $confirmMvp->getCountDescInterviewsOfModel()) ?></div>
                                    </div>
                                </div>

                            <!--Если у MVP не существует подтверждение-->
                            <?php else: ?>

                                <div class="row report-mobile-header-hypothesis report-mobile-bg-grey">
                                    <div class="col-xs-12">
                                        <?= Html::a($mvp->propertyContainer->getProperty('title')
                                            . ' - ' . $mvp->getDescription(), ['/mvps/index', 'id' => $mvp->getBasicConfirmId()],
                                            ['class' => 'link-stage-report-mobile white']) ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 report-mobile-header-columns">
                                        <div>План</div>
                                        <div>Надо</div>
                                        <div>Положит.</div>
                                        <div>Отрицат.</div>
                                        <div>Не опрошены</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 report-mobile-value-columns">
                                        <div>-</div>
                                        <div>-</div>
                                        <div>-</div>
                                        <div>-</div>
                                        <div>-</div>
                                    </div>
                                </div>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>
    </div>

<?php else: ?>
    <h3 class="text-center">Пока нет сегментов...</h3>
<?php endif; ?>

<div class="row">
    <div class="col-md-12" style="display:flex;justify-content: center;">
        <?= Html::button('Закрыть', [
            'onclick' => 'javascript:history.back()',
            'class' => 'btn button-close-result-mobile'
        ]) ?>
    </div>
</div>
