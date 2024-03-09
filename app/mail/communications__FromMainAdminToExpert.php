<?php

use app\models\ProjectCommunications;

/** @var ProjectCommunications $communication*/

?>

<p>
    <?= $communication->getDescriptionPattern(true) ?>
</p>