<?php

use app\models\BusinessModel;
use app\models\Gcps;
use app\models\ProjectCommunications;
use yii\helpers\Html;
use app\models\Segments;
use app\models\User;
use app\models\EnableExpertise;
use app\models\StageExpertise;

/**
 * @var BusinessModel $model
 * @var Gcps $gcp
 * @var Segments $segment
 */

?>


<div class="row business-model-block-buttons-desktop" style="margin: 0;">

    <div class="col-lg-3" style="padding-top: 17px; padding-bottom: 17px;">
        <?= Html::a('Бизнес-модель' . Html::img('/images/icons/icon_report_next.png'), ['/business-model/get-instruction'],[
            'class' => 'link_to_instruction_page open_modal_instruction_page', 'title' => 'Инструкция'
        ]) ?>
    </div>

    <div class="col-lg-9" style="padding-top: 17px; padding-bottom: 17px;">

        <?php if (!$model->getDeletedAt()): ?>

            <?= Html::a('Скачать PDF', ['/business-model/mpdf-business-model', 'id' => $model->id],[
                'class' => 'btn btn-success pull-right',
                'title' => 'Скачать бизнес-модель',
                'target' => '_blank',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'padding' => '2px 7px 0 7px',
                    'width' => '220px',
                    'height' => '40px',
                    'border-radius' => '8px',
                    'background' => '#52BE7F',
                    'text-transform' => 'uppercase',
                    'font-size' => '16px',
                    'color' => '#FFFFFF',
                    'font-weight' => '700',
                ],
            ]) ?>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                <?= Html::a('Редактировать', ['/business-model/get-hypothesis-to-update', 'id' => $model->getId()], [
                    'class' => 'btn btn-default update-hypothesis pull-right',
                    'title' => 'Редактировать бизнес-модель',
                    'style' => [
                        'display' => 'flex',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'padding' => '2px 7px 0 7px',
                        'width' => '220px',
                        'height' => '40px',
                        'border-radius' => '8px',
                        'background' => '#7F9FC5',
                        'text-transform' => 'uppercase',
                        'font-size' => '16px',
                        'color' => '#FFFFFF',
                        'font-weight' => '700',
                    ],
                ]) ?>

                <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                    <?=  Html::a( 'Разрешить экспертизу',
                        ['/business-model/enable-expertise', 'id' => $model->getId()], [
                            'class' => 'btn btn-default link-enable-expertise pull-right',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'padding' => '2px 7px 0 7px',
                                'width' => '220px',
                                'height' => '40px',
                                'border-radius' => '8px',
                                'background' => '#4F4F4F',
                                'text-transform' => 'uppercase',
                                'font-size' => '16px',
                                'color' => '#FFFFFF',
                                'font-weight' => '700',
                            ]
                        ]) ?>

                <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                    <?=  Html::a( 'Смотреть экспертизу',
                        ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise pull-right',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'padding' => '2px 7px 0 7px',
                                'width' => '220px',
                                'height' => '40px',
                                'border-radius' => '8px',
                                'background' => '#4F4F4F',
                                'text-transform' => 'uppercase',
                                'font-size' => '16px',
                                'color' => '#FFFFFF',
                                'font-weight' => '700',
                            ],
                        ]) ?>

                <?php endif; ?>

            <?php elseif (User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                    <?=  Html::a( 'Экспертиза не разрешена', ['#'], [
                        'onclick' => 'return false;',
                        'class' => 'btn btn-default pull-right',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'padding' => '2px 7px 0 7px',
                            'width' => '220px',
                            'height' => '40px',
                            'border-radius' => '8px',
                            'background' => '#4F4F4F',
                            'text-transform' => 'uppercase',
                            'font-size' => '16px',
                            'color' => '#FFFFFF',
                            'font-weight' => '700',
                        ]
                    ]) ?>

                <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON && ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                    <?=  Html::a( 'Экспертиза',
                        ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise pull-right',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'padding' => '2px 7px 0 7px',
                                'width' => '220px',
                                'height' => '40px',
                                'border-radius' => '8px',
                                'background' => '#4F4F4F',
                                'text-transform' => 'uppercase',
                                'font-size' => '16px',
                                'color' => '#FFFFFF',
                                'font-weight' => '700',
                            ]
                        ]) ?>

                <?php endif; ?>

            <?php else : ?>

                <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                    <?=  Html::a( 'Экспертиза не разрешена', ['#'], [
                        'onclick' => 'return false;',
                        'class' => 'btn btn-default pull-right',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'padding' => '2px 7px 0 7px',
                            'width' => '220px',
                            'height' => '40px',
                            'border-radius' => '8px',
                            'background' => '#4F4F4F',
                            'text-transform' => 'uppercase',
                            'font-size' => '16px',
                            'color' => '#FFFFFF',
                            'font-weight' => '700',
                        ]
                    ]) ?>

                <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                    <?=  Html::a( 'Экспертиза',
                        ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise pull-right',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'padding' => '2px 7px 0 7px',
                                'width' => '220px',
                                'height' => '40px',
                                'border-radius' => '8px',
                                'background' => '#4F4F4F',
                                'text-transform' => 'uppercase',
                                'font-size' => '16px',
                                'color' => '#FFFFFF',
                                'font-weight' => '700',
                            ]
                        ]) ?>

                <?php endif; ?>

            <?php endif; ?>

        <?php else: ?>

            <?php if ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                <?=  Html::a( 'Смотреть экспертизу',
                    ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                        'class' => 'btn btn-default link-get-list-expertise pull-right',
                        'style' => [
                            'display' => 'flex',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'padding' => '2px 7px 0 7px',
                            'width' => '220px',
                            'height' => '40px',
                            'border-radius' => '8px',
                            'background' => '#4F4F4F',
                            'text-transform' => 'uppercase',
                            'font-size' => '16px',
                            'color' => '#FFFFFF',
                            'font-weight' => '700',
                        ],
                    ]) ?>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<div class="blocks_business_model">

    <div class="block_20_business_model">

        <div class="desc_block_20">
            <h5>Ключевые партнеры</h5>
            <div><?= $model->getPartners() ?></div>
        </div>

    </div>

    <div class="block_20_business_model">

        <div class="desc_block_20">

            <h5>Ключевые направления</h5>

            <div class="mini_header_desc_block">Тип взаимодейстивия с рынком:</div>
            <?php
            if ($segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) {
                echo 'В2С (бизнес-клиент)';
            } else {
                echo 'B2B (бизнес-бизнес)';
            }
            ?>

            <div class="mini_header_desc_block">Сфера деятельности:</div>
            <?= $segment->getFieldOfActivity() ?>

            <div class="mini_header_desc_block">Вид / специализация деятельности:</div>
            <?= $segment->getSortOfActivity() ?>

        </div>

        <div class="desc_block_20">
            <h5>Ключевые ресурсы</h5>
            <div><?= $model->getResources() ?></div>
        </div>

    </div>

    <div class="block_20_business_model">

        <div class="desc_block_20">
            <h5>Ценностное предложение</h5>
            <?= $gcp->getDescription() ?>
        </div>

    </div>

    <div class="block_20_business_model">

        <div class="desc_block_20">
            <h5>Взаимоотношения с клиентами</h5>
            <div><?= $model->getRelations() ?></div>
        </div>

        <div class="desc_block_20">
            <h5>Каналы коммуникации и сбыта</h5>
            <div><?= $model->getDistributionOfSales() ?></div>
        </div>

    </div>

    <div class="block_20_business_model">

        <div class="desc_block_20">

            <h5>Потребительский сегмент</h5>

            <div class="mini_header_desc_block">Наименование:</div>
            <?= $segment->getName() ?>

            <div class="mini_header_desc_block">Краткое описание:</div>
            <?= $segment->getDescription() ?>

            <?php if ($segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C): ?>
                <div class="mini_header_desc_block">Потенциальное количество потребителей:</div>
                <?= number_format($segment->getQuantity(), 0, '', ' ') . ' человек' ?>
            <?php else: ?>
                <div class="mini_header_desc_block">Потенциальное количество представителей сегмента:</div>
                <?= number_format($segment->getQuantity(), 0, '', ' ') . ' ед.' ?>
            <?php endif; ?>

            <div class="mini_header_desc_block">Объем рынка:</div>
            <?= number_format($segment->getMarketVolume() * 1000000, 0, '', ' ') . ' рублей' ?>

        </div>
    </div>

