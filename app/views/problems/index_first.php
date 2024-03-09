<?php

use app\models\ConfirmSegment;
use app\models\forms\FormCreateProblem;
use app\models\StageExpertise;
use yii\helpers\Html;
use app\models\User;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Генерация гипотез проблем сегмента';
$this->registerCssFile('@web/css/problem-index-style.css');
$this->registerCssFile('@web/css/methodological-guide-style.css');

/**
 * @var ConfirmSegment $confirmSegment
 * @var FormCreateProblem $formModel
 */

$project = $confirmSegment->hypothesis->project;
?>

<div class="generation-problem-index">

    <?php if (!User::isUserAdmin(Yii::$app->user->identity['username'])) : ?>

        <div class="methodological-guide">

            <div class="header_hypothesis_first_index">Генерация гипотез проблем сегментов</div>

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
                            'margin' => '10px 1% 10px 2%',
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
                            'margin' => '10px 2% 10px 1%',
                        ],
                    ]) ?>

                </div>
            </div>

            <div class="arrow_stages_project_mobile">
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage active"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
                <div class="item-stage passive"></div>
            </div>

            <div class="arrow_links_router_mobile">
                <div class="arrow_link_router_mobile_left">
                    <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                        Url::to(['/confirm-segment/view', 'id' => $confirmSegment->getId()])) ?>
                </div>
                <div class="text-stage">3/9. Генерация гипотез проблем сегментов</div>
                <div class="arrow_link_router_mobile_right">
                    <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
                </div>
            </div>

            <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>
                <div class="row container-fluid block-button-new-problem">
                    <div class="col-md-12">
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Добавить проблему</div></div>',
                            ['/confirm-segment/data-availability-for-next-step', 'id' => $confirmSegment->getId()],
                            ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus pull-left']
                        ) ?>
                    </div>
                    <div class="col-md-12">
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div class="pl-20">Добавить задание исполнителю</div></div>', [
                            '/tasks/get-task-create', 'projectId' => $project->getId(), 'stage' => StageExpertise::PROBLEM, 'stageId' => $confirmSegment->getId()],
                            ['id' => 'showFormContractorTaskCreate', 'class' => 'new_hypothesis_link_plus pull-left']
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="container-list">

                <div class="simple-block">
                    <p>
                        <span>Задача:</span>
                        Сформировать список гипотез, которые должны быть протестированы на следующей стадии.
                    </p>
                    <p>
                        <span>Результат:</span>
                        Одна или несколько сформулированных и проверенных гипотез, внесенные в форму на платформе Spaccel.ru.
                    </p>
                </div>

                <br>

                <p>
                    Исходной информацией для формулировки гипотез проблем сегментов является информационная таблица, где собраны
                    все «Варианты проблем» по материалам всех респондентов. Перед формулировкой гипотезы проблемы проанализируйте
                    результаты, собранные в поле «Варианты проблем». Для формулировки гипотез проблем необходимо использовать
                    удобный интерфейс spaccel.
                </p>

                <p>
                    Обычно формулируют несколько гипотез проблем. В этом случае можно поручить дальнейшее тестирование нескольким
                    членам вашей команды: по одной гипотезе проблемы – одному человеку. Таким образом, можно распараллелить работы.
                </p>

                <div class="bold">Общие рекомендации как анализировать результаты ГИ:</div>

                <div class="container-text">
                    <ol>
                        <li><span class="pl-15">Сводим все в таблицу и смотрим, где есть схожие и противоположные ответы на вопросы.</span></li>
                        <li><span class="pl-15">Ищем одинаковые. Тогда мы выясняем, есть ли у пользователей схожая проблема.</span></li>
                        <li><span class="pl-15">Если в процессе проведения интервью с одним респондентом выявили проблему, то желательно включить в сценарий проведения с другими респондентами вопрос, есть ли у них выявленная проблема.</span></li>
                        <li>
                            <span class="pl-15">Помните, что проблема может лежать в другой плоскости и не в том месте, про которое спрашивали.</span>
                            <p class="mt-10 pl-25">
                                <span class="bold">Пример:</span>
                                Есть стартап, который помогает делать on-line трансляции в интернете. Это делают, потому что есть ожидание,
                                что их никто не умеет делать технически. Поэтому их мало. Но реальность – нет аудитории. Нет интереса.
                                А проблема в том, что просто не умеют привлечь аудиторию.
                            </p>
                        </li>
                        <li>
                            <span class="pl-15">Может быть выявлена проблема, связанная с тем, что у собеседников разный лексикон. Т.е. иногда будущий пользователь системы не может правильно определить, что ему нужно, потому что он не владеет лексиконом.</span>
                            <p class="mt-10 pl-25">
                                <span class="bold">Пример:</span>
                                Пользователям не нужна CRM система, а нужна система, как они говорят, учет продаж,  что у них с воронкой продаж,
                                контроль менеджеров. Т.е. они называют CRM другими словами.
                            </p>
                        </li>
                    </ol>
                </div>

                <p class="bold">
                    Сформулируйте гипотезы проблемы для проверки. Правильная гипотеза (потребности, проблемы) состоит из: предположения,
                    действия для проверки, метрики результата и вывода.
                </p>

                <p class="bold">Примеры:</p>

                <p>
                    <span class="bold">Сетевой магазин.</span>
                    Гипотеза: минимум 20% офисов продаж узнают о новых акциях с задержкой больше трех дней, либо вообще не узнают — из-за
                    этого продажи не растут, либо падают. Чтобы найти решение, мы проведем интервью с менеджерами из десяти точек продаж
                    в одном регионе. Если семеро и больше подтвердят гипотезу, то сделаем пилотный запуск системы быстрого оповещения точек
                    продаж об акциях через мессенджер, после чего проверим, как изменился уровень продаж.
                </p>

                <p>
                    <span class="bold">Магазин книг на английском языке.</span>
                    Гипотеза: читатели испытывают трудности при заказе книг на Amazon из-за долгой и дорогой доставки. Мы проведем опрос
                    среди людей, которые читают на английском. Если больше 70% ответят о долгой и/или дорогой доставке книг с Amazon, гипотезу
                    считаем подтвержденной и запускаем тестовую рекламную кампанию на сайт по продаже книг с быстрой и недорогой доставкой книг
                    в оригинале.
                </p>

                <p class="bold">
                    Важно! Если вы видите, что обозначенные респондентами потребности и проблемы отличаются, значит, выбранный вами сегмент имеет
                    слишком широкий охват и его необходимо сузить. Выделить какие-то более мелкие группы по возрасту, по профессии, соц. статусу,
                    по образованию и т.д.
                </p>

                <p class="bold">
                    Генерируйте новые сегменты до тех пор, пока при тестировании (валидации) не увидите в них (респондентах) общие черты.
                </p>

            </div>

        </div>

        <div class="formExpectedResults" style="display: none;">

            <?php
            $form = ActiveForm::begin([
                'id' => 'formExpectedResults'
            ]); ?>

            <div class="formExpectedResults_inputs">

                <div class="row container-fluid rowExpectedResults rowExpectedResults-" style="margin-bottom: 15px;">

                    <div class="col-xs-6 field-EXR">

                        <?= $form->field($formModel, "_expectedResultsInterview[0][question]", ['template' => '{input}'])->textarea([
                            'rows' => 3,
                            'maxlength' => true,
                            'required' => true,
                            'placeholder' => 'Вопрос',
                            'id' => '_expectedResults_question-',
                            'class' => 'style_form_field_respond form-control',
                        ]) ?>

                    </div>

                    <div class="col-xs-6 field-EXR">

                        <?= $form->field($formModel, "_expectedResultsInterview[0][answer]", ['template' => '{input}'])->textarea([
                            'rows' => 3,
                            'maxlength' => true,
                            'required' => true,
                            'placeholder' => 'Ответ',
                            'id' => '_expectedResults_answer-',
                            'class' => 'style_form_field_respond form-control',
                        ]) ?>

                    </div>

                    <div class="col-xs-12">

                        <?= Html::button('Удалить вопрос', [
                            'id' => 'remove-expectedResults-',
                            'class' => "remove-expectedResults btn btn-default",
                            'style' => [
                                'display' => 'flex',
                                'align-items' => 'center',
                                'justify-content' => 'center',
                                'width' => '180px',
                                'height' => '40px',
                                'font-size' => '16px',
                                'border-radius' => '8px',
                                'text-transform' => 'uppercase',
                                'font-weight' => '700',
                                'padding-top' => '9px'
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
            <?php
            ActiveForm::end();
            ?>
        </div>

        <!--Модальные окна-->
        <?= $this->render('modal') ?>

    <?php else : ?>

        <div class="methodological-guide">

            <h3 class="header-text"><span>Генерация гипотез проблем сегментов</span></h3>

            <div class="container-list">

                <div class="simple-block">
                    <p>
                        <span>Задача:</span>
                        Проверить на соответствие рекомендациям заполненной формы <span>Проблема сегмента.</span>
                    </p>
                    <p>
                        <span>Результат:</span>
                        Проектант получил необходимые рекомендации.
                    </p>
                </div>

                <div class="bold">Рекомендовать проектантам:</div>
                <div class="container-text">
                    <ul>
                        <li class="pl-15">Рекомендовать генерировать проблемы на основе повторяющихся (из интервью к интервью) предполагаемых проблем.</li>
                        <li class="pl-15">Проверить соответствие формулировки проблемы заданной структуре формулы проблемы.</li>
                        <li class="pl-15">Предложить сгенерить, по возможности, больше одной проблемы.</li>
                    </ul>
                </div>

                <h4><span class="bold"><u>Информация, полученная Проектантом:</u></span></h4>

                <div class="simple-block">
                    <p>
                        <span>Задача:</span>
                        Сформировать список гипотез, которые должны быть протестированы на следующей стадии.
                    </p>
                    <p>
                        <span>Результат:</span>
                        Одна или несколько сформулированных и проверенных гипотез, внесенные в форму на платформе Spaccel.ru.
                    </p>
                </div>

                <br>

                <p>
                    Исходной информацией для формулировки гипотез проблем сегментов является информационная таблица, где собраны
                    все «Варианты проблем» по материалам всех респондентов. Перед формулировкой гипотезы проблемы проанализируйте
                    результаты, собранные в поле «Варианты проблем». Для формулировки гипотез проблем необходимо использовать
                    удобный интерфейс spaccel.
                </p>

                <p>
                    Обычно формулируют несколько гипотез проблем. В этом случае можно поручить дальнейшее тестирование нескольким
                    членам вашей команды: по одной гипотезе проблемы – одному человеку. Таким образом, можно распараллелить работы.
                </p>

                <div class="bold">Общие рекомендации как анализировать результаты ГИ:</div>

                <div class="container-text">
                    <ol>
                        <li><span class="pl-15">Сводим все в таблицу и смотрим, где есть схожие и противоположные ответы на вопросы.</span></li>
                        <li><span class="pl-15">Ищем одинаковые. Тогда мы выясняем, есть ли у пользователей схожая проблема.</span></li>
                        <li><span class="pl-15">Если в процессе проведения интервью с одним респондентом выявили проблему, то желательно включить в сценарий проведения с другими респондентами вопрос, есть ли у них выявленная проблема.</span></li>
                        <li>
                            <span class="pl-15">Помните, что проблема может лежать в другой плоскости и не в том месте, про которое спрашивали.</span>
                            <p class="mt-10 pl-25">
                                <span class="bold">Пример:</span>
                                Есть стартап, который помогает делать on-line трансляции в интернете. Это делают, потому что есть ожидание,
                                что их никто не умеет делать технически. Поэтому их мало. Но реальность – нет аудитории. Нет интереса.
                                А проблема в том, что просто не умеют привлечь аудиторию.
                            </p>
                        </li>
                        <li>
                            <span class="pl-15">Может быть выявлена проблема, связанная с тем, что у собеседников разный лексикон. Т.е. иногда будущий пользователь системы не может правильно определить, что ему нужно, потому что он не владеет лексиконом.</span>
                            <p class="mt-10 pl-25">
                                <span class="bold">Пример:</span>
                                Пользователям не нужна CRM система, а нужна система, как они говорят, учет продаж,  что у них с воронкой продаж,
                                контроль менеджеров. Т.е. они называют CRM другими словами.
                            </p>
                        </li>
                    </ol>
                </div>

                <p class="bold">
                    Сформулируйте гипотезы проблемы для проверки. Правильная гипотеза (потребности, проблемы) состоит из: предположения,
                    действия для проверки, метрики результата и вывода.
                </p>

                <p class="bold">Примеры:</p>

                <p>
                    <span class="bold">Сетевой магазин.</span>
                    Гипотеза: минимум 20% офисов продаж узнают о новых акциях с задержкой больше трех дней, либо вообще не узнают — из-за
                    этого продажи не растут, либо падают. Чтобы найти решение, мы проведем интервью с менеджерами из десяти точек продаж
                    в одном регионе. Если семеро и больше подтвердят гипотезу, то сделаем пилотный запуск системы быстрого оповещения точек
                    продаж об акциях через мессенджер, после чего проверим, как изменился уровень продаж.
                </p>

                <p>
                    <span class="bold">Магазин книг на английском языке.</span>
                    Гипотеза: читатели испытывают трудности при заказе книг на Amazon из-за долгой и дорогой доставки. Мы проведем опрос
                    среди людей, которые читают на английском. Если больше 70% ответят о долгой и/или дорогой доставке книг с Amazon, гипотезу
                    считаем подтвержденной и запускаем тестовую рекламную кампанию на сайт по продаже книг с быстрой и недорогой доставкой книг
                    в оригинале.
                </p>

                <p class="bold">
                    Важно! Если вы видите, что обозначенные респондентами потребности и проблемы отличаются, значит, выбранный вами сегмент имеет
                    слишком широкий охват и его необходимо сузить. Выделить какие-то более мелкие группы по возрасту, по профессии, соц. статусу,
                    по образованию и т.д.
                </p>

                <p class="bold">
                    Генерируйте новые сегменты до тех пор, пока при тестировании (валидации) не увидите в них (респондентах) общие черты.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/hypothesis_problem_index.js'); ?>