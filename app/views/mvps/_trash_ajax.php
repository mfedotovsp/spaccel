<?php

use app\models\ConfirmMvp;
use app\models\Mvps;
use app\models\StatusConfirmHypothesis;
use yii\helpers\Html;
use app\models\User;
use app\models\EnableExpertise;
use app\models\StageExpertise;
use yii\helpers\Url;

/**
 * @var Mvps[] $models
 * @var int $basicConfirmId
 */

?>


<!--Данные для списка MVP -->
<?php foreach ($models as $model) : ?>

    <?php
    /** @var $confirm ConfirmMvp */
    $confirm = ConfirmMvp::find(false)
        ->andWhere(['mvp_id' => $model->getId()])
        ->one()
    ?>

    <div class="hypothesis_table_desktop">
        <div class="row container-one_hypothesis row_hypothesis-<?= $model->getId() ?>">

            <div class="col-lg-1">
                <div class="row">

                    <div class="col-lg-4" style="padding: 0;">

                        <?php if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {

                            echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px',]]) . '</div>';

                        }elseif (!$confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

                            echo '<div class="" style="padding: 0 5px;">' . Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px']]) . '</div>';

                        }elseif ($confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {

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

            <div class="col-lg-6 text_description_problem" title="<?= $model->getDescription() ?>">
                <?php if ($contractor = $model->contractor): ?>
                    <div class="font-size-12">
                        <span class="border">Исполнитель: </span>
                        <span><?= $contractor->getUsername() ?></span>
                    </div>
                <?php endif; ?>
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

            <div class="col-lg-3">

                <div class="row pull-right" style="padding-right: 10px; display:flex; align-items: center;">

                    <div style="margin-right: 25px;">

                        <?php if ($confirm): ?>

                            <?= Html::a('Далее', ['/confirm-mvp/view-trash', 'id' => $confirm->getId()], [
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

                    </div>
                    <div>

                        <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                            <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-danger.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['#'], [
                                    'class' => 'link-enable-expertise', 'title' => 'Экспертиза не разрешена', 'onclick' => 'return false;'
                                ]) ?>

                            <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::MVP], 'stageId' => $model->getId()], [
                                    'class' => 'link-get-list-expertise',
                                    'title' => 'Смотреть экспертизу',
                                ]) ?>

                            <?php endif; ?>

                            <?= Html::a(Html::img('/images/icons/recovery_icon.png', ['style' => ['width' => '24px']]),
                                ['/mvps/recovery', 'id' => $model->getId()], [
                                'title' => 'Восстановить'
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

                                <?= Html::a(Html::img('/images/icons/icon-enable-expertise-success.png', ['style' => ['width' => '35px', 'margin-right' => '20px']]),['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::MVP], 'stageId' => $model->getId()], [
                                    'class' => 'link-get-list-expertise',
                                    'style' => ['margin-left' => '20px'],
                                    'title' => 'Экспертиза',
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

            <div class="col-xs-12">
                <span class="header_table_hypothesis_mobile">Статус:</span>
                <span class="text_14_table_hypothesis">
                    <?php if ($model->getExistConfirm() === StatusConfirmHypothesis::COMPLETED) {
                        echo 'подтвержден';
                    } elseif (!$confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
                        echo 'ожидает подтверждения';
                    } elseif ($confirm && $model->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE) {
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

                    <?= Html::a('Восстановить MVP', ['/mvps/recovery', 'id' => $model->getId()], [
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

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::MVP], 'stageId' => $model->getId()], [
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

                    <?php if ($confirm && $model->getEnableExpertise() === EnableExpertise::ON) : ?>

                        <?= Html::a('Работать далее', Url::to(['/confirm-mvp/view-trash', 'id' => $confirm->getId()]), [
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

            <?php else: ?>

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

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::MVP], 'stageId' => $model->getId()], [
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

                    <?php if ($confirm): ?>

                        <?= Html::a('Работать далее', Url::to(['/confirm-mvp/view-trash', 'id' => $confirm->getId()]), [
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

<div class="mt-15" style="display: flex; justify-content: center;">
    <?= Html::a('Вернуться к списку', ['/mvps/list', 'id' => $basicConfirmId],[
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
