<?php

use app\models\ConfirmMvp;
use yii\helpers\Html;
use app\models\User;
use yii\helpers\Url;

$this->title = 'Генерация бизнес-модели';
$this->registerCssFile('@web/css/business-model-index-style.css');
$this->registerCssFile('@web/css/methodological-guide-style.css');

/**
 * @var ConfirmMvp $confirmMvp
 */

$project = $confirmMvp->hypothesis->project;

?>

<div class="business-model-index">

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
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage passive"></div>
        <div class="item-stage active"></div>
    </div>

    <div class="arrow_links_router_mobile">
        <div class="arrow_link_router_mobile_left">
            <?= Html::a(Html::img('@web/images/icons/arrow_left_active.png'),
                Url::to(['/confirm-mvp/view', 'id' => $confirmMvp->getId()])) ?>
        </div>
        <div class="text-stage">9/9. Разработка бизнес-модели</div>
        <div class="arrow_link_router_mobile_right">
            <?= Html::img('@web/images/icons/arrow_left_passive.png') ?>
        </div>
    </div>

    <?php if (!User::isUserAdmin(Yii::$app->user->identity['username'])) : ?>

        <div class="methodological-guide">

            <div class="header_hypothesis_first_index">Разработка бизнес-модели</div>

            <div class="row container-fluid">
                <div class="col-md-12">
                    <?php if (User::isUserSimple(Yii::$app->user->identity['username'])) : ?>
                        <?=  Html::a( '<div class="new_hypothesis_link_block"><div>' . Html::img(['@web/images/icons/add_vector.png'], ['style' => ['width' => '35px']]) . '</div><div style="padding-left: 20px;">Бизнес-модель</div></div>',
                                ['/confirm-mvp/data-availability-for-next-step', 'id' => $confirmMvp->getId()],
                                ['id' => 'checking_the_possibility', 'class' => 'new_hypothesis_link_plus pull-left']
                            ) ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="container-list">

                <p>Целью, а также заключительным этапом работы в рамках нашего Акселератора является генерация Бизнес-модели. Мы предлагаем
                    использовать Канву бизнес-модели (англ. Business model canvas), разработанную авторами Александром Остервальдером и Ивом Пинье.</p>

                <p>Канва бизнес-модели состоит из 9 блоков, которые могут быть объединены в 4 группы, каждый из блоков описывает
                    свою часть бизнес-модели организации, а именно: ключевые партнеры, ключевые активности, достоинства и предложения,
                    отношения с заказчиком, пользовательские сегменты, ключевые ресурсы, каналы поставки, структура затрат и источники доходов.</p>

                <p>На основании ранее заполненных форм система автоматически сгенерирует вариант Бизнес-модели,
                    который при необходимости вы сможете отредактировать.</p>

                <p>Наличие решения к цепочке «Клиент – Проблема – Ценностное предложение» означает, что Бизнес-модель найдена.
                    Подтверждением актуальности Бизнес-модели может также служить первая произведенная продажа.</p>

                <div>Ниже приведены материалы по теме «Канва бизнес-модели»:</div>
                <div class="container-text">
                    <ol>
                        <li class="pl-15">
                            A Better Way to Think About Your Business Model, A. Osterwalder, Harvard Business Review, 06.05.2013
                            <div><?= Html::a('https://hbr.org/2013/05/a-better-way-to-think-about-yo', 'https://hbr.org/2013/05/a-better-way-to-think-about-yo', ['target' => '_blank'])?></div>
                        </li>
                        <li class="pl-15">
                            Business Model Canvas. Строим модель бизнеса на примере Uber и Netflix, А. Ница, Skillbox, 21.07.21
                            <div><?= Html::a('https://skillbox.ru/media/management/business-model-canvas/', 'https://skillbox.ru/media/management/business-model-canvas/', ['target' => '_blank'])?></div>
                        </li>
                        <li class="pl-15">
                            Как построить работающую бизнес-модель, Н. Корзинов, Rusbase, 28.06.2018
                            <div><?= Html::a('https://rb.ru/opinion/biznes-model/', 'https://rb.ru/opinion/biznes-model/', ['target' => '_blank'])?></div>
                        </li>
                    </ol>
                </div>

                <p>
                    По итогам формирования Бизнес-модели в системе Spaccel.ru еще раз внимательно просмотрите заполненные блоки, возможно, некоторые потребуют доработки, другие же потребуют
                    внесения информации. По сути, вы увидите упрощенную модель вашего будущего бизнеса, взаимосвязь всех бизнес-процессов, схему функционирования компании.
                </p>

                <p>
                    Проанализируйте получившуюся картину на предмет логичности и жизнеспособности, сделайте вывод относительно перспектив продукта и бизнеса. Если какой-либо из блоков «выпадает»
                    из общей картины, вам необходимо вернуться назад (совершить pivot) и отрегулировать параметры блока (блоков) или, при необходимости, продукта в целом.
                </p>

            </div>

        </div>

        <!--Модальные окна-->
        <?= $this->render('modal') ?>

    <?php else : ?>

        <div class="methodological-guide">

            <div class="header_hypothesis_first_index">Разработка бизнес-модели</div>

            <div class="container-list">

                <div class="simple-block">
                    <p>
                        <span>Задача:</span>
                        Проверить на соответствие рекомендациям и формату заполненную форму <span>Бизнес-модель.</span>
                    </p>
                    <p>
                        <span>Результат:</span>
                        Информация проверена. При необходимости сформированы замечания о необходимости произвести корректировки.
                    </p>
                </div>

                <div class="bold">Рекомендации:</div>
                <div class="container-text">
                    <ul>
                        <li class="pl-15">Все поля должны быть заполнены.</li>
                        <li class="pl-15">Проверить, опираясь на здравый смысл, целостность и логичность бизнес-модели, взаимоувязанность ее элементов.</li>
                    </ul>
                </div>

                <h4><span class="bold"><u>Информация, полученная Проектантом:</u></span></h4>

                <p>Целью, а также заключительным этапом работы в рамках нашего Акселератора является генерация Бизнес-модели. Мы предлагаем
                    использовать Канву бизнес-модели (англ. Business model canvas), разработанную авторами Александром Остервальдером и Ивом Пинье.</p>

                <p>Канва бизнес-модели состоит из 9 блоков, которые могут быть объединены в 4 группы, каждый из блоков описывает
                    свою часть бизнес-модели организации, а именно: ключевые партнеры, ключевые активности, достоинства и предложения,
                    отношения с заказчиком, пользовательские сегменты, ключевые ресурсы, каналы поставки, структура затрат и источники доходов.</p>

                <p>На основании ранее заполненных форм система автоматически сгенерирует вариант Бизнес-модели,
                    который при необходимости вы сможете отредактировать.</p>

                <p>Наличие решения к цепочке «Клиент – Проблема – Ценностное предложение» означает, что Бизнес-модель найдена.
                    Подтверждением актуальности Бизнес-модели может также служить первая произведенная продажа.</p>

                <div>Ниже приведены материалы по теме «Канва бизнес-модели»:</div>
                <div class="container-text">
                    <ol>
                        <li class="pl-15">
                            A Better Way to Think About Your Business Model, A. Osterwalder, Harvard Business Review, 06.05.2013
                            <div><?= Html::a('https://hbr.org/2013/05/a-better-way-to-think-about-yo', 'https://hbr.org/2013/05/a-better-way-to-think-about-yo', ['target' => '_blank'])?></div>
                        </li>
                        <li class="pl-15">
                            Business Model Canvas. Строим модель бизнеса на примере Uber и Netflix, А. Ница, Skillbox, 21.07.21
                            <div><?= Html::a('https://skillbox.ru/media/management/business-model-canvas/', 'https://skillbox.ru/media/management/business-model-canvas/', ['target' => '_blank'])?></div>
                        </li>
                        <li class="pl-15">
                            Как построить работающую бизнес-модель, Н. Корзинов, Rusbase, 28.06.2018
                            <div><?= Html::a('https://rb.ru/opinion/biznes-model/', 'https://rb.ru/opinion/biznes-model/', ['target' => '_blank'])?></div>
                        </li>
                    </ol>
                </div>

                <p>
                    По итогам формирования Бизнес-модели в системе Spaccel.ru еще раз внимательно просмотрите заполненные блоки, возможно, некоторые потребуют доработки, другие же потребуют
                    внесения информации. По сути, вы увидите упрощенную модель вашего будущего бизнеса, взаимосвязь всех бизнес-процессов, схему функционирования компании.
                </p>

                <p>
                    Проанализируйте получившуюся картину на предмет логичности и жизнеспособности, сделайте вывод относительно перспектив продукта и бизнеса. Если какой-либо из блоков «выпадает»
                    из общей картины, вам необходимо вернуться назад (совершить pivot) и отрегулировать параметры блока (блоков) или, при необходимости, продукта в целом.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/business_model_index.js'); ?>
