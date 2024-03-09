<?php

use yii\helpers\Html;
use app\models\CommunicationPatterns;

/**
 * @var CommunicationPatterns[] $patternsCARCE
 */

?>

<?php if ($patternsCARCE) : ?>

    <!--Заголовки для созданных шаблонов-->
    <div class="row block-patterns">
        <div class="col-xs-6 col-lg-8">Описание шаблона коммуникации</div>
        <div class="col-xs-3 col-lg-2 text-center">Срок доступа к проекту</div>
        <div class="col-xs-3 col-lg-2 text-center">Действия</div>
    </div>

    <!--Созданные шаблоны-->
    <?php foreach ($patternsCARCE as $pattern) : ?>
        <div class="row style-row-pattern row-pattern-<?= $pattern->getId() ?>">

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
                        ['/client/communications/deactivate-pattern', 'id' => $pattern->getId()],
                        [
                            'class' => 'deactivate-communication-pattern',
                            'style' => ['margin-left' => '30px'],
                            'title' => 'Отменить'
                        ]
                    ) ?>

                <?php else : ?>

                    <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                        [
                            '/client/communications/activate-pattern', 'id' => $pattern->getId(),
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
                        '/client/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                        'communicationType' => $pattern->getCommunicationType()
                    ],
                    [
                        'class' => 'update-communication-pattern',
                        'title' => 'Редактировать'
                    ]
                ) ?>

                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
                    ['/client/communications/delete-pattern', 'id' => $pattern->getId()],
                    [
                        'class' => 'delete-communication-pattern',
                        'title' => 'Удалить'
                    ]
                ) ?>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
