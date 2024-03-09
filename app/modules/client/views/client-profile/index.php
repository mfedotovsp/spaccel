<?php

$this->title = 'Профиль организации';
$this->registerCssFile('@web/css/profile-style.css');

use app\models\Client;
use app\modules\client\models\form\AvatarCompanyForm;
use app\modules\client\models\form\FormUpdateClient;

/**
 * @var Client $client
 * @var FormUpdateClient $model
 * @var AvatarCompanyForm $avatarForm
 */

?>

<div class="client-profile">

    <div class="row profile_menu" style="height: 51px;">

    </div>


    <div class="data_client_content">
        <?= $this->render('ajax_view', [
             'client' => $client,
             'model' => $model,
             'avatarForm' => $avatarForm
        ]) ?>
    </div>

    <!--Модальные окна-->
    <?= $this->render('modal_profile_company') ?>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/profile_company.js'); ?>
