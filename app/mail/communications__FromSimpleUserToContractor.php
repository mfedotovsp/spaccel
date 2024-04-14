<?php

use app\models\ContractorCommunications;

/**
 * @var ContractorCommunications $communication
 * @var string $unsubscribeLink
 */

?>

<p>
    <?= $communication->getDescription(true) ?>
</p>

<p style="color:slategray; font-size: 12px; margin-top: 30px">
    Отписаться от рассылки можно по
    <a style="color:slategray" href="<?= $unsubscribeLink ?>">этой ссылке</a>.
</p>
