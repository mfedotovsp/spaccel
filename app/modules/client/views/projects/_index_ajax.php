<?php

use app\models\Projects;
use app\models\StatusConfirmHypothesis;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/**
 * @var Projects[] $projects
 * @var Pagination $pages
 */

?>

<?php foreach ($projects as $project) : ?>

    <div class="container-fluid">

        <div class="row block_for_projectname_and_username">
            <div class="col-md-8">
                <?= Html::a('<span>Проект:</span><span>' . $project->getProjectName() . '</span>',
                    ['/projects/index', 'id' => $project->getUserId()]) ?>
            </div>
            <div class="col-md-4">
                <div class="pull-right">
                    <?= Html::a('<span>Автор:</span><span>' . $project->user->getUsername() . '</span>',
                        ['/profile/index', 'id' => $project->getUserId()]) ?>
                </div>
            </div>
        </div>

        <div class="row ratingOfProject">

            <div class="base_line">
                <!--Наличие положительного подтверждения у сегментов-->
                <?php if ($project->segments) : ?>

                    <?php $count_exist_confirm_segment = 0; ?>
                    <?php foreach ($project->segments as $segment) : ?>
                        <?php if ($segment->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                            $count_exist_confirm_segment++;
                        } ?>
                    <?php endforeach; ?>

                    <?php if ($count_exist_confirm_segment > 0) : ?>
                        <div class="segments_line_success"></div>
                    <?php else : ?>
                        <div class="segments_line_default"></div>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="segments_line_default"></div>
                <?php endif; ?>

                <!--Наличие положительного подтверждения у проблем сегментов-->
                <?php if ($project->problems) : ?>

                    <?php $count_exist_confirm_problem = 0; ?>
                    <?php foreach ($project->problems as $problem) : ?>
                        <?php if ($problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                            $count_exist_confirm_problem++;
                        } ?>
                    <?php endforeach; ?>

                    <?php if ($count_exist_confirm_problem > 0) : ?>
                        <div class="rating_line_success"></div>
                    <?php else : ?>
                        <div class="rating_line_default"></div>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="rating_line_default"></div>
                <?php endif; ?>

                <!--Наличие положительного подтверждения у ценностных предложений-->
                <?php if ($project->gcps) : ?>

                    <?php $count_exist_confirm_gcp = 0; ?>
                    <?php foreach ($project->gcps as $gcp) : ?>
                        <?php if ($gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                            $count_exist_confirm_gcp++;
                        } ?>
                    <?php endforeach; ?>

                    <?php if ($count_exist_confirm_gcp > 0) : ?>
                        <div class="rating_line_success"></div>
                    <?php else : ?>
                        <div class="rating_line_default"></div>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="rating_line_default"></div>
                <?php endif; ?>

                <!--Наличие положительного подтверждения у mvps-->
                <?php if ($project->mvps) : ?>

                    <?php $count_exist_confirm_mvp = 0; ?>
                    <?php foreach ($project->mvps as $mvp) : ?>
                        <?php if ($mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                            $count_exist_confirm_mvp++;
                        } ?>
                    <?php endforeach; ?>

                    <?php if ($count_exist_confirm_mvp > 0) : ?>
                        <div class="rating_line_success"></div>
                    <?php else : ?>
                        <div class="rating_line_default"></div>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="rating_line_default"></div>
                <?php endif; ?>

            </div>

            <div class="business_models_line">
                <!--Наличие бизнес-моделей-->
                <?php if ($project->businessModels) : ?>
                    <div class="business_models_line_success"></div>
                <?php else : ?>
                    <div class="business_models_line_default"></div>
                <?php endif; ?>
            </div>

        </div>
    </div>


    <div class="containerDataOfTableResultProject" style="border-bottom: 1px solid #B4B4B4;">

        <div class="dataOfTableResultProject">

            <?php foreach ($project->segments as $number_segment => $segment) : ?>

                <div class="rowSegmentTableResultProject">

                    <div class="container_all_stage">

                        <div class="segment-blocks" style="display:flex; width: 30.55%;">

                            <!--Наименования сегментов-->
                            <div class="column_segment_name">
                                <?= Html::a('<span>Сегмент ' . ($number_segment+1) . ': </span>' . $segment->getName(),
                                    ['/segments/index', 'id' => $segment->getProjectId()], ['class' => 'link_in_column_result_table']) ?>
                            </div>

                            <!--Статусы сегментов-->
                            <?php if ($segment->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                <div class="text-center regular_column_for_segment">
                                    <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                                        ['/confirm-segment/view', 'id' => $segment->confirm->getId()], ['title'=> 'Посмотреть подтверждение сегмента'])
                                    ?>
                                </div>

                            <?php elseif ($segment->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                <div class="text-center regular_column_for_segment">
                                    <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                                        ['/confirm-segment/view', 'id' => $segment->confirm->getId()], ['title'=> 'Посмотреть подтверждение сегмента'])
                                    ?>
                                </div>

                            <?php elseif ($segment->confirm && $segment->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                <div class="text-center regular_column_for_segment">
                                    <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                        ['/confirm-segment/view', 'id' => $segment->confirm->getId()], ['title'=> 'Посмотреть подтверждение сегмента'])
                                    ?>
                                </div>

                            <?php elseif (!$segment->confirm && $segment->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                <div class="text-center regular_column_for_segment_empty">- - -</div>

                            <?php endif; ?>

                            <!--Даты создания сегментов-->
                            <div class="text-center regular_column_for_segment">
                                <?= date('d.m.y', $segment->getCreatedAt()) ?>
                            </div>

                            <!--Даты подтверждения сегментов-->
                            <div class="text-center regular_column_for_segment">
                                <?php if ($segment->getTimeConfirm()) {
                                    echo date('d.m.y', $segment->getTimeConfirm());
                                } ?>
                            </div>

                        </div>

                        <!--Параметры проблем-->
                        <div class="" style="display:flex; flex-direction: column; width: 69.45%;">

                            <!--Если у сегмента отсутствуют гипотезы проблем-->
                            <?php if (empty($segment->problems)) : ?>

                                <div class="" style="display:flex; height: 100%;">

                                    <div class="text-center first_regular_column_of_stage_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>

                                    <div class="text-center first_regular_column_of_stage_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>

                                    <div class="text-center first_regular_column_of_stage_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>
                                    <div class="text-center regular_column_for_problem"></div>

                                </div>

                            <?php endif;?>

                            <!--Если у сегмента есть гипотезы проблем-->
                            <?php foreach ($segment->problems as $number_problem => $problem) : ?>

                                <div class="" style="display:flex; height: 100%;">

                                    <div class="problem-blocks" style="display:flex; height: 100%; width: 100%;">

                                        <!--Наименования проблем-->
                                        <?php $problem_title = 'ГПС ' . ($number_segment+1) . '.' . explode(' ',$problem->getTitle())[1]; ?>
                                        <div class="text-center first_regular_column_of_stage_for_problem">
                                            <?= Html::a($problem_title, ['/problems/index', 'id' => $problem->getConfirmSegmentId()],
                                                ['class' => 'link_in_column_result_table', 'title' => $problem->getDescription()]) ?>
                                        </div>

                                        <!--Статусы проблем-->
                                        <?php if ($problem->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                            <div class="text-center regular_column_for_problem">
                                                <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                                                    ['/confirm-problem/view', 'id' => $problem->confirm->getId()], ['title'=> 'Посмотреть подтверждение ГПС'])
                                                ?>
                                            </div>

                                        <?php elseif ($problem->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                            <div class="text-center regular_column_for_problem">
                                                <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                                                    ['/confirm-problem/view', 'id' => $problem->confirm->getId()], ['title'=> 'Посмотреть подтверждение ГПС'])
                                                ?>
                                            </div>

                                        <?php elseif ($problem->confirm && $problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                            <div class="text-center regular_column_for_problem">
                                                <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                                    ['/confirm-problem/view', 'id' => $problem->confirm->getId()], ['title'=> 'Посмотреть подтверждение ГПС'])
                                                ?>
                                            </div>

                                        <?php elseif (!$problem->confirm && $problem->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                            <div class="text-center regular_column_for_problem_empty">- - -</div>

                                        <?php endif; ?>

                                        <!--Даты создания проблем-->
                                        <div class="text-center regular_column_for_problem">
                                            <?= date('d.m.y',$problem->getCreatedAt()) ?>
                                        </div>

                                        <!--Даты подтверждения проблем-->
                                        <div class="text-center regular_column_for_problem">
                                            <?php if ($problem->getTimeConfirm()) {
                                                echo date('d.m.y', $problem->getTimeConfirm());
                                            } ?>
                                        </div>


                                        <!--Параметры ценностных предложений-->

                                        <div class="" style="display:flex; flex-direction: column; width: 69.7%;">

                                            <div class="" style="display:flex; height: 100%;">

                                                <!--Если у проблемы отсутствуют гипотезы ценностных предложений-->
                                                <?php if (empty($problem->gcps)) : ?>

                                                    <div class="text-center first_regular_column_of_stage_for_gcp"></div>
                                                    <div class="text-center regular_column_for_gcp"></div>
                                                    <div class="text-center regular_column_for_gcp"></div>
                                                    <div class="text-center regular_column_for_gcp"></div>

                                                    <div class="text-center first_regular_column_of_stage_for_gcp"></div>
                                                    <div class="text-center regular_column_for_gcp"></div>
                                                    <div class="text-center regular_column_for_gcp"></div>
                                                    <div class="text-center regular_column_for_gcp"></div>

                                                <?php endif;?>

                                            </div>

                                            <?php foreach ($problem->gcps as $gcp) : ?>

                                                <div class="gcp-blocks" style="display:flex; height: 100%;">

                                                    <!--Наименования ценностных предложений-->
                                                    <?php $gcp_title = 'ГЦП ' . ($number_segment+1) . '.' . explode(' ',$problem->getTitle())[1] . '.' . explode(' ',$gcp->getTitle())[1]; ?>
                                                    <div class="text-center first_regular_column_of_stage_for_gcp">
                                                        <?= Html::a($gcp_title, ['/gcps/index', 'id' => $gcp->getConfirmProblemId()],
                                                            ['class' => 'link_in_column_result_table', 'title' => $gcp->getDescription()]) ?>
                                                    </div>

                                                    <!--Статусы ценностных предложений-->
                                                    <?php if ($gcp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                                        <div class="text-center regular_column_for_gcp">
                                                            <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                                                                ['/confirm-gcp/view', 'id' => $gcp->confirm->getId()], ['title'=> 'Посмотреть подтверждение ГЦП'])
                                                            ?>
                                                        </div>

                                                    <?php elseif ($gcp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                                        <div class="text-center regular_column_for_gcp">
                                                            <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                                                                ['/confirm-gcp/view', 'id' => $gcp->confirm->getId()], ['title'=> 'Посмотреть подтверждение ГЦП'])
                                                            ?>
                                                        </div>

                                                    <?php elseif ($gcp->confirm && $gcp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                                        <div class="text-center regular_column_for_gcp">
                                                            <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                                                ['/confirm-gcp/view', 'id' => $gcp->confirm->getId()], ['title'=> 'Посмотреть подтверждение ГЦП'])
                                                            ?>
                                                        </div>

                                                    <?php elseif (!$gcp->confirm && $gcp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                                        <div class="text-center regular_column_for_gcp_empty">- - -</div>

                                                    <?php endif; ?>

                                                    <!--Даты создания ценностных предложений-->
                                                    <div class="text-center regular_column_for_gcp">
                                                        <?= date('d.m.y',$gcp->getCreatedAt()) ?>
                                                    </div>

                                                    <!--Даты подтверждения ценностных предложений-->
                                                    <div class="text-center regular_column_for_gcp">
                                                        <?php if ($gcp->getTimeConfirm()) {
                                                            echo date('d.m.y', $gcp->getTimeConfirm());
                                                        } ?>
                                                    </div>


                                                    <!--Параметры mvps-->
                                                    <div class="" style="display:flex; flex-direction: column; width: 56.3%;">

                                                        <!--Если у ценностного предложения отсутствуют mvp-->
                                                        <?php if (empty($gcp->mvps)) : ?>

                                                            <div class="" style="display:flex; height: 100%;">

                                                                <div class="text-center first_regular_column_of_stage_for_mvp"></div>
                                                                <div class="text-center regular_column_for_mvp"></div>
                                                                <div class="text-center regular_column_for_mvp"></div>
                                                                <div class="text-center regular_column_for_mvp"></div>

                                                            </div>

                                                        <?php endif;?>

                                                        <?php foreach ($gcp->mvps as $mvp) : ?>

                                                            <div class="" style="display:flex; height: 100%;">

                                                                <!--Наименования mvps-->
                                                                <?php
                                                                $mvp_title = 'MVP ' . ($number_segment+1) . '.' . explode(' ',$problem->getTitle())[1]
                                                                    . '.' . explode(' ',$gcp->getTitle())[1] . '.' . explode(' ',$mvp->getTitle())[1];
                                                                ?>
                                                                <div class="text-center first_regular_column_of_stage_for_mvp">
                                                                    <?= Html::a($mvp_title, ['/mvps/index', 'id' => $mvp->getConfirmGcpId()],
                                                                        ['class' => 'link_in_column_result_table', 'title' => $mvp->getDescription()]) ?>
                                                                </div>

                                                                <!--Статусы mvps-->
                                                                <?php if ($mvp->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) : ?>

                                                                    <div class="text-center regular_column_for_mvp">
                                                                        <?= Html::a(Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px']]),
                                                                            ['/confirm-mvp/view', 'id' => $mvp->confirm->getId()], ['title'=> 'Посмотреть подтверждение MVP'])
                                                                        ?>
                                                                    </div>

                                                                <?php elseif ($mvp->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) : ?>

                                                                    <div class="text-center regular_column_for_mvp">
                                                                        <?= Html::a(Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px']]),
                                                                            ['/confirm-mvp/view', 'id' => $mvp->confirm->getId()], ['title'=> 'Посмотреть подтверждение MVP'])
                                                                        ?>
                                                                    </div>

                                                                <?php elseif ($mvp->confirm && $mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                                                    <div class="text-center regular_column_for_mvp">
                                                                        <?= Html::a(Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]),
                                                                            ['/confirm-mvp/view', 'id' => $mvp->confirm->getId()], ['title'=> 'Посмотреть подтверждение MVP'])
                                                                        ?>
                                                                    </div>

                                                                <?php elseif (!$mvp->confirm && $mvp->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) : ?>

                                                                    <div class="text-center regular_column_for_mvp_empty">- - -</div>

                                                                <?php endif; ?>

                                                                <!--Даты создания mvps-->
                                                                <div class="text-center regular_column_for_mvp">
                                                                    <?= date('d.m.y',$mvp->getCreatedAt()) ?>
                                                                </div>

                                                                <!--Даты подтверждения mvps-->
                                                                <div class="text-center regular_column_for_mvp">
                                                                    <?php if ($mvp->getTimeConfirm()) {
                                                                        echo date('d.m.y', $mvp->getTimeConfirm());
                                                                    } ?>
                                                                </div>


                                                                <!--Бизнес модели-->
                                                                <?php if ($mvp->businessModel) : ?>

                                                                    <div class="text-center column_business_model_for_mvp">
                                                                        <?= Html::a(Html::img('@web/images/icons/icon-pdf.png', ['style' => ['width' => '20px']]),
                                                                            ['/business-model/index', 'id' => $mvp->confirm->getId()], ['title'=> 'Посмотреть бизнес-модель']) ?>
                                                                    </div>

                                                                <?php else : ?>

                                                                    <div class="text-center column_business_model_for_mvp"></div>

                                                                <?php endif; ?>


                                                            </div>

                                                        <?php endforeach; ?>

                                                    </div>
                                                </div>

                                            <?php endforeach; ?>

                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>

<?php endforeach; ?>


<div class="pagination-admin-projects-result">
    <?= LinkPager::widget([
        'pagination' => $pages,
        'activePageCssClass' => 'pagination_active_page',
        'options' => ['class' => 'admin-projects-result-pagin-list'],
    ]) ?>
</div>