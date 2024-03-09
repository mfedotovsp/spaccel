<?php

use app\models\WishList;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/**
 * @var WishList[] $models
 * @var Pagination $pages
 * @var integer $clientId
 */

?>

<?php if ($models): ?>

    <?php foreach ($models as $model): ?>

        <div class="parent-wish_list_ready">
            <div class="row one-wish_list_ready">
                <div class="col-md-4 pl-20"><?= $model->getCompanyName() ?></div>
                <div class="col-md-3"><?= $model->getTypeCompanyName() ?></div>
                <div class="col-md-3"><?= $model->getTypeProductionName() ?></div>
                <div class="col-md-2"><?= $model->client->getName() ?></div>
            </div>

            <div class="row one-wish_list_ready-data">

                <div class="col-md-12 pl-20">
                    <div><?= '<span class="bolder">Размер предприятия по количеству персонала: </span>' . $model->getSizeName() ?></div>
                    <div><?= '<span class="bolder">Локация предприятия (город): </span>' . $model->location->getName() ?></div>
                    <div><?= '<span class="bolder">Сфера деятельности предприятия: </span>' . $model->getCompanyFieldOfActivity() ?></div>
                    <div><?= '<span class="bolder">Вид деятельности предприятия: </span>' . $model->getCompanySortOfActivity() ?></div>
                    <div><?= '<span class="bolder">Продукция/услуги предприятия: </span>' . $model->getCompanyProducts() ?></div>
                </div>

                <div class="col-md-12 mt-15">
                    <div class="requirementsTable">
                        <div class="row headers">
                            <div class="col-md-3">Описание запроса</div>
                            <div class="col-md-3">Описание ожидаемого решения</div>
                            <div class="col-md-3">Причины</div>
                            <div class="col-md-3">Дополнительная информация</div>
                        </div>

                        <?php foreach ($model->requirements as $key => $requirement): ?>

                            <div class="row requirementsDataTable">
                                <div class="col-md-3">
                                    <?= '<span class="bolder">' . ($key+1) . '. </span>' . $requirement->getRequirement() ?>
                                </div>

                                <div class="col-md-3">
                                    <?= $requirement->getExpectedResult() ?>
                                </div>

                                <div class="col-md-3">
                                    <?php foreach ($requirement->reasons as $reason): ?>
                                        <div class="mb-10"> - <?= $reason->getReason() ?></div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="col-md-3">
                                    <?= $requirement->getAddInfo() ?>
                                </div>

                                <?php if (($key+1) === count($model->requirements)): ?>
                                    <div class="col-md-12">
                                <?php else: ?>
                                    <div class="col-md-12 mb-10">
                                <?php endif; ?>
                                    <span class="bolder">Актуальный запрос: </span>
                                    <span class="pl-5 isActual"><?= $requirement->getIsActualDesc() ?></span>
                                    <?php if ($model->getClientId() === $clientId): ?>
                                        <?= Html::a(' - изменить', ['/admin/wish-list/change-requirement-actual', 'id' => $requirement->getId()],
                                            ['class' => 'change-requirement-actual', 'style']
                                        ) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-12 mt-15 pl-20">
                    <div><?= '<span class="bolder">Дополнительная информация: </span>' . $model->getAddInfo() ?></div>
                    <div><?= '<span class="bolder">Сформирован: </span>' . date('d.m.Y', $model->getCompletedAt()) ?></div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

    <div class="pagination-admin-projects-result">
        <?= LinkPager::widget([
            'pagination' => $pages,
            'activePageCssClass' => 'pagination_active_page',
            'options' => ['class' => 'admin-projects-result-pagin-list'],
        ]) ?>
    </div>

<?php else: ?>

    <div class="row mt-15">
        <div class="col-md-12 text-center bolder">Ничего не найдено</div>
    </div>

<?php endif; ?>