<?php

use app\models\ProjectCommunications;

/**
 * @var ProjectCommunications $communication
 * @var string $unsubscribeLink
 */

?>

<p>
    <?= $communication->getDescriptionPattern(true) ?>
</p>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>
