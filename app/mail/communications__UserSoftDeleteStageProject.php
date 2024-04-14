<?php

use app\models\CommunicationTypes;
use app\models\ProjectCommunications;
use app\models\User;
use yii\helpers\Html;

/**
 * @var ProjectCommunications $communication
 * @var int $role
 * @var string $unsubscribeLink
*/

$description = '';

if (in_array($role, [User::ROLE_ADMIN, User::ROLE_EXPERT], true)) {

    if ($communication->getType() === CommunicationTypes::USER_DELETED_PROJECT) {
        if ($role === User::ROLE_EXPERT) {
            $description = 'Проектант, ' . $communication->user->getUsername() . ', удалил проект «' . Html::a($communication->hypothesis->getProjectName(), Yii::$app->urlManager->createAbsoluteUrl(['/projects/index', 'id' => $communication->project->getUserId(), 'project_id' => $communication->getProjectId()])) . '»';
        } else {
            $description = 'Проектант, ' . $communication->user->getUsername() . ', удалил проект «' . Html::a($communication->hypothesis->getProjectName(), Yii::$app->urlManager->createAbsoluteUrl(['/projects/index', 'id' => $communication->project->getUserId()])) . '»';
        }
    }

    if ($communication->getType() === CommunicationTypes::USER_DELETED_SEGMENT) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', удалил сегмент «' . Html::a($communication->hypothesis->getName(), Yii::$app->urlManager->createAbsoluteUrl(['/segments/index', 'id' => $communication->getProjectId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_DELETED_PROBLEM) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', удалил проблему сегмента «' . Html::a($communication->hypothesis->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/problems/index', 'id' => $communication->hypothesis->getBasicConfirmId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_DELETED_GCP) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', удалил ценностное предложение «' . Html::a($communication->hypothesis->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/gcps/index', 'id' => $communication->hypothesis->getBasicConfirmId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }

    if ($communication->getType() === CommunicationTypes::USER_DELETED_MVP) {
        $description = 'Проектант, ' . $communication->user->getUsername() . ', удалил MVP-продукт «' . Html::a($communication->hypothesis->getTitle(), Yii::$app->urlManager->createAbsoluteUrl(['/mvps/index', 'id' => $communication->hypothesis->getBasicConfirmId()]))
            . '»</br>Проект: «' . $communication->project->getProjectName() . '»';
    }
}

?>

<p><?= $description ?></p>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>
