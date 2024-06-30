<?php

use app\models\ConfirmGcp;
use app\models\StageExpertise;
use yii\helpers\Html;
use app\models\User;
use yii\helpers\Url;

$this->title = 'Разработка MVP';
$this->registerCssFile('@web/css/mvp-index-style.css');
$this->registerCssFile('@web/css/methodological-guide-style.css');

/**
 * @var ConfirmGcp $confirmGcp
 */

$project = $confirmGcp->hypothesis->project;

?>

<div class="mvp-index">

    <?php if (!User::isUserAdmin(Yii::$app->user->identity['username'])) : ?>

        <div class="methodological-guide">

            <div class="header_hypothesis_first_index">Разработка MVP</div>

            <div class="header-title-index-mobile">
                <div style="overflow: hidden; max-width: 70%;">Проект: <?= $project->getProjectName() ?></div>
                <div class="buttons-project-menu-mobile" style="position: absolute; right: 20px; top: 5px;">
                    <?= Html::img('@web/images/icons/icon-four-white-squares.png', ['class' => 'open-project-menu-mobile', 'style' => ['width' => '30px']]) ?>
                    <?= Html::img('@web/images/icons/icon-white-cross.png', ['class' => 'close-project-menu-mobile', 'style' => ['width' => '30px', 'display' => 'none']]) ?>
                </div>
            </div>

            <div class="project-menu-mobile">
                <div class="project_buttons_mobile">

                    <?= Html::a('Сводная таблица', ['/projects/result-mobile', 'id' => $project->getId()], [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'width' => '47%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 1% 0 2%',
                        ],
                    ]) ?>

                    <?= Html::a('Трэкшн карта', ['/projects/roadmap-mobile', 'id' => $project->getId()], [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'width' => '47%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 2% 0 1%',
                        ],
                    ]) ?>

                </div>

                <div class="project_buttons_mobile">

                    <?= Html::a('Протокол', ['/projects/report-mobile', 'id' => $project->getId()], [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'width' => '47%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 1% 0 2%',
                        ],
                    ]) ?>

                    <?= Html::a('Презентация', ['/projects/presentation-mobile', 'id' => $project->getId()], [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'width' => '47%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 2% 0 1%',
                        ],
                    ]) ?>

                </div>

                <div class="project_buttons_mobile">

                    <?= Html::a('Экспорт в Excel', ['/export-to-excel/project', 'id' => $project->getId()], [
                        'class' => 'btn btn-default',
                        'style' => [
                            'display' => 'flex',
                            'width' => '47%',
                            'height' => '36px',
                            'background' => '#7F9FC5',
                            'color' => '#FFFFFF',
                            'align-items' => 'center',
                            'justify-content' => 'center',
                            'border-radius' => '0',
                            'border' => '1px solid #ffffff',
                            'font-size' => '18px',
                            'margin' => '10px 1% 10px 2%',
                        ],
                    ]) ?>

                </div>

            </div>

            <div class="arrow_stages_project_mobile">
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage active"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
            </div>

            <div class="arrow_links_router_mobile">
                <div class="arrow_link_router_mobile_left">
                    <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                        Url::to(['/confirm-gcp/view', 'id' => $confirmGcp->getId()])) ?>
                </div>
                <div class="text-stage">7/9. Разработка MVP</div>
                <div class="arrow_link_router_mobile_right">
                    <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
                </div>
            </div>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>
                <div class="row container-fluid block-button-new-mvp">
                    <div class="col-md-12">
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Добавить продукт MVP</div></div>',
                            ['/confirm-gcp/data-availability-for-next-step', 'id' => $confirmGcp->getId()],
                            ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus pull-left']
                        ) ?>
                    </div>
                    <div class="col-md-12">
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Добавить задание исполнителю</div></div>', [
                            '/tasks/get-task-create', 'projectId' => $project->getId(), 'stage' => StageExpertise::MVP, 'stageId' => $confirmGcp->getId()],
                            ['id' => 'showFormContractorTaskCreate', 'class' => 'new_hypothesis_link_plus pull-left']
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="container-list">

                <p>
                    Концепция MVP (Minimum Viable Product) применяется, чтобы минимизировать рыночные риски и используется для создания любого типа продукта.
                    MVP определяют, как результат «синхронной разработки» — одновременного развития продукта и исследования целевой аудитории, ее реакции на продукт.
                </p>

                <p>
                    MVP — это не прототип. Минимально жизнеспособный продукт содержит только самую необходимую функциональность. MVP создается не для тестирования технологий,
                    а для того, чтобы проверить на практике, нужен ли пользователям такой продукт, верны ли гипотезы, лежащие в основе бизнес-модели.
                </p>

                <div>Таким образом, минимально жизнеспособный продукт позволяет:</div>
                <ul>
                    <li class="pl-15">подтвердить жизнеспособность идеи и проверить гипотезы о продукте с помощью реальных данных;</li>
                    <li class="pl-15">выявить тенденции, которые можно использовать при разработке полной версии продукта;</li>
                    <li class="pl-15">снизить риск крупных финансовых потерь в случае выпуска неудачного продукта;</li>
                    <li class="pl-15">сократить стоимость разработки за счет приоритизации важных и выявления невостребованных функций;</li>
                    <li class="pl-15">ускорить поиск ошибок и внутреннее тестирование продукта;</li>
                    <li class="pl-15">собрать базу пользователей перед полномасштабным запуском;</li>
                    <li class="pl-15">занять рыночную нишу и привлечь инвесторов раньше конкурентов.</li>
                </ul>

                <p>
                    Чтобы создать минимально жизнеспособный продукт, необходимо пройти через несколько подготовительных этапов. Первые четыре шага нацелены на предварительное
                    уточнение бизнес-идеи. Пятый и шестой этапы касаются проектирования продукта, и только на седьмом и восьмом пунктах дело дойдет непосредственно до разработки и тестирования.
                </p>

                <div class="pl-15">A.	Сформулируйте задачу,</div>
                <div class="pl-15">B.	Определите аудиторию, для кого этот MVP предназначен,</div>
                <div class="pl-15">C.	Изучите конкурентов,</div>
                <div class="pl-15">D.	Выделите основные функции для реализации и рассчитайте объем MVP,</div>
                <div class="pl-15">E.	Выберите подходящую методологию и разработайте MVP.</div>

                <br>

                <p>Шаги с A до D выполнены на предыдущих этапах и результаты этих этапов должны быть использованы в разработке MVP.</p>

                <div>
                    MVP может быть выполнен в виде презентации, функционального макета, программного обеспечения, опытного образца, видео. В данном случае, мы несколько упрощаем понятие MVP,
                    т.к. в отдельных случаях не всегда удается выполнить полноценный действующий MVP. Однако хорошая презентация может достаточно убедительно продемонстрировать работоспособность
                    ключевых разработанных функций перспективного продукта.
                </div>

            </div>

        </div>

        <!--Модальные окна-->
        <?= $this->render('modal') ?>

    <?php else : ?>

        <div class="methodological-guide">

            <h3 class="header-text"><span>Разработка MVP</span></h3>

            <div class="container-list">

                <div class="simple-block">
                    <p>
                        <span>Задача:</span>
                        Проверить на соответствие рекомендациям заполненной формы <span>MVP.</span>
                    </p>
                    <p>
                        <span>Результат:</span>
                        Проектант получил необходимые рекомендации.
                    </p>
                </div>

                <div class="bold">Рекомендовать проектантам:</div>
                <div class="container-text">
                    <ul>
                        <li class="pl-15">Разработать MVP на основе РИД и технологии, заявленных как базовые, а также на базе разработанного и подтвержденного Ценностного предложения.</li>
                        <li class="pl-15">Проверить соответствие разработанного MVP заданной формулировке.</li>
                        <li class="pl-15">Предложить разработать, по возможности, больше одного MVP.</li>
                    </ul>
                </div>

                <h4><span class="bold"><u>Информация, полученная Проектантом:</u></span></h4>

                <p>
                    Концепция MVP (Minimum Viable Product) применяется, чтобы минимизировать рыночные риски и используется для создания любого типа продукта.
                    MVP определяют, как результат «синхронной разработки» — одновременного развития продукта и исследования целевой аудитории, ее реакции на продукт.
                </p>

                <p>
                    MVP — это не прототип. Минимально жизнеспособный продукт содержит только самую необходимую функциональность. MVP создается не для тестирования технологий,
                    а для того, чтобы проверить на практике, нужен ли пользователям такой продукт, верны ли гипотезы, лежащие в основе бизнес-модели.
                </p>

                <div>Таким образом, минимально жизнеспособный продукт позволяет:</div>
                <ul>
                    <li class="pl-15">подтвердить жизнеспособность идеи и проверить гипотезы о продукте с помощью реальных данных;</li>
                    <li class="pl-15">выявить тенденции, которые можно использовать при разработке полной версии продукта;</li>
                    <li class="pl-15">снизить риск крупных финансовых потерь в случае выпуска неудачного продукта;</li>
                    <li class="pl-15">сократить стоимость разработки за счет приоритизации важных и выявления невостребованных функций;</li>
                    <li class="pl-15">ускорить поиск ошибок и внутреннее тестирование продукта;</li>
                    <li class="pl-15">собрать базу пользователей перед полномасштабным запуском;</li>
                    <li class="pl-15">занять рыночную нишу и привлечь инвесторов раньше конкурентов.</li>
                </ul>

                <p>
                    Чтобы создать минимально жизнеспособный продукт, необходимо пройти через несколько подготовительных этапов. Первые четыре шага нацелены на предварительное
                    уточнение бизнес-идеи. Пятый и шестой этапы касаются проектирования продукта, и только на седьмом и восьмом пунктах дело дойдет непосредственно до разработки и тестирования.
                </p>

                <div class="pl-15">A.	Сформулируйте задачу,</div>
                <div class="pl-15">B.	Определите аудиторию, для кого этот MVP предназначен,</div>
                <div class="pl-15">C.	Изучите конкурентов,</div>
                <div class="pl-15">D.	Выделите основные функции для реализации и рассчитайте объем MVP,</div>
                <div class="pl-15">E.	Выберите подходящую методологию и разработайте MVP.</div>

                <br>

                <p>Шаги с A до D выполнены на предыдущих этапах и результаты этих этапов должны быть использованы в разработке MVP.</p>

                <div>
                    MVP может быть выполнен в виде презентации, функционального макета, программного обеспечения, опытного образца, видео. В данном случае, мы несколько упрощаем понятие MVP,
                    т.к. в отдельных случаях не всегда удается выполнить полноценный действующий MVP. Однако хорошая презентация может достаточно убедительно продемонстрировать работоспособность
                    ключевых разработанных функций перспективного продукта.
                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/hypothesis_mvp_index.js'); ?>
