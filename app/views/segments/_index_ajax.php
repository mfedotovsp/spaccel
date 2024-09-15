<?php

use app\models\ProjectCommunications;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use app\models\User;
use app\models\Segments;
use app\models\EnableExpertise;
use app\models\StageExpertise;
use yii\helpers\Url;

/**
 * @var Segments[] $models
 */

?>

<!--Данные для списка сегментов-->
<?php foreach ($models as $model) : ?>

    <div class="hypothesis_table_desktop">

        <div class="row container-one_hypothesis row_hypothesis-<?= $model->getId() ?>">

            <div class="col-lg-3 pl-5 pr-5">

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
                            <?php if ($contractor = $model->contractor): ?>
                                <div class="font-size-12">
                                    <span class="border">Исполнитель: </span>
                                    <span><?= $contractor->getUsername() ?></span>
                                </div>
                            <?php endif; ?>
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

            <div class="col-lg-1 text-center">
                <?= number_format($model->getMarketVolume(), 0, '', ' ') ?>
            </div>

            <div class="col-lg-3">
                <div class="row pull-right display-flex align-items-center pr-10">
                    <div class="mr-25">

                        <?php if ($model->confirm) : ?>

                            <?= Html::a('Далее', ['/confirm-segment/view', 'id' => $model->confirm->getId()], [
                                'class' => 'btn btn-default',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'color' => '#FFFFFF',
                                    'background' => '#52BE7F',
                                    'width' => '120px',
                                    'height' => '40px',
                                    'font-size' => '18px',
                                    'border-radius' => '8px',
                                ]
                            ]) ?>

                        <?php else : ?>

                            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                                <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                                    <?= Html::a('Подтвердить', ['#'], [
                                        'disabled' => true,
                                        'onclick' => 'return false;',
                                        'title' => 'Необходимо разрешить экспертизу',
                                        'class' => 'btn btn-default',
                                        'style' => [
                                            'display' => 'flex',
                                            'align-items' => 'center',
                                            'justify-content' => 'center',
                                            'color' => '#FFFFFF',
                                            'background' => '#707F99',
                                            'width' => '120px',
                                            'height' => '40px',
                                            'font-size' => '18px',
                                            'border-radius' => '8px',
                                        ]
                                    ]) ?>

                                <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                                    <?= Html::a('Подтвердить', ['#'], [
                                        'id' => 'choosing_confirmation_option-segment-' . $model->getId(),
                                        'class' => 'btn btn-default display_choosing_confirmation_option_modal',
                                        'style' => [
                                            'display' => 'flex',
                                            'align-items' => 'center',
                                            'justify-content' => 'center',
                                            'color' => '#FFFFFF',
                                            'background' => '#707F99',
                                            'width' => '120px',
                                            'height' => '40px',
                                            'font-size' => '18px',
                                            'border-radius' => '8px',
                                        ]
                                    ]) ?>

                                <?php endif; ?>

                            <?php else: ?>

                                <?= Html::a('Подтвердить', ['#'], [
                                    'onclick' => 'return false',
                                    'class' => 'btn btn-default',
                                    'style' => [
                                        'display' => 'flex',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'color' => '#FFFFFF',
                                        'background' => '#707F99',
                                        'width' => '120px',
                                        'height' => '40px',
                                        'font-size' => '18px',
                                        'border-radius' => '8px',
                                    ]
                                ]) ?>

                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                    <div>

                        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                            <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/segments/enable-expertise', 'id' => $model->getId()], [
                                    'class' => 'link-enable-expertise',
                                    'title' => 'Разрешить экспертизу',
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::SEGMENT], 'stageId' => $model->getId()], [
                                    'class' => 'link-get-list-expertise',
                                    'title' => 'Смотреть экспертизу',
                                ]) ?>

                            <?php endif; ?>

                            <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),['/segments/get-hypothesis-to-update', 'id' => $model->getId()], [
                                'class' => 'update-hypothesis',
                                'title' => 'Редактировать',
                            ]) ?>

                            <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),['/segments/delete', 'id' => $model->getId()], [
                                'class' => 'delete_hypothesis',
                                'title' => 'Удалить',
                            ]) ?>

                        <?php elseif (User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                            <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['#'], [
                                    'onclick' => 'return false;',
                                    'class' => 'no-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза не разрешена',
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON && ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::SEGMENT], 'stageId' => $model->getId()], [
                                    'class' => 'link-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза',
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON && !ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['#'], [
                                    'onclick' => 'return false;',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза не доступна',
                                ]) ?>

                            <?php endif; ?>

                            <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px']]),['/segments/show-all-information', 'id' => $model->getId()], [
                                'class' => 'openAllInformationSegment', 'title' => 'Смотреть описание сегмента',
                            ]) ?>

                        <?php else : ?>

                            <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['#'], [
                                    'onclick' => 'return false;',
                                    'class' => 'no-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза не разрешена',
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::SEGMENT], 'stageId' => $model->getId()], [
                                    'class' => 'link-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Смотреть экспертизу',
                                ]) ?>

                            <?php endif; ?>

                            <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px']]),['/segments/show-all-information', 'id' => $model->getId()], [
                                'class' => 'openAllInformationSegment', 'title' => 'Смотреть описание сегмента',
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
                    } elseif (!$model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
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

            <?php if ($contractor): ?>
                <div class="col-xs-12 mb-5">
                    <div class="header_table_hypothesis_mobile">Исполнитель:</div>
                    <div class="text_14_table_hypothesis">
                        <?= $contractor->getUsername() ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                <div class="hypothesis_buttons_mobile">

                    <?= Html::a('Редактировать', ['/segments/get-hypothesis-to-update', 'id' => $model->getId()], [
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
                            'margin' => '10px 1% 0 2%',
                        ],
                    ]) ?>

                    <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                        <?= Html::a('Запросить экспертизу', ['/segments/enable-expertise', 'id' => $model->getId()], [
                            'class' => 'btn btn-default link-enable-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 0 1%',
                            ],
                        ]) ?>

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::SEGMENT], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 0 1%',
                            ],
                        ]) ?>

                    <?php endif; ?>

                </div>

                <div class="hypothesis_buttons_mobile">

                    <?= Html::a('Удалить сегмент', ['/segments/delete', 'id' => $model->getId()], [
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
                            'margin' => '10px 1% 2% 2%',
                        ],
                    ]) ?>

                    <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                        <?= Html::a('Работать далее', ['#'], [
                            'disabled' => true,
                            'onclick' => 'return false;',
                            'class' => 'btn btn-default',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#52BE7F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 2% 1%',
                            ]
                        ]) ?>

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                        <?php if ($model->confirm): ?>

                            <?= Html::a('Работать далее', Url::to(['/confirm-segment/view', 'id' => $model->confirm->getId()]), [
                                'class' => 'btn btn-default',
                                'style' => [
                                    'display' => 'flex',
                                    'width' => '47%',
                                    'height' => '36px',
                                    'background' => '#52BE7F',
                                    'color' => '#FFFFFF',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'border-radius' => '0',
                                    'border' => '1px solid #ffffff',
                                    'font-size' => '18px',
                                    'margin' => '10px 2% 2% 1%',
                                ]
                            ]) ?>

                        <?php else: ?>

                            <?= Html::a('Работать далее', ['#'], [
                                'id' => 'choosing_confirmation_option-segment-' . $model->getId(),
                                'class' => 'btn btn-default display_choosing_confirmation_option_modal',
                                'style' => [
                                    'display' => 'flex',
                                    'width' => '47%',
                                    'height' => '36px',
                                    'background' => '#52BE7F',
                                    'color' => '#FFFFFF',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'border-radius' => '0',
                                    'border' => '1px solid #ffffff',
                                    'font-size' => '18px',
                                    'margin' => '10px 2% 2% 1%',
                                ]
                            ]) ?>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>

            <?php elseif (User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                <div class="hypothesis_buttons_mobile">

                    <?= Html::a('Смотреть описание', ['/segments/show-all-information', 'id' => $model->getId()], [
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
                            'margin' => '10px 1% 0 2%',
                        ],
                    ]) ?>

                    <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                        <?= Html::a('Экспертиза не разрешена', ['#'], [
                            'onclick' => 'return false;',
                            'class' => 'btn btn-default link-enable-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 0 1%',
                            ],
                        ]) ?>

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON && ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                        <?= Html::a('Экспертиза', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::SEGMENT], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 0 1%',
                            ],
                        ]) ?>

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON && !ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                        <?= Html::a('Экспертиза не доступна', ['#'], [
                            'onclick' => 'return false;',
                            'class' => 'btn btn-default link-enable-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 0 1%',
                            ],
                        ]) ?>

                    <?php endif; ?>

                </div>

                <div class="hypothesis_buttons_mobile">

                    <?php if ($model->confirm): ?>

                        <?= Html::a('Работать далее', Url::to(['/confirm-segment/view', 'id' => $model->confirm->getId()]), [
                            'class' => 'btn btn-default',
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
                                'margin' => '10px 2% 2% 2%',
                            ]
                        ]) ?>

                    <?php else: ?>

                        <?= Html::a('Работать далее', ['#'], [
                            'disabled' => true,
                            'onclick' => 'return false;',
                            'class' => 'btn btn-default',
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
                                'margin' => '10px 2% 2% 2%',
                            ]
                        ]) ?>

                    <?php endif; ?>

                </div>

            <?php else : ?>

                <div class="hypothesis_buttons_mobile">

                    <?= Html::a('Смотреть описание', ['/segments/show-all-information', 'id' => $model->getId()], [
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
                            'margin' => '10px 1% 0 2%',
                        ],
                    ]) ?>

                    <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                        <?= Html::a('Экспертиза запрещена', ['#'], [
                            'onclick' => 'return false;',
                            'class' => 'btn btn-default link-enable-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 0 1%',
                            ],
                        ]) ?>

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::SEGMENT], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '47%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 2% 0 1%',
                            ],
                        ]) ?>

                    <?php endif; ?>

                </div>

                <div class="hypothesis_buttons_mobile">

                    <?php if ($model->confirm): ?>

                        <?= Html::a('Работать далее', Url::to(['/confirm-segment/view', 'id' => $model->confirm->getId()]), [
                            'class' => 'btn btn-default',
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
                                'margin' => '10px 2% 2% 2%',
                            ]
                        ]) ?>

                    <?php else: ?>

                        <?= Html::a('Работать далее', ['#'], [
                            'disabled' => true,
                            'onclick' => 'return false;',
                            'class' => 'btn btn-default',
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
                                'margin' => '10px 2% 2% 2%',
                            ]
                        ]) ?>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>

<?php endforeach;?>
