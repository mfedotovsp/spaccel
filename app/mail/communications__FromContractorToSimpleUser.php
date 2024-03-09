<?php

use app\models\ContractorCommunications;
use app\models\User;
use yii\helpers\Html;
use app\modules\contractor\models\form\FormCreateCommunicationResponse;

/**
 * @var User $contractor
 * @var ContractorCommunications $communication
 */

?>

<p>
    Данное письмо является ответом исполнителя <b><?= $contractor->getUsername() ?></b> на запрос о готовности присоединиться к работе над проектм <?= Html::a('«'.$communication->project->getProjectName().'»', Yii::$app->urlManager->createAbsoluteUrl(['/projects/index', 'id' => $communication->project->getUserId(), 'project_id' => $communication->getProjectId()])) ?>
</p>

<p>
    <b>Ответ исполнителя: </b> <?= FormCreateCommunicationResponse::getAnswers()[$communication->communicationResponse->getAnswer()] ?>

    <?php if ($communication->communicationResponse->getComment()) : ?>
        <br>
        <b>Комментарий: </b> <?= $communication->communicationResponse->getComment() ?>
    <?php endif; ?>
</p>

