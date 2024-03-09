<?php

use app\models\ProjectCommunications;
use app\models\User;
use yii\helpers\Html;
use app\modules\expert\models\form\FormCreateCommunicationResponse;

/**
 * @var User $user
 * @var ProjectCommunications $communication
 */

?>

<p>
    Данное письмо является ответом эксперта <b><?= $user->getUsername() ?></b> на запрос о готовности провети экспертизу проекта <?= Html::a('«'.$communication->project->getProjectName().'»', Yii::$app->urlManager->createAbsoluteUrl(['/projects/index', 'id' => $communication->project->getUserId(), 'project_id' => $communication->getProjectId()])) ?>
</p>

<p>
    <b>Ответ эксперта: </b> <?= FormCreateCommunicationResponse::getAnswers()[$communication->communicationResponse->getAnswer()] ?>

    <?php if ($communication->communicationResponse->getComment()) : ?>
        <br>
        <b>Комментарий: </b> <?= $communication->communicationResponse->getComment() ?>
    <?php endif; ?>
</p>

