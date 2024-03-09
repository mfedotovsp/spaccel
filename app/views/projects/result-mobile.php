<?php

use app\models\BusinessModel;
use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\EnableExpertise;
use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\Projects;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;

/**
 * @var Projects $project
 * @var Segments[] $segments
 * @var Problems $problem
 * @var Gcps $gcp
 * @var Mvps $mvp
 */

$this->title = 'Сводная таблица проекта'

?>

<div class="row">
    <div class="col-xs-12 header-title-mobile"><?= $this->title ?></div>
</div>

<div class="projectsResultMobile">

    <div class="block-project-name">
        <div>
            <?= $project->getProjectName() ?>
        </div>
    </div>

    <div class="line-project"></div>

    <!--SEGMENTS-->
    <?php if ($segments): ?>

        <?php foreach ($segments as $number_segment => $segment) : ?>

            <div class="block-segment-margin">

                <?php
                /** @var $confirmSegment ConfirmSegment */
                $confirmSegment = ConfirmSegment::find(false)
                    ->andWhere(['segment_id' => $segment->getId()])
                    ->one();

                if ($segment->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                    <div class="block-segment">

                        <?php if ($segment->getEnableExpertise() === EnableExpertise::ON) : ?>
                            <?= Html::a('ГЦС ' . ($number_segment + 1), [
                                '/confirm-segment/create', 'id' => $segment->getId()],[
                                'class' => 'link-confirm-result-mobile bg-grey'
                            ]) ?>
                        <?php else: ?>
                            <?= Html::a('ГЦС ' . ($number_segment + 1), [
                                '/segments/index', 'id' => $segment->getProjectId()],[
                                'class' => 'link-confirm-result-mobile bg-grey'
                            ]) ?>
                        <?php endif; ?>

                        <div class="content">
                            <div class="truncate-text">
                                <?= $segment->getName() ?>
                            </div>

                            <div class="line-content"></div>

                            <div class="content-bottom-incomplete">
                                <div class="date-created_at">
                                    <?= date('d.m.y', $segment->getCreatedAt()) ?>
                                </div>

                                <div class="block-button-confirm">
                                    <?php if ($segment->getEnableExpertise() === EnableExpertise::ON) : ?>
                                        <?= Html::a('Подтвердить', ['/confirm-segment/create', 'id' => $segment->getId()], [
                                            'class' => 'button-confirm-result-mobile'
                                        ]) ?>
                                    <?php else: ?>
                                        <?= Html::a('Подтвердить', ['#'], [
                                            'class' => 'button-confirm-result-mobile disabled',
                                            'onclick' => 'return false;'
                                        ]) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($segment->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                    <div class="block-segment block-segment-<?= $number_segment ?>">

                        <?= Html::a('ГЦС ' . ($number_segment + 1), [
                            '/confirm-segment/view', 'id' => $confirmSegment->getId()],[
                            'class' => 'link-confirm-result-mobile bg-green'
                        ]) ?>

                        <?php
                        /** @var $problems Problems[] */
                        $problems = Problems::find(false)
                            ->andWhere(['segment_id' => $segment->getId()])
                            ->all();

                        if ($problems) : ?>
                            <div class="line-segment line-segment-<?= $number_segment ?>"></div>
                        <?php endif; ?>

                        <div class="content">
                            <div class="truncate-text">
                                <?= $segment->getName() ?>
                            </div>

                            <div class="line-content"></div>

                            <div class="content-bottom-complete">
                                <div class="date-created_at">
                                    <?= date('d.m.y', $segment->getCreatedAt()) ?>
                                </div>
                                <div class="date-confirm">
                                    <?= date('d.m.y', $segment->getTimeConfirm()) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($segment->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                    <div class="block-segment">

                        <?= Html::a('ГЦС ' . ($number_segment + 1), [
                            '/confirm-segment/view', 'id' => $confirmSegment->getId()],[
                            'class' => 'link-confirm-result-mobile bg-red'
                        ]) ?>

                        <div class="content">
                            <div class="truncate-text">
                                <?= $segment->getName() ?>
                            </div>

                            <div class="line-content"></div>

                            <div class="content-bottom-not_completed">
                                <div class="date-created_at">
                                    <?= date('d.m.y', $segment->getCreatedAt()) ?>
                                </div>
                                <div class="date-confirm color-red">
                                    <?= date('d.m.y', $segment->getTimeConfirm()) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <!--PROBLEMS-->
                <?php
                /** @var $problems Problems[] */
                $problems = Problems::find(false)
                    ->andWhere(['segment_id' => $segment->getId()])
                    ->all();

                if ($problems) : ?>

                    <?php foreach ($problems as $number_problem => $problem) : ?>

                        <div class="block-problem-margin">

                            <?php
                            /** @var $confirmProblem ConfirmProblem */
                            $confirmProblem = ConfirmProblem::find(false)
                                ->andWhere(['problem_id' => $problem->getId()])
                                ->one();

                            if ($problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                <div class="block-problem segment-number-<?= $number_segment ?>">

                                    <?php if ($problem->getEnableExpertise() === EnableExpertise::ON) : ?>
                                        <?= Html::a('ГПС ' . ($number_segment + 1) . '.' . ($number_problem + 1), [
                                            '/confirm-problem/create', 'id' => $problem->getId()],[
                                            'class' => 'link-confirm-result-mobile bg-grey'
                                        ]) ?>
                                    <?php else: ?>
                                        <?= Html::a('ГПС ' . ($number_segment + 1) . '.' . ($number_problem + 1), [
                                            '/problems/index', 'id' => $problem->getBasicConfirmId()],[
                                            'class' => 'link-confirm-result-mobile bg-grey'
                                        ]) ?>
                                    <?php endif; ?>

                                    <div class="content">
                                        <div class="truncate-text">
                                            <?= $problem->getDescription() ?>
                                        </div>

                                        <div class="line-content"></div>

                                        <div class="content-bottom-incomplete">
                                            <div class="date-created_at">
                                                <?= date('d.m.y', $problem->getCreatedAt()) ?>
                                            </div>

                                            <div class="block-button-confirm">
                                                <?php if ($problem->getEnableExpertise() === EnableExpertise::ON) : ?>
                                                    <?= Html::a('Подтвердить', ['/confirm-problem/create', 'id' => $problem->getId()], [
                                                        'class' => 'button-confirm-result-mobile'
                                                    ]) ?>
                                                <?php else: ?>
                                                    <?= Html::a('Подтвердить', ['#'], [
                                                        'class' => 'button-confirm-result-mobile disabled',
                                                        'onclick' => 'return false;'
                                                    ]) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                <div class="block-problem segment-number-<?= $number_segment ?> block-problem-<?= $number_segment . '-' . $number_problem ?>">

                                    <?= Html::a('ГПС ' . ($number_segment + 1) . '.' . ($number_problem + 1), [
                                        '/confirm-problem/view', 'id' => $confirmProblem->getId()],[
                                        'class' => 'link-confirm-result-mobile bg-green'
                                    ]) ?>

                                    <?php
                                    /** @var $gcps Gcps[] */
                                    $gcps = Gcps::find(false)
                                        ->andWhere(['problem_id' => $problem->getId()])
                                        ->all();

                                    if ($gcps) : ?>
                                        <div class="line-problem line-problem-<?= $number_segment . '-' . $number_problem ?>"></div>
                                    <?php endif; ?>

                                    <div class="content">
                                        <div class="truncate-text">
                                            <?= $problem->getDescription() ?>
                                        </div>

                                        <div class="line-content"></div>

                                        <div class="content-bottom-complete">
                                            <div class="date-created_at">
                                                <?= date('d.m.y', $problem->getCreatedAt()) ?>
                                            </div>
                                            <div class="date-confirm">
                                                <?= date('d.m.y', $problem->getTimeConfirm()) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                <div class="block-problem segment-number-<?= $number_segment ?>">

                                    <?= Html::a('ГПС ' . ($number_segment + 1) . '.' . ($number_problem + 1), [
                                        '/confirm-problem/view', 'id' => $confirmProblem->getId()],[
                                        'class' => 'link-confirm-result-mobile bg-red'
                                    ]) ?>

                                    <div class="content">
                                        <div class="truncate-text">
                                            <?= $problem->getDescription() ?>
                                        </div>

                                        <div class="line-content"></div>

                                        <div class="content-bottom-not_completed">
                                            <div class="date-created_at">
                                                <?= date('d.m.y', $problem->getCreatedAt()) ?>
                                            </div>
                                            <div class="date-confirm color-red">
                                                <?= date('d.m.y', $problem->getTimeConfirm()) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endif; ?>

                            <!--GCPS-->
                            <?php
                            /** @var $gcps Gcps[] */
                            $gcps = Gcps::find(false)
                                ->andWhere(['problem_id' => $problem->getId()])
                                ->all();
                            if ($gcps) : ?>

                                <?php foreach ($gcps as $number_gcp => $gcp) : ?>

                                    <div class="block-gcp-margin">

                                        <?php
                                        /** @var $confirmGcp ConfirmGcp */
                                        $confirmGcp = ConfirmGcp::find(false)
                                            ->andWhere(['gcp_id' => $gcp->getId()])
                                            ->one();

                                        if ($gcp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                            <div class="block-gcp problem-number-<?= $number_segment . '-' . $number_problem ?>">

                                                <?php if ($gcp->getEnableExpertise() === EnableExpertise::ON) : ?>
                                                    <?= Html::a('ГЦП ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1), [
                                                        '/confirm-gcp/create', 'id' => $gcp->getId()],[
                                                        'class' => 'link-confirm-result-mobile bg-grey'
                                                    ]) ?>
                                                <?php else: ?>
                                                    <?= Html::a('ГЦП ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1), [
                                                        '/gcps/index', 'id' => $gcp->getBasicConfirmId()],[
                                                        'class' => 'link-confirm-result-mobile bg-grey'
                                                    ]) ?>
                                                <?php endif; ?>

                                                <div class="content">
                                                    <div class="truncate-text">
                                                        <?= $gcp->getDescription() ?>
                                                    </div>

                                                    <div class="line-content"></div>

                                                    <div class="content-bottom-incomplete">
                                                        <div class="date-created_at">
                                                            <?= date('d.m.y', $gcp->getCreatedAt()) ?>
                                                        </div>

                                                        <div class="block-button-confirm">
                                                            <?php if ($gcp->getEnableExpertise() === EnableExpertise::ON) : ?>
                                                                <?= Html::a('Подтвердить', ['/confirm-gcp/create', 'id' => $gcp->getId()], [
                                                                    'class' => 'button-confirm-result-mobile'
                                                                ]) ?>
                                                            <?php else: ?>
                                                                <?= Html::a('Подтвердить', ['#'], [
                                                                    'class' => 'button-confirm-result-mobile disabled',
                                                                    'onclick' => 'return false;'
                                                                ]) ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php elseif ($gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                            <div class="block-gcp problem-number-<?= $number_segment . '-' . $number_problem ?> block-gcp-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp ?>">

                                                <?= Html::a('ГЦП ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1), [
                                                    '/confirm-gcp/view', 'id' => $confirmGcp->getId()],[
                                                    'class' => 'link-confirm-result-mobile bg-green'
                                                ]) ?>

                                                <?php
                                                /** @var $mvps Mvps[] */
                                                $mvps = Mvps::find(false)
                                                    ->andWhere(['gcp_id' => $gcp->getId()])
                                                    ->all();

                                                if ($mvps) : ?>
                                                    <div class="line-gcp line-gcp-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp ?>"></div>
                                                <?php endif; ?>

                                                <div class="content">
                                                    <div class="truncate-text">
                                                        <?= $gcp->getDescription() ?>
                                                    </div>

                                                    <div class="line-content"></div>

                                                    <div class="content-bottom-complete">
                                                        <div class="date-created_at">
                                                            <?= date('d.m.y', $gcp->getCreatedAt()) ?>
                                                        </div>
                                                        <div class="date-confirm">
                                                            <?= date('d.m.y', $gcp->getTimeConfirm()) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php elseif ($gcp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                            <div class="block-gcp problem-number-<?= $number_segment . '-' . $number_problem ?>">

                                                <?= Html::a('ГЦП ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1), [
                                                    '/confirm-gcp/view', 'id' => $confirmGcp->getId()],[
                                                    'class' => 'link-confirm-result-mobile bg-red'
                                                ]) ?>

                                                <div class="content">
                                                    <div class="truncate-text">
                                                        <?= $gcp->getDescription() ?>
                                                    </div>

                                                    <div class="line-content"></div>

                                                    <div class="content-bottom-not_completed">
                                                        <div class="date-created_at">
                                                            <?= date('d.m.y', $gcp->getCreatedAt()) ?>
                                                        </div>
                                                        <div class="date-confirm color-red">
                                                            <?= date('d.m.y', $gcp->getTimeConfirm()) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php endif; ?>

                                        <!--MVPS-->
                                        <?php
                                        /** @var $mvps Mvps[] */
                                        $mvps = Mvps::find(false)
                                            ->andWhere(['gcp_id' => $gcp->getId()])
                                            ->all();

                                        if ($mvps) : ?>

                                            <?php foreach ($mvps as $number_mvp => $mvp) : ?>

                                                <div class="block-mvp-margin">

                                                    <?php
                                                    /** @var $confirmMvp ConfirmMvp */
                                                    $confirmMvp = ConfirmMvp::find(false)
                                                        ->andWhere(['mvp_id' => $mvp->getId()])
                                                        ->one();

                                                    if ($mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                                        <div class="block-mvp gcp-number-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp ?>">

                                                            <?php if ($mvp->getEnableExpertise() === EnableExpertise::ON) : ?>
                                                                <?= Html::a('MVP ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1) . '.' . ($number_mvp + 1), [
                                                                    '/confirm-mvp/create', 'id' => $mvp->getId()],[
                                                                    'class' => 'link-confirm-result-mobile bg-grey'
                                                                ]) ?>
                                                            <?php else: ?>
                                                                <?= Html::a('MVP ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1) . '.' . ($number_mvp + 1), [
                                                                    '/mvps/index', 'id' => $mvp->getBasicConfirmId()],[
                                                                    'class' => 'link-confirm-result-mobile bg-grey'
                                                                ]) ?>
                                                            <?php endif; ?>

                                                            <div class="content">
                                                                <div class="truncate-text">
                                                                    <?= $mvp->getDescription() ?>
                                                                </div>

                                                                <div class="line-content"></div>

                                                                <div class="content-bottom-incomplete">
                                                                    <div class="date-created_at">
                                                                        <?= date('d.m.y', $mvp->getCreatedAt()) ?>
                                                                    </div>

                                                                    <div class="block-button-confirm">
                                                                        <?php if ($mvp->getEnableExpertise() === EnableExpertise::ON) : ?>
                                                                            <?= Html::a('Подтвердить', ['/confirm-mvp/create', 'id' => $mvp->getId()], [
                                                                                'class' => 'button-confirm-result-mobile'
                                                                            ]) ?>
                                                                        <?php else: ?>
                                                                            <?= Html::a('Подтвердить', ['#'], [
                                                                                'class' => 'button-confirm-result-mobile disabled',
                                                                                'onclick' => 'return false;'
                                                                            ]) ?>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                                        <div class="block-mvp gcp-number-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp ?> block-mvp-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp . '-' . $number_mvp ?>">

                                                            <?= Html::a('MVP ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1) . '.' . ($number_mvp + 1), [
                                                                '/confirm-mvp/view', 'id' => $confirmMvp->getId()],[
                                                                'class' => 'link-confirm-result-mobile bg-green'
                                                            ]) ?>

                                                            <?php
                                                            /** @var $businessModel BusinessModel */
                                                            $businessModel = BusinessModel::find(false)
                                                                ->andWhere(['mvp_id' => $mvp->getId()])
                                                                ->one();

                                                            if ($businessModel) : ?>
                                                                <div class="line-mvp line-mvp-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp . '-' . $number_mvp ?>"></div>
                                                            <?php endif; ?>

                                                            <div class="content">
                                                                <div class="truncate-text">
                                                                    <?= $mvp->getDescription() ?>
                                                                </div>

                                                                <div class="line-content"></div>

                                                                <div class="content-bottom-complete">
                                                                    <div class="date-created_at">
                                                                        <?= date('d.m.y', $mvp->getCreatedAt()) ?>
                                                                    </div>
                                                                    <div class="date-confirm">
                                                                        <?= date('d.m.y', $mvp->getTimeConfirm()) ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                                        <div class="block-mvp gcp-number-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp ?>">

                                                            <?= Html::a('MVP ' . ($number_segment + 1) . '.' . ($number_problem + 1) . '.' . ($number_gcp + 1) . '.' . ($number_mvp + 1), [
                                                                '/confirm-mvp/view', 'id' => $confirmMvp->getId()],[
                                                                'class' => 'link-confirm-result-mobile bg-red'
                                                            ]) ?>

                                                            <div class="content">
                                                                <div class="truncate-text">
                                                                    <?= $mvp->getDescription() ?>
                                                                </div>

                                                                <div class="line-content"></div>

                                                                <div class="content-bottom-not_completed">
                                                                    <div class="date-created_at">
                                                                        <?= date('d.m.y', $mvp->getCreatedAt()) ?>
                                                                    </div>
                                                                    <div class="date-confirm color-red">
                                                                        <?= date('d.m.y', $mvp->getTimeConfirm()) ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php endif; ?>

                                                    <?php
                                                    /** @var $businessModel BusinessModel */
                                                    $businessModel = BusinessModel::find(false)
                                                        ->andWhere(['mvp_id' => $mvp->getId()])
                                                        ->one();

                                                    if ($businessModel) : ?>

                                                        <div class="block-business-model-margin">
                                                            <div class="block-business-model mvp-number-<?= $number_segment . '-' . $number_problem . '-' . $number_gcp . '-' . $number_mvp ?>">

                                                                <?= Html::a('Бизнес-модель', [
                                                                    '/business-model/index', 'id' => $businessModel->getBasicConfirmId()],[
                                                                    'class' => 'button-business-model-result-mobile'
                                                                ]) ?>

                                                                <div class="line-content"></div>

                                                                <div class="" style="border-width: 0 1px 1px 1px; border-style: solid; border-color: #4F4F4F; border-radius: 0 0 4px 4px; width: inherit; min-height: 30px; display: flex; align-items: center; justify-content: center;">
                                                                    <?= Html::a(Html::img('/images/icons/icon_success_pdf.png', ['height' => '20px']),[
                                                                        '/business-model/mpdf-business-model', 'id' => $businessModel->getId()], [
                                                                        'target' => '_blank',
                                                                    ]) ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php endif; ?>

                                                </div>

                                            <?php endforeach; ?>

                                        <?php endif; ?>

                                    </div>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<div class="row">
    <div class="col-md-12" style="display:flex;justify-content: center;">
        <?= Html::button('Закрыть', [
            'onclick' => 'javascript:history.back()',
            'class' => 'btn button-close-result-mobile'
        ]) ?>
    </div>
</div>


<?php $this->registerJsFile('@web/js/projects_result_mobile.js'); ?>