<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\CommunicationPatterns;
use kartik\select2\Select2;
use app\models\CommunicationTypes;

$this->title = 'Настройки коммуникаций';
$this->registerCssFile('@web/css/communication-settings-style.css');

/**
 * @var CommunicationPatterns $formPattern
 * @var array $selection_project_access_period
 * @var CommunicationPatterns[] $patternsCARCE
 * @var CommunicationPatterns[] $patternsCWRARCE
 * @var CommunicationPatterns[] $patternsCAEP
 * @var CommunicationPatterns[] $patternsCDNAEP
 * @var CommunicationPatterns[] $patternsCWEFP
 */

?>

<div class="communication-settings">

    <div class="row">
        <div class="col-md-7 header-instruction">

            <?= Html::a($this->title . Html::img('/images/icons/icon_report_next.png'), ['#'],[
                'class' => 'link_to_instruction_page open_modal_instruction_page',
                'title' => 'Инструкция', 'onclick' => 'return false'
            ]) ?>

        </div>

        <div class="col-md-5">

            <?= Html::a( 'Назначение экспертов на проекты',
                Url::to(['/admin/expertise/tasks']),[
                'class' => 'btn btn-success pull-right',
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'justify-content' => 'center',
                    'background' => '#52BE7F',
                    'width' => '100%',
                    'min-width' => '350px',
                    'max-width' => '450px',
                    'height' => '40px',
                    'font-size' => '24px',
                    'border-radius' => '8px',
                    'margin-bottom' => '15px'
                ],
            ]) ?>

        </div>
    </div>

    <div class="row">
        <div class="panel-body col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-font-size">
                    <div class="row panel-content">
                        <div class="col-xs-8 col-sm-9">Коммуникация «запрос о готовности провести экспертизу»</div>
                        <div class="col-xs-4 col-sm-3">
                            <?=  Html::a( '<div class="new_pattern_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png']) . '</div><div>Добавить шаблон</div></div>', ['#'],
                                ['id' => 'show_form_pattern_CARCE', 'class' => 'new_pattern_link_plus']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-pattern-CARCE">

                <?php $form = ActiveForm::begin([
                    'id' => 'create_pattern_CARCE',
                    'action' => Url::to([
                        '/admin/communications/create-pattern',
                        'communicationType' => CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE,
                    ]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                    <?= $form->field($formPattern, 'description', [
                        'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                    ])->textarea([
                        'rows' => 1,
                        'maxlength' => true,
                        'class' => 'style_form_field_respond form-control',
                        'placeholder' => '',
                    ]) ?>

                    <?= $form->field($formPattern, 'project_access_period', [
                        'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-2" style="margin-bottom: 15px;">{input}</div>',
                    ])->widget(Select2::class, [
                        'data' => $selection_project_access_period,
                        'options' => ['id' => 'selection_project_access_period'],
                        'disabled' => false,  //Сделать поле неактивным
                        'hideSearch' => true, //Скрытие поиска
                    ]) ?>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <?= Html::submitButton('Сохранить', [
                                    'class' => 'btn btn-success',
                                    'style' => [
                                        'display' => 'flex',
                                        'align-items' => 'center',
                                        'justify-content' => 'center',
                                        'background' => '#52BE7F',
                                        'width' => '100%',
                                        'height' => '40px',
                                        'font-size' => '24px',
                                        'border-radius' => '8px',
                                        'margin-bottom' => '15px',
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>

            <div class="all-patterns-CARCE">

                <?php if ($patternsCARCE) : ?>

                    <!--Заголовки для созданных шаблонов-->
                    <div class="row block-patterns">
                        <div class="col-xs-6 col-lg-8">Описание шаблона коммуникации</div>
                        <div class="col-xs-3 col-lg-2 text-center">Срок доступа к проекту</div>
                        <div class="col-xs-3 col-lg-2 text-center">Действия</div>
                    </div>

                    <!--Созданные шаблоны-->
                    <?php foreach ($patternsCARCE as $pattern) : ?>
                        <div class="row style-row-pattern row-pattern-<?= $pattern->getId() ?>">

                            <div class="col-xs-6 col-lg-8"><?= $pattern->getDescription() ?></div>
                            <div class="col-xs-3 col-lg-2 text-center">
                                <?php if (in_array($pattern->getProjectAccessPeriod(), [1, 21])) {
                                    echo $pattern->getProjectAccessPeriod() . ' день';
                                } elseif (in_array($pattern->getProjectAccessPeriod(), [2, 3, 4, 22, 23, 24])) {
                                    echo $pattern->getProjectAccessPeriod() . ' дня';
                                } else {
                                    echo $pattern->getProjectAccessPeriod() . ' дней';
                                } ?>
                            </div>
                            <div class="col-xs-3 col-lg-2 text-center">

                                <?php if ($pattern->getIsActive() === CommunicationPatterns::ACTIVE) : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_active.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        ['/admin/communications/deactivate-pattern', 'id' => $pattern->getId()],
                                        [
                                            'class' => 'deactivate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Отменить'
                                        ]
                                    ) ?>

                                <?php else : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        [
                                            '/admin/communications/activate-pattern', 'id' => $pattern->getId(),
                                            'communicationType' => $pattern->getCommunicationType()
                                        ],
                                        [
                                            'class' => 'activate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Применить'
                                        ]
                                    ) ?>

                                <?php endif; ?>

                                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                    [
                                        '/admin/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                                        'communicationType' => $pattern->getCommunicationType()
                                    ],
                                    [
                                        'class' => 'update-communication-pattern',
                                        'title' => 'Редактировать'
                                    ]
                                ) ?>

                                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
                                    ['/admin/communications/delete-pattern', 'id' => $pattern->getId()],
                                    [
                                        'class' => 'delete-communication-pattern',
                                        'title' => 'Удалить'
                                    ]
                                ) ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!--Шаблон по умолчанию-->
            <div class="row block-default-pattern bg-success">
                <div class="col-xs-8 col-sm-9 col-lg-10"><?= CommunicationPatterns::COMMUNICATION_DEFAULT_ABOUT_READINESS_CONDUCT_EXPERTISE ?></div>
                <div class="col-xs-4 col-sm-3 col-lg-2 text-center">По умолчанию</div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="panel-body col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-font-size">
                    <div class="row panel-content">
                        <div class="col-xs-8 col-sm-9">Коммуникация «отмена запроса о готовности провести экспертизу»</div>
                        <div class="col-xs-4 col-sm-3">
                            <?=  Html::a( '<div class="new_pattern_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png']) . '</div><div>Добавить шаблон</div></div>', ['#'],
                                ['id' => 'show_form_pattern_CWRARCE', 'class' => 'new_pattern_link_plus']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-pattern-CWRARCE">

                <?php $form = ActiveForm::begin([
                    'id' => 'create_pattern_CWRARCE',
                    'action' => Url::to([
                        '/admin/communications/create-pattern',
                        'communicationType' => CommunicationTypes::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE,
                    ]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <?= $form->field($formPattern, 'description', [
                    'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textarea([
                    'rows' => 1,
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <?= Html::submitButton('Сохранить', [
                                'class' => 'btn btn-success',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#52BE7F',
                                    'width' => '100%',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                    'margin-bottom' => '15px',
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

            <div class="all-patterns-CWRARCE">

                <?php if ($patternsCWRARCE) : ?>

                    <!--Заголовки для созданных шаблонов-->
                    <div class="row block-patterns">
                        <div class="col-xs-9 col-lg-10">Описание шаблона коммуникации</div>
                        <div class="col-xs-3 col-lg-2 text-center">Действия</div>
                    </div>

                    <!--Созданные шаблоны-->
                    <?php foreach ($patternsCWRARCE as $pattern) : ?>
                        <div class="row style-row-pattern row-pattern-<?= $pattern->getId() ?>">

                            <div class="col-xs-9 col-lg-10"><?= $pattern->getDescription() ?></div>

                            <div class="col-xs-3 col-lg-2 text-center">

                                <?php if ($pattern->getIsActive() === CommunicationPatterns::ACTIVE) : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_active.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        ['/admin/communications/deactivate-pattern', 'id' => $pattern->getId()],
                                        [
                                            'class' => 'deactivate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Отменить'
                                        ]
                                    ) ?>

                                <?php else : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        [
                                            '/admin/communications/activate-pattern', 'id' => $pattern->getId(),
                                            'communicationType' => $pattern->getCommunicationType()
                                        ],
                                        [
                                            'class' => 'activate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Применить'
                                        ]
                                    ) ?>

                                <?php endif; ?>

                                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                    [
                                        '/admin/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                                        'communicationType' => $pattern->getCommunicationType()
                                    ],
                                    [
                                        'class' => 'update-communication-pattern',
                                        'title' => 'Редактировать'
                                    ]
                                ) ?>

                                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
                                    ['/admin/communications/delete-pattern', 'id' => $pattern->getId()],
                                    [
                                        'class' => 'delete-communication-pattern',
                                        'title' => 'Удалить'
                                    ]
                                ) ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!--Шаблон по умолчанию-->
            <div class="row block-default-pattern bg-success">
                <div class="col-xs-8 col-sm-9 col-lg-10"><?= CommunicationPatterns::COMMUNICATION_DEFAULT_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE ?></div>
                <div class="col-xs-4 col-sm-3 col-lg-2 text-center">По умолчанию</div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="panel-body col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-font-size">
                    <div class="row panel-content">
                        <div class="col-xs-8 col-sm-9">Коммуникация «назначение эксперта на проект»</div>
                        <div class="col-xs-4 col-sm-3">
                            <?=  Html::a( '<div class="new_pattern_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png']) . '</div><div>Добавить шаблон</div></div>', ['#'],
                                ['id' => 'show_form_pattern_CAEP', 'class' => 'new_pattern_link_plus']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-pattern-CAEP">

                <?php $form = ActiveForm::begin([
                    'id' => 'create_pattern_CAEP',
                    'action' => Url::to([
                        '/admin/communications/create-pattern',
                        'communicationType' => CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT,
                    ]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <?= $form->field($formPattern, 'description', [
                    'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textarea([
                    'rows' => 1,
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <?= Html::submitButton('Сохранить', [
                                'class' => 'btn btn-success',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#52BE7F',
                                    'width' => '100%',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                    'margin-bottom' => '15px',
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

            <div class="all-patterns-CAEP">

                <?php if ($patternsCAEP) : ?>

                    <!--Заголовки для созданных шаблонов-->
                    <div class="row block-patterns">
                        <div class="col-xs-9 col-lg-10">Описание шаблона коммуникации</div>
                        <div class="col-xs-3 col-lg-2 text-center">Действия</div>
                    </div>

                    <!--Созданные шаблоны-->
                    <?php foreach ($patternsCAEP as $pattern) : ?>
                        <div class="row style-row-pattern row-pattern-<?= $pattern->getId() ?>">

                            <div class="col-xs-9 col-lg-10"><?= $pattern->getDescription() ?></div>

                            <div class="col-xs-3 col-lg-2 text-center">

                                <?php if ($pattern->getIsActive() === CommunicationPatterns::ACTIVE) : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_active.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        ['/admin/communications/deactivate-pattern', 'id' => $pattern->getId()],
                                        [
                                            'class' => 'deactivate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Отменить'
                                        ]
                                    ) ?>

                                <?php else : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        [
                                            '/admin/communications/activate-pattern', 'id' => $pattern->getId(),
                                            'communicationType' => $pattern->getCommunicationType()
                                        ],
                                        [
                                            'class' => 'activate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Применить'
                                        ]
                                    ) ?>

                                <?php endif; ?>

                                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                    [
                                        '/admin/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                                        'communicationType' => $pattern->getCommunicationType()
                                    ],
                                    [
                                        'class' => 'update-communication-pattern',
                                        'title' => 'Редактировать'
                                    ]
                                ) ?>

                                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
                                    ['/admin/communications/delete-pattern', 'id' => $pattern->getId()],
                                    [
                                        'class' => 'delete-communication-pattern',
                                        'title' => 'Удалить'
                                    ]
                                ) ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!--Шаблон по умолчанию-->
            <div class="row block-default-pattern bg-success">
                <div class="col-xs-8 col-sm-9 col-lg-10"><?= CommunicationPatterns::COMMUNICATION_DEFAULT_APPOINTS_EXPERT_PROJECT ?></div>
                <div class="col-xs-4 col-sm-3 col-lg-2 text-center">По умолчанию</div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="panel-body col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-font-size">
                    <div class="row panel-content">
                        <div class="col-xs-8 col-sm-9">Коммуникация «отказ эксперту в назначении на проект»</div>
                        <div class="col-xs-4 col-sm-3">
                            <?=  Html::a( '<div class="new_pattern_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png']) . '</div><div>Добавить шаблон</div></div>', ['#'],
                                ['id' => 'show_form_pattern_CDNAEP', 'class' => 'new_pattern_link_plus']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-pattern-CDNAEP">

                <?php $form = ActiveForm::begin([
                    'id' => 'create_pattern_CDNAEP',
                    'action' => Url::to([
                        '/admin/communications/create-pattern',
                        'communicationType' => CommunicationTypes::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT,
                    ]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <?= $form->field($formPattern, 'description', [
                    'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textarea([
                    'rows' => 1,
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <?= Html::submitButton('Сохранить', [
                                'class' => 'btn btn-success',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#52BE7F',
                                    'width' => '100%',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                    'margin-bottom' => '15px',
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

            <div class="all-patterns-CDNAEP">

                <?php if ($patternsCDNAEP) : ?>

                    <!--Заголовки для созданных шаблонов-->
                    <div class="row block-patterns">
                        <div class="col-xs-9 col-lg-10">Описание шаблона коммуникации</div>
                        <div class="col-xs-3 col-lg-2 text-center">Действия</div>
                    </div>

                    <!--Созданные шаблоны-->
                    <?php foreach ($patternsCDNAEP as $pattern) : ?>
                        <div class="row style-row-pattern row-pattern-<?= $pattern->getId() ?>">

                            <div class="col-xs-9 col-lg-10"><?= $pattern->getDescription() ?></div>

                            <div class="col-xs-3 col-lg-2 text-center">

                                <?php if ($pattern->getIsActive() === CommunicationPatterns::ACTIVE) : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_active.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        ['/admin/communications/deactivate-pattern', 'id' => $pattern->getId()],
                                        [
                                            'class' => 'deactivate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Отменить'
                                        ]
                                    ) ?>

                                <?php else : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        [
                                            '/admin/communications/activate-pattern', 'id' => $pattern->getId(),
                                            'communicationType' => $pattern->getCommunicationType()
                                        ],
                                        [
                                            'class' => 'activate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Применить'
                                        ]
                                    ) ?>

                                <?php endif; ?>

                                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                    [
                                        '/admin/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                                        'communicationType' => $pattern->getCommunicationType()
                                    ],
                                    [
                                        'class' => 'update-communication-pattern',
                                        'title' => 'Редактировать'
                                    ]
                                ) ?>

                                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
                                    ['/admin/communications/delete-pattern', 'id' => $pattern->getId()],
                                    [
                                        'class' => 'delete-communication-pattern',
                                        'title' => 'Удалить'
                                    ]
                                ) ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!--Шаблон по умолчанию-->
            <div class="row block-default-pattern bg-success">
                <div class="col-xs-8 col-sm-9 col-lg-10"><?= CommunicationPatterns::COMMUNICATION_DEFAULT_DOES_NOT_APPOINTS_EXPERT_PROJECT ?></div>
                <div class="col-xs-4 col-sm-3 col-lg-2 text-center">По умолчанию</div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="panel-body col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading panel-heading-font-size">
                    <div class="row panel-content">
                        <div class="col-xs-8 col-sm-9">Коммуникация «отмена назначения эксперта на проект»</div>
                        <div class="col-xs-4 col-sm-3">
                            <?=  Html::a( '<div class="new_pattern_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png']) . '</div><div>Добавить шаблон</div></div>', ['#'],
                                ['id' => 'show_form_pattern_CWEFP', 'class' => 'new_pattern_link_plus']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-pattern-CWEFP">

                <?php $form = ActiveForm::begin([
                    'id' => 'create_pattern_CWEFP',
                    'action' => Url::to([
                        '/admin/communications/create-pattern',
                        'communicationType' => CommunicationTypes::MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT,
                    ]),
                    'options' => ['class' => 'g-py-15'],
                    'errorCssClass' => 'u-has-error-v1',
                    'successCssClass' => 'u-has-success-v1-1',
                ]); ?>

                <?= $form->field($formPattern, 'description', [
                    'template' => '<div class="col-md-12" style="padding-left: 30px;">{label}</div><div class="col-md-12" style="margin-bottom: 15px;">{input}</div>'
                ])->textarea([
                    'rows' => 1,
                    'maxlength' => true,
                    'class' => 'style_form_field_respond form-control',
                    'placeholder' => '',
                ]) ?>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <?= Html::submitButton('Сохранить', [
                                'class' => 'btn btn-success',
                                'style' => [
                                    'display' => 'flex',
                                    'align-items' => 'center',
                                    'justify-content' => 'center',
                                    'background' => '#52BE7F',
                                    'width' => '100%',
                                    'height' => '40px',
                                    'font-size' => '24px',
                                    'border-radius' => '8px',
                                    'margin-bottom' => '15px',
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

            <div class="all-patterns-CWEFP">

                <?php if ($patternsCWEFP) : ?>

                    <!--Заголовки для созданных шаблонов-->
                    <div class="row block-patterns">
                        <div class="col-xs-9 col-lg-10">Описание шаблона коммуникации</div>
                        <div class="col-xs-3 col-lg-2 text-center">Действия</div>
                    </div>

                    <!--Созданные шаблоны-->
                    <?php foreach ($patternsCWEFP as $pattern) : ?>
                        <div class="row style-row-pattern row-pattern-<?= $pattern->getId() ?>">

                            <div class="col-xs-9 col-lg-10"><?= $pattern->getDescription() ?></div>

                            <div class="col-xs-3 col-lg-2 text-center">

                                <?php if ($pattern->getIsActive() === CommunicationPatterns::ACTIVE) : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_active.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        ['/admin/communications/deactivate-pattern', 'id' => $pattern->getId()],
                                        [
                                            'class' => 'deactivate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Отменить'
                                        ]
                                    ) ?>

                                <?php else : ?>

                                    <?= Html::a(Html::img('/images/icons/icon_circle_default.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                        [
                                            '/admin/communications/activate-pattern', 'id' => $pattern->getId(),
                                            'communicationType' => $pattern->getCommunicationType()
                                        ],
                                        [
                                            'class' => 'activate-communication-pattern',
                                            'style' => ['margin-left' => '30px'],
                                            'title' => 'Применить'
                                        ]
                                    ) ?>

                                <?php endif; ?>

                                <?= Html::a(Html::img('/images/icons/icon_update.png', ['style' => ['width' => '24px', 'margin-right' => '20px']]),
                                    [
                                        '/admin/communications/get-form-update-communication-pattern', 'id' => $pattern->getId(),
                                        'communicationType' => $pattern->getCommunicationType()
                                    ],
                                    [
                                        'class' => 'update-communication-pattern',
                                        'title' => 'Редактировать'
                                    ]
                                ) ?>

                                <?= Html::a(Html::img('/images/icons/icon_delete.png', ['style' => ['width' => '24px']]),
                                    ['/admin/communications/delete-pattern', 'id' => $pattern->getId()],
                                    [
                                        'class' => 'delete-communication-pattern',
                                        'title' => 'Удалить'
                                    ]
                                ) ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!--Шаблон по умолчанию-->
            <div class="row block-default-pattern bg-success">
                <div class="col-xs-8 col-sm-9 col-lg-10"><?= CommunicationPatterns::COMMUNICATION_DEFAULT_WITHDRAWS_EXPERT_FROM_PROJECT ?></div>
                <div class="col-xs-4 col-sm-3 col-lg-2 text-center">По умолчанию</div>
            </div>

        </div>
    </div>

</div>


<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/communication_settings.js'); ?>
