<?php

/**
 * @var LocationWishList[] $models
 */

use app\models\LocationWishList;
use yii\helpers\Html;

?>

<?php foreach ($models as $k => $model): ?>

    <div class="data-location">
        <div class="row data-location-style">
            <div class="col-xs-11">
                <?= ($k+1) .'. '. $model->getName() ?>
            </div>
            <div class="col-xs-1">
                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),['/admin/location/get-location-to-update', 'id' => $model->getId()], [
                    'id' => 'update_location-' . $model->getId(),
                    'class' => 'update-location pull-right',
                    'title' => 'Редактировать',
                ]) ?>
            </div>
        </div>
    </div>

<?php endforeach; ?>