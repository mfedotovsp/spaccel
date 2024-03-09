<?php

use app\models\CommunicationTypes;
use app\models\CommunicationPatterns;
use yii\helpers\Html;

/**
 * @var CommunicationPatterns $pattern
 * @var array $selection_project_access_period
 */

?>


<?php if ($pattern->getCommunicationType() === CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE) : ?>

    <div class="col-xs-6 col-lg-8"><?= $pattern->getDescription() ?></div>
    <div class="col-xs-3 col-lg-2 text-center">
        <?php if (in_array($pattern->getProjectAccessPeriod(), [1, 21])) {
            echo $pattern->getProjectAccessPeriod() . ' день';
        } elseif (in_array($pattern->getProjectAccessPeriod(), [2, 3, 4, 22, 23, 24])) {
            echo $pattern->getProjectAccessPeriod() . ' дня';
        } else {
            echo $pattern->getProjectAccessPeriod() . ' дней';
        } ?>
    </div>
    <div class="col-xs-3 col-lg-2 text-center">

        <?php if ($pattern->getIsActive() === CommunicationPatterns::ACTIVE) : ?>

            <?= Html::a(Html::img('/images/icons/icon_circle_active.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                ['/admin/communications/deactivate-pattern', 'id' => $pattern->getId()],
                [
                    'class' => 'deactivate-communication-pattern',
                    'style' => ['margin-left' => '30px'],
                    'title' => 'Отменить'
                ]
            ) ?>

        <?php else : ?>

            <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                [
                    '/admin/communications/activate-pattern', 'id' => $pattern->getId(),
                    'communicationType' => $pattern->getCommunicationType()
                ],
                [
                    'class' => 'activate-communication-pattern',
                    'style' => ['margin-left' => '30px'],
                    'title' => 'Применить'
                ]
            ) ?>

        <?php endif; ?>

        <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
            [
                '/admin/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                'communicationType' => $pattern->getCommunicationType()
            ],
            [
                'class' => 'update-communication-pattern',
                'title' => 'Редактировать'
            ]
        ) ?>

        <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
            ['/admin/communications/delete-pattern', 'id' => $pattern->getId()],
            [
                'class' => 'delete-communication-pattern',
                'title' => 'Удалить'
            ]
        ) ?>

    </div>

<?php else : ?>

    <div class="col-xs-9 col-lg-10"><?= $pattern->getDescription() ?></div>

    <div class="col-xs-3 col-lg-2 text-center">

        <?php if ($pattern->getIsActive() === CommunicationPatterns::ACTIVE) : ?>

            <?= Html::a(Html::img('/images/icons/icon_circle_active.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                ['/admin/communications/deactivate-pattern', 'id' => $pattern->getId()],
                [
                    'class' => 'deactivate-communication-pattern',
                    'style' => ['margin-left' => '30px'],
                    'title' => 'Отменить'
                ]
            ) ?>

        <?php else : ?>

            <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                [
                    '/admin/communications/activate-pattern', 'id' => $pattern->getId(),
                    'communicationType' => $pattern->getCommunicationType()
                ],
                [
                    'class' => 'activate-communication-pattern',
                    'style' => ['margin-left' => '30px'],
                    'title' => 'Применить'
                ]
            ) ?>

        <?php endif; ?>

        <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
            [
                '/admin/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                'communicationType' => $pattern->getCommunicationType()
            ],
            [
                'class' => 'update-communication-pattern',
                'title' => 'Редактировать'
            ]
        ) ?>

        <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
            ['/admin/communications/delete-pattern', 'id' => $pattern->getId()],
            [
                'class' => 'delete-communication-pattern',
                'title' => 'Удалить'
            ]
        ) ?>

    </div>

<?php endif; ?>
