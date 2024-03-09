<?php

use app\models\Projects;
use app\models\Segments;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use app\models\EnableExpertise;
use app\models\StageExpertise;

/**
 * @var Projects[] $models
 * @var int $userId
*/
?>

<!--Данные для списка проектов-->
<?php foreach ($models as $model) : ?>

    <div class="row container-one_hypothesis row_hypothesis-<?= $model->getId() ?>">

        <div class="col-lg-3">

            <div class="project_name_table hypothesis_title">
                <?= $model->getProjectName() ?>
            </div>

            <div class="project_description_text" title="<?= $model->getDescription() ?>">
                <?= $model->getDescription() ?>
            </div>

        </div>


        <div class="col-lg-3">

            <div class="header_table_project_mobile">
                Результат интеллектуальной деятельности
            </div>

            <div class="text_14_table_project" title="<?= $model->getRid() ?>">
                <?= $model->getRid() ?>
            </div>

        </div>

        <div class="col-lg-2">

            <div class="header_table_project_mobile">
                Базовая технология
            </div>

            <div class="text_14_table_project" title="<?= $model->getTechnology() ?>">
                <?= $model->getTechnology() ?>
            </div>

        </div>

        <div class="col-lg-4 part_project_table_desktop">

            <div style="display:flex; align-items: center; justify-content: space-between; padding-right: 15px;">

                <div style="min-width: 130px;"><?= date('d.m.y', $model->getCreatedAt()) . ' / ' . date('d.m.y', $model->getUpdatedAt()) ?></div>

                <div class="pull-right" style="display: flex; align-items: center; justify-content: space-between;">

                    <?php
                    $countSegments = Segments::find(false)
                        ->andWhere(['project_id' => $model->getId()])
                        ->count();

                    if ((int)$countSegments > 0 && $model->getEnableExpertise() === EnableExpertise::ON) : ?>

                        <?= Html::a('Далее', Url::to(['/segments/index', 'id' => $model->getId()]), [
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

                    <?php endif; ?>

                    <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                        <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                            <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '10px']]),['#'], [
                                'class' => 'link-enable-expertise', 'style' => ['margin-left' => '20px'], 'title' => 'Экспертиза не разрешена', 'onclick' => 'return false;',
                            ]) ?>

                        <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                            <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '10px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROJECT], 'stageId' => $model->getId()], [
                                'class' => 'link-get-list-expertise',
                                'style' => ['margin-left' => '20px'],
                                'title' => 'Смотреть экспертизу',
                            ]) ?>

                        <?php endif; ?>

                        <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px']]),['/projects/show-all-information', 'id' => $model->getId()], [
                            'class' => 'openAllInformationProject',
                            'style' => ['margin-left' => '8px'],
                            'title' => 'Смотреть описание проекта',
                        ]) ?>

                        <?= Html::a(Html::img('/images/icons/recovery_icon.png', ['style' => ['width' => '24px']]),
                            ['/projects/recovery', 'id' => $model->getId()], [
                                'title' => 'Восстановить'
                            ]) ?>

                    <?php else : ?>

                        <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                            <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '10px']]),['#'], [
                                'class' => 'link-enable-expertise', 'style' => ['margin-left' => '20px'], 'title' => 'Экспертиза не разрешена', 'onclick' => 'return false;',
                            ]) ?>

                        <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                            <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '10px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROJECT], 'stageId' => $model->getId()], [
                                'class' => 'link-get-list-expertise',
                                'style' => ['margin-left' => '20px'],
                                'title' => 'Смотреть экспертизу',
                            ]) ?>

                        <?php endif; ?>

                        <?= Html::a(Html::img('/images/icons/icon_view.png', ['style' => ['width' => '28px', 'margin-right' => '20px']]),['/projects/show-all-information', 'id' => $model->getId()], [
                            'class' => 'openAllInformationProject',
                            'style' => ['margin-left' => '8px'],
                            'title' => 'Смотреть описание проекта',
                        ]) ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="col-lg-4 part_project_table_mobile">

            <div class="project_dates_mobile">
                <div class="project_created_at">
                    <div style="font-weight: 700;">Создан:</div>
                    <div><?= date('d.m.y', $model->getCreatedAt()) ?></div>
                </div>
                <div class="project_updated_at">
                    <div style="font-weight: 700;">Изменен:</div>
                    <div><?= date('d.m.y', $model->getUpdatedAt()) ?></div>
                </div>
            </div>

            <div class="project_buttons_mobile">

                <?= Html::a('Сводная таблица', ['/projects/result-mobile', 'id' => $model->getId()], [
                    'class' => 'btn btn-default',
                    'style' => [
                        'display' => 'flex',
                        'width' => '47%',
                        'height' => '36px',
                        'background' => '#C6C6C6',
                        'color' => '#FFFFFF',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'border-radius' => '0',
                        'border' => '1px solid #ffffff',
                        'font-size' => '18px',
                        'margin' => '10px 1% 0 2%',
                    ],
                ]) ?>

                <?= Html::a('Трэкшн карта', ['/projects/roadmap-mobile', 'id' => $model->getId()], [
                    'class' => 'btn btn-default',
                    'style' => [
                        'display' => 'flex',
                        'width' => '47%',
                        'height' => '36px',
                        'background' => '#C6C6C6',
                        'color' => '#FFFFFF',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'border-radius' => '0',
                        'border' => '1px solid #ffffff',
                        'font-size' => '18px',
                        'margin' => '10px 2% 0 1%',
                    ],
                ]) ?>

            </div>

            <div class="project_buttons_mobile">

                <?= Html::a('Протокол', ['/projects/report-mobile', 'id' => $model->getId()], [
                    'class' => 'btn btn-default',
                    'style' => [
                        'display' => 'flex',
                        'width' => '47%',
                        'height' => '36px',
                        'background' => '#C6C6C6',
                        'color' => '#FFFFFF',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'border-radius' => '0',
                        'border' => '1px solid #ffffff',
                        'font-size' => '18px',
                        'margin' => '10px 1% 20px 2%',
                    ],
                ]) ?>

                <?= Html::a('Презентация', ['/projects/presentation-mobile', 'id' => $model->getId()], [
                    'class' => 'btn btn-default',
                    'style' => [
                        'display' => 'flex',
                        'width' => '47%',
                        'height' => '36px',
                        'background' => '#C6C6C6',
                        'color' => '#FFFFFF',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'border-radius' => '0',
                        'border' => '1px solid #ffffff',
                        'font-size' => '18px',
                        'margin' => '10px 2% 20px 1%',
                    ],
                ]) ?>

            </div>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                <div class="project_buttons_mobile">

                    <?= Html::a('Смотреть проект', ['/projects/show-all-information', 'id' => $model->getId()], [
                        'class' => 'btn btn-default openAllInformationProject',
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

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROJECT], 'stageId' => $model->getId()], [
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

                <div class="project_buttons_mobile">

                    <?= Html::a('Восстановить проект', ['/projects/recovery', 'id' => $model->getId()], [
                        'class' => 'btn btn-default',
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

                    <?php if ((int)$countSegments === 0 || $model->getEnableExpertise() === EnableExpertise::OFF) : ?>

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

                        <?= Html::a('Работать далее', Url::to(['/segments/index', 'id' => $model->getId()]), [
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

            <?php else: ?>

                <?php if ((int)$countSegments === 0 || $model->getEnableExpertise() === EnableExpertise::ON) : ?>

                    <div class="project_buttons_mobile">

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::PROJECT], 'stageId' => $model->getId()], [
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
                                'margin' => '0 1% 2% 2%',
                            ],
                        ]) ?>

                        <?= Html::a('Работать далее', Url::to(['/segments/index', 'id' => $model->getId()]), [
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
                                'margin' => '0 2% 2% 1%',
                            ]
                        ]) ?>

                    </div>

                <?php else: ?>

                    <div class="text-block-not-expertise-mobile">Экспертиза не разрешена</div>

                <?php endif; ?>

            <?php endif; ?>

        </div>

    </div>
<?php endforeach;?>


<div class="mt-15" style="display: flex; justify-content: center;">
    <?= Html::a('Вернуться к списку', ['/projects/list', 'id' => $userId],[
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
