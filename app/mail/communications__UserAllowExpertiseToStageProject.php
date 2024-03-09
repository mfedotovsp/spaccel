<?php

use app\models\CommunicationTypes;
use app\models\ProjectCommunications;
use app\models\StageExpertise;
use app\models\User;
use yii\helpers\Html;


/**
 * @var ProjectCommunications $communication
 * @var int $role
*/

$description = '';

if (in_array($role, [User::ROLE_ADMIN, User::ROLE_EXPERT], true)) {

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_SEGMENT_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::SEGMENT)
            . ': ' . Html::a($communication->hypothesis->getName(), Yii::$app->urlManager->createAbsoluteUrl(['/segments/index', 'id' => $communication->getProjectId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_SEGMENT_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::CONFIRM_SEGMENT)
            . ': ' . Html::a($communication->hypothesis->segment->getName(), Yii::$app->urlManager->createAbsoluteUrl(['/confirm-segment/view', 'id' => $communication->getHypothesisId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_PROBLEM_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::PROBLEM)
            . ': ' . Html::a($communication->hypothesis->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/problems/index', 'id' => $communication->hypothesis->getBasicConfirmId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_PROBLEM_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::CONFIRM_PROBLEM)
            . ': ' . Html::a($communication->hypothesis->problem->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/confirm-problem/view', 'id' => $communication->getHypothesisId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_GCP_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::GCP)
            . ': ' . Html::a($communication->hypothesis->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/gcps/index', 'id' => $communication->hypothesis->getBasicConfirmId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_GCP_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::CONFIRM_GCP)
            . ': ' . Html::a($communication->hypothesis->gcp->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/confirm-gcp/view', 'id' => $communication->getHypothesisId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_MVP_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::MVP)
            . ': ' . Html::a($communication->hypothesis->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/mvps/index', 'id' => $communication->hypothesis->getBasicConfirmId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_MVP_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::CONFIRM_MVP)
            . ': ' . Html::a($communication->hypothesis->mvp->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/confirm-mvp/view', 'id' => $communication->getHypothesisId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_ALLOWED_BUSINESS_MODEL_EXPERTISE) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', разрешил эспертизу по этапу «' . $communication->getStage(StageExpertise::BUSINESS_MODEL)
            . ': ' . Html::a($communication->hypothesis->mvp->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/business-model/index', 'id' => $communication->getHypothesisId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }
}

?>

<p><?= $description ?></p>
