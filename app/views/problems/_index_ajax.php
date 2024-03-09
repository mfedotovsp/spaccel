<?php

use app\models\Problems;
use app\models\ProjectCommunications;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use app\models\User;
use app\models\EnableExpertise;
use app\models\StageExpertise;
use yii\helpers\Url;

/**
 * @var Problems[] $models
 */

?>

<!--Данные для списка проблем-->
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

            <div class="col-lg-5 text_field_problem" title="<?= $model->getDescription() ?>">
                <?php if ($contractor = $model->contractor): ?>
                    <div class="font-size-12">
                        <span class="border">Исполнитель: </span>
                        <span><?= $contractor->getUsername() ?></span>
                    </div>
                <?php endif; ?>
                <?= $model->getDescription() ?>
            </div>

            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-6 text_field_problem text-center">
                        К = <?= $model->getIndicatorPositivePassage() ?> %
                    </div>

                    <div class="col-lg-3 text-center">
                        <?= date("d.m.y", $model->getCreatedAt()) ?>
                    </div>

                    <div class="col-lg-3 text-center">
                        <?php if ($model->getTimeConfirm()) : ?>
                            <?= date("d.m.y", $model->getTimeConfirm()) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="row pull-right" style="padding-right: 10px; display:flex; align-items: center;">
                    <div style="margin-right: 25px;">

                        <?php if ($model->confirm) : ?>

                            <?= Html::a('Далее', ['/confirm-problem/view', 'id' => $model->confirm->getId()], [
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
                            ])
                            ?>

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

                                    <?= Html::a('Подтвердить', ['/confirm-problem/create', 'id' => $model->getId()], [
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

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/problems/enable-expertise', 'id' => $model->getId()], [
                                    'class' => 'link-enable-expertise',
                                    'title' => 'Разрешить экспертизу',
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROBLEM], 'stageId' => $model->getId()], [
                                    'class' => 'link-get-list-expertise',
                                    'title' => 'Смотреть экспертизу',
                                ]) ?>

                            <?php endif; ?>

                            <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),['/problems/get-hypothesis-to-update', 'id' => $model->getId()], [
                                'class' => 'update-hypothesis',
                                'title' => 'Редактировать',
                            ]) ?>

                            <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),['/problems/delete', 'id' => $model->getId()], [
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

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON  && ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROBLEM], 'stageId' => $model->id], [
                                    'class' => 'link-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза',
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON  && !ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['#'], [
                                    'onclick' => 'return false;',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза не доступна',
                                ]) ?>

                            <?php endif; ?>

                        <?php else : ?>

                            <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['#'], [
                                    'onclick' => 'return false;',
                                    'class' => 'no-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза не разрешена',
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROBLEM], 'stageId' => $model->getId()], [
                                    'class' => 'link-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Смотреть экспертизу',
                                ]) ?>

                            <?php endif; ?>

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

            <div class="col-xs-12 mb-5">
                <span class="header_table_hypothesis_mobile">Статус:</span>
                <span class="text_14_table_hypothesis">
                    <?php if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                        echo 'подтверждена';
                    } elseif (!$model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($model->confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($model->getExistConfirm() === StatusConfirmHypothesis::NOT_COMPLETED) {
                        echo 'не подтвержена';
                    } ?>
                </span>
            </div>

            <div class="col-xs-12 mb-5">
                <span class="header_table_hypothesis_mobile">Описание:</span>
                <span class="text_14_table_hypothesis">
                    <?= $model->getDescription() ?>
                </span>
            </div>

            <div class="col-xs-12 mb-5">
                <span class="header_table_hypothesis_mobile">Показатель прохождения теста:</span>
                <span class="text_14_table_hypothesis">
                    <?= $model->getIndicatorPositivePassage() . '%' ?>
                </span>
            </div>

            <div class="col-xs-12 mb-5">
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

                    <?= Html::a('Редактировать', ['/problems/get-hypothesis-to-update', 'id' => $model->getId()], [
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

                        <?= Html::a('Запросить экспертизу', ['/problems/enable-expertise', 'id' => $model->getId()], [
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

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROBLEM], 'stageId' => $model->getId()], [
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

                    <?= Html::a('Удалить проблему', ['/problems/delete', 'id' => $model->getId()], [
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

                        <?= Html::a('Работать далее', Url::to(['/confirm-problem/create', 'id' => $model->getId()]), [
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

                    <?php endif; ?>

                </div>

            <?php elseif (User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                <div class="hypothesis_buttons_mobile">

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

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON  && ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                        <?= Html::a('Экспертиза', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROBLEM], 'stageId' => $model->getId()], [
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

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON  && !ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

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

                    <?php if ($model->confirm): ?>

                        <?= Html::a('Работать далее', Url::to(['/confirm-problem/view', 'id' => $model->confirm->getId()]), [
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

                    <?php endif; ?>

                </div>

            <?php else : ?>

                <div class="hypothesis_buttons_mobile">

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

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROBLEM], 'stageId' => $model->getId()], [
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

                    <?php if ($model->confirm): ?>

                        <?= Html::a('Работать далее', Url::to(['/confirm-problem/view', 'id' => $model->confirm->getId()]), [
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

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>

<?php endforeach; ?>
