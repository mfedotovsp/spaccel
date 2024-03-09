<?php

use yii\helpers\Html;
use app\models\CommunicationPatterns;

/**
 * @var CommunicationPatterns[] $patterns
 * @var int $communicationType
 */

?>

<?php if ($patterns) : ?>

    <!--Заголовки для созданных шаблонов-->
    <div class="row block-patterns">
        <div class="col-xs-9 col-lg-10">Описание шаблона коммуникации</div>
        <div class="col-xs-3 col-lg-2 text-center">Действия</div>
    </div>

    <!--Созданные шаблоны-->
    <?php foreach ($patterns as $pattern) : ?>
        <div class="row style-row-pattern row-pattern-<?= $pattern->getId() ?>">

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
                            'communicationType' => $communicationType
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
                        'communicationType' => $communicationType
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
        </div>
    <?php endforeach; ?>
<?php endif; ?>