</div>

<div class="blocks_business_model">

    <div class="block_50_business_model">

        <div class="desc_block_50">
            <h5>Структура издержек</h5>
            <div><?= $model->getCost() ?></div>
        </div>

    </div>

    <div class="block_50_business_model">

        <div class="desc_block_50">
            <h5>Потоки поступления доходов</h5>
            <div><?= $model->getRevenue() ?></div>
        </div>

    </div>

</div>


<div class="row container-fluid blocks_business_model_mobile">

    <div class="col-xs-12 block_property_business_model">
        <div class="header_blue_property">Ключевые партнеры</div>
        <div class="content_property"><?= $model->getPartners() ?></div>
    </div>

    <div class="col-xs-12 block_property_business_model">
        <div class="header_blue_property">Ключевые направления</div>
        <div class="content_property">
            <div class="mini_header_desc_block">Тип взаимодейстивия с рынком:</div>
            <?php
            if ($segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) {
                echo 'В2С (бизнес-клиент)';
            } else {
                echo 'B2B (бизнес-бизнес)';
            }
            ?>

            <div class="mini_header_desc_block">Сфера деятельности:</div>
            <?= $segment->getFieldOfActivity() ?>

            <div class="mini_header_desc_block">Вид / специализация деятельности:</div>
            <?= $segment->getSortOfActivity() ?>
        </div>
    </div>

    <div class="col-xs-12 block_property_business_model">
        <div class="header_blue_property">Ключевые ресурсы</div>
        <div class="content_property"><?= $model->getResources() ?></div>
    </div>

    <div class="col-xs-12 block_property_business_model">
        <div class="header_blue_property">Ценностное предложение</div>
        <div class="content_property"><?= $gcp->getDescription() ?></div>
    </div>

    <div class="col-xs-12 block_property_business_model">
        <div class="header_blue_property">Взаимоотношения с клиентами</div>
        <div class="content_property"><?= $model->getRelations() ?></div>
    </div>

    <div class="col-xs-12 block_property_business_model">
        <div class="header_blue_property">Каналы коммуникации и сбыта</div>
        <div class="content_property"><?= $model->getRelations() ?></div>
    </div>

    <div class="col-xs-12 block_property_business_model">
        <div class="header_blue_property">Потребительский сегмент</div>
        <div class="content_property">
            <div>
                <span class="mini_header_desc_block">Наименование:</span>
                <?= $segment->getName() ?>
            </div>
            <div>
                <span class="mini_header_desc_block">Краткое описание:</span>
                <?= $segment->getDescription() ?>
            </div>
            <div>
                <?php if ($segment->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C): ?>
                    <span class="mini_header_desc_block">Потенциал. кол-во потребителей:</span>
                    <?= number_format($segment->getQuantity(), 0, '', ' ') . ' человек' ?>
                <?php else: ?>
                    <span class="mini_header_desc_block">Потенциал. кол-во представителей сегмента:</span>
                    <?= number_format($segment->getQuantity(), 0, '', ' ') . ' ед.' ?>
                <?php endif; ?>
            </div>
            <div>
                <span class="mini_header_desc_block">Объем рынка:</span>
                <?= number_format($segment->getMarketVolume() * 1000000, 0, '', ' ') . ' рублей' ?>
            </div>
        </div>
    </div>

    <div class="col-xs-6 block_property_business_model">
        <div class="header_red_property">Структура издержек</div>
        <div class="content_property"><?= $model->getCost() ?></div>
    </div>

    <div class="col-xs-6 block_property_business_model">
        <div class="header_green_property">Потоки поступления доходов</div>
        <div class="content_property"><?= $model->getRevenue() ?></div>
    </div>

    <div class="col-xs-12 business-model-block-buttons-mobile">

        <?php if (!$model->getDeletedAt()): ?>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>

                <div class="hypothesis_buttons_mobile">

                    <?= Html::a('Редактировать', ['/business-model/get-hypothesis-to-update', 'id' => $model->getId()], [
                        'class' => 'btn btn-default update-hypothesis',
                        'style' => [
                            'display' => 'flex',
                            'width' => '50%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 0 0 0',
                        ],
                    ]) ?>

                    <?php if ($model->getEnableExpertise() === EnableExpertise::OFF) : ?>

                        <?= Html::a('Запросить экспертизу', ['/business-model/enable-expertise', 'id' => $model->getId()], [
                            'class' => 'btn btn-default link-enable-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '50%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 0 0 0',
                            ],
                        ]) ?>

                    <?php elseif ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '50%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 0 0 0',
                            ],
                        ]) ?>

                    <?php endif; ?>

                </div>

            <?php elseif (User::isUserExpert(Yii::$app->user->identity['username'])) : ?>

                <?php if ($model->getEnableExpertise() === EnableExpertise::ON && ProjectCommunications::checkOfAccessToCarryingExpertise(Yii::$app->user->getId(), $model->getProjectId())) : ?>

                    <?=  Html::a( 'Экспертиза',
                        ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise pull-right',
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'padding' => '2px 7px 0 7px',
                                'width' => '220px',
                                'height' => '40px',
                                'border-radius' => '8px',
                                'background' => '#4F4F4F',
                                'text-transform' => 'uppercase',
                                'font-size' => '16px',
                                'color' => '#FFFFFF',
                                'font-weight' => '700',
                            ]
                        ]) ?>

                <?php endif; ?>

            <?php else : ?>

                <div class="hypothesis_buttons_mobile">

                    <?php if ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                        <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                            'class' => 'btn btn-default link-get-list-expertise',
                            'style' => [
                                'display' => 'flex',
                                'width' => '100%',
                                'height' => '36px',
                                'background' => '#4F4F4F',
                                'color' => '#FFFFFF',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'border-radius' => '0',
                                'border' => '1px solid #ffffff',
                                'font-size' => '18px',
                                'margin' => '10px 0 0 0',
                            ],
                        ]) ?>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

            <div class="hypothesis_buttons_mobile">

                <?= Html::a('Скачать PDF', ['/business-model/mpdf-business-model', 'id' => $model->id],[
                    'class' => 'btn btn-success',
                    'target' => '_blank',
                    'style' => [
                        'display' => 'flex',
                        'width' => '100%',
                        'height' => '36px',
                        'background' => '#52BE7F',
                        'color' => '#FFFFFF',
                        'align-items' => 'center',
                        'justify-content' => 'center',
                        'border-radius' => '0',
                        'border' => '1px solid #ffffff',
                        'font-size' => '18px',
                        'margin' => '10px 0 0 0',
                    ]
                ]) ?>

            </div>

        <?php else: ?>

            <div class="hypothesis_buttons_mobile">

                <?php if ($model->getEnableExpertise() === EnableExpertise::ON) : ?>

                    <?= Html::a('Смотреть экспертизу', ['/expertise/get-list', 'stage' => StageExpertise::getList()[StageExpertise::BUSINESS_MODEL], 'stageId' => $model->getId()], [
                        'class' => 'btn btn-default link-get-list-expertise',
                        'style' => [
                            'display' => 'flex',
                            'width' => '100%',
                            'height' => '36px',
                            'background' => '#4F4F4F',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 0 0 0',
                        ],
                    ]) ?>

                <?php endif; ?>

            </div>

        <?php endif; ?>
    </div>

</div>
