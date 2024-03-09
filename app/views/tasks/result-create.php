<?php

/**
 * @var $success bool
 */

?>

<?php if (!$success): ?>
    <h4 class="text-center text-danger">Не удалось сохранить задание. Обратитесь в техподдержку.</h4>
<?php else: ?>
    <h4 class="text-center text-success">Задание сохранено.</h4>
<?php endif; ?>
