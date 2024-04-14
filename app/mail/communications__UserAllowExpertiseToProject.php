<?php

use app\models\ProjectCommunications;
use app\models\User;
use yii\helpers\Html;

/**
 * @var ProjectCommunications $communication
 * @var int $role
 * @var string $unsubscribeLink
*/

?>

<?php if (in_array($role, [User::ROLE_ADMIN_COMPANY, User::ROLE_MAIN_ADMIN], true)): ?>

    <p>
        Проектант, <?= $communication->user->getUsername() ?>, разрешил эспертизу по этапу «описание проекта: <?= Html::a($communication->project->getProjectName(), Yii::$app->urlManager->createAbsoluteUrl(['/projects/index', 'id' => $communication->project->getUserId(), 'project_id' => $communication->getProjectId()]))?>». Вы можете назначить эксперта на этот проект.
    </p>

<?php elseif ($role === User::ROLE_ADMIN): ?>

    <p>
        Проектант, <?= $communication->user->getUsername() ?>, разрешил эспертизу по этапу «описание проекта: <?= Html::a($communication->project->getProjectName(), Yii::$app->urlManager->createAbsoluteUrl(['/projects/index', 'id' => $communication->project->getUserId(), 'project_id' => $communication->getProjectId()]))?>».
    </p>

<?php endif; ?>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>
