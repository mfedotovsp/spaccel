<?php

use app\models\Expertise;
use app\models\StageExpertise;
use yii\helpers\Html;
use app\models\ExpertType;

/**
 * @var array $types
 * @var string $stage
 * @var int $stageId
 */

?>

<div class="row" style="margin-bottom: 20px;">

    <h4 class="text-center bolder">Выберите тип деятельности, по которому будет проводиться экспертиза</h4>

    <?php foreach ($types as $type) : ?>

        <div class="col-md-12 text-center">

            <?php
                $checkExistAndCheckCompletedExpertise = Expertise::checkExistAndCheckCompleted(StageExpertise::getKey($stage), $stageId, ExpertType::getKey($type), Yii::$app->user->getId());
                if (!$checkExistAndCheckCompletedExpertise) :
            ?>

                <?= Html::a($type . Html::img('@web/images/icons/next-step.png', [ 'class' => 'pull-right', 'style' => ['width' => '20px', 'margin-top' => '3px']]), ['/expertise/get-form', 'stage' => $stage, 'stageId' => $stageId, 'type' => ExpertType::getKey($type)], [
                    'class' => 'btn btn-default link-get-form-expertise',
                    'style' => [
                        'width' => '80%',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                    ]
                ]) ?>

            <?php elseif ($checkExistAndCheckCompletedExpertise === Expertise::NO_COMPLETED) : ?>

                    <?= Html::a($type . Html::img('@web/images/icons/danger-offer.png', [ 'class' => 'pull-right', 'style' => ['width' => '20px', 'margin-top' => '3px']]), ['/expertise/get-form', 'stage' => $stage, 'stageId' => $stageId, 'type' => ExpertType::getKey($type)], [
                        'class' => 'btn btn-default link-get-form-expertise',
                        'style' => [
                            'width' => '80%',
                            'height' => '40px',
                            'font-size' => '18px',
                            'border-radius' => '8px',
                        ]
                    ]) ?>

            <?php elseif ($checkExistAndCheckCompletedExpertise === Expertise::COMPLETED) : ?>

                <?= Html::a($type . Html::img('@web/images/icons/positive-offer.png', [ 'class' => 'pull-right', 'style' => ['width' => '20px', 'margin-top' => '3px']]), ['/expertise/get-form', 'stage' => $stage, 'stageId' => $stageId, 'type' => ExpertType::getKey($type)], [
                    'class' => 'btn btn-default link-get-form-expertise',
                    'style' => [
                        'width' => '80%',
                        'height' => '40px',
                        'font-size' => '18px',
                        'border-radius' => '8px',
                    ]
                ]) ?>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

    <div class="col-md-12" style="display:flex; justify-content: space-between; margin-top: 50px;">
        <div style="display:flex; align-items: center;">
            <?= Html::img('@web/images/icons/positive-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
            <div>Экспертиза завершена</div>
        </div>

        <div style="display:flex; align-items: center;">
            <?= Html::img('@web/images/icons/danger-offer.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
            <div>Экспертиза не завершена</div>
        </div>

        <div style="display:flex; align-items: center;">
            <?= Html::img('@web/images/icons/next-step.png', ['style' => ['width' => '20px', 'margin-right' => '8px']]) ?>
            <div>Экспертиза отсутствует</div>
        </div>
    </div>

</div>
