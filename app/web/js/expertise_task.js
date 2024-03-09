//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var module = (window.location.pathname).split('/')[1];
var page = window.location.search.split('=')[1];
if (typeof(page) === 'undefined') page = 1;

$(document).ready(function() {

    // Если поле поиска не пусто, то делаем его видимым
    var input_search_tasks = $('#search_tasks');
    if ($(input_search_tasks).val() !== '') {
        $('.search-block').toggle('display');
    }
});


// При клике по строке с названием проекта
// Показываем и скрываем данные
$(body).on('click', '.container-one_hypothesis', function () {

    var project_id = $(this).parent().attr('id').split('-')[1];
    var block_data = $(this).parent().find('.hereAddDataOfProject');
    var visible_param; // Переменная для определения видимости блока

    if ($(block_data).is(':hidden')){
        visible_param = true;
        // Делаем активный блок неактиным
        $(body).find('.container-one_hypothesis.active').trigger('click');
         // Делаем выбранный блок активным
        $(this).addClass('active');
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#7F9FC5',
            'border-radius' : '12px 12px 0px 0px',
        });
    }
    if ($(block_data).is(':visible')) {
        visible_param = false;
        // Делаем выбранный блок неактивным
        $(this).removeClass('active');
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#707F99',
            'border-radius' : '12px',
        });
    }

    // Меняем видимость блока
    $(block_data).toggle('display');

    if (visible_param === true) {
        // Если нет активных блоков (первый клик по строке с названием проекта)
        if ($(block_data).find('.block-links-menu-tasks > .row > div > .link-menu-tasks').hasClass('active')) {} else {
            // Открываем контент по первой ссылке меню
            $(block_data).find('.block-links-menu-tasks > .row > div:nth-child(1) > .link-menu-tasks').trigger('click');
        }
    }
});


// При клике по ссылкам меню задания на экспертизу
$(body).on('click', '.link-menu-tasks', function (e) {

    var link = (this);
    var url = $(link).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            var active_link = $('#expertise_task-'+response.project_id).find('.link-menu-tasks');
            $(active_link).removeClass('active');
            $(link).addClass('active');
            $('#expertise_task-'+response.project_id).find('.hereAddDataOfProject > .block-tasks-content').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Показать и скрыть форму поиска
$(body).on('click', '#show_search_tasks', function () {
    $('.search-block').toggle('display');
});

//Запрет на отправку формы поиска
$(body).keydown('form#search_expertise_tasks', function(e) {
    if (e.keyCode === 13) e.preventDefault();
});


// Отслеживаем ввод в строку поиск и выводим найденные проекты
$(body).on('input', 'form#search_expertise_tasks', function(e) {

    var container = $('.expertise-tasks-content');
    var data = $(this).serialize();
    var url = $(this).attr('action');

    // Записываем строку поиска в кэш
    $.ajax({url: '/' + module + '/expertise/save-cache-search-form', method: 'POST', data: data});

    // Смена URL без перезагрузки страницы
    history.pushState(null, null, '/' + module + '/expertise/tasks');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            $('.expertise-tasks-content').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Поиск экспертов для проведения экспертизы по проекту
$(body).on('click', '.search_submit_experts', function (e) {

    var project_id = $(this).attr('id').split('-')[1];
    var data = $('#search_form_experts-' + project_id).serialize();
    var url = $('#search_form_experts-' + project_id).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function (response) {

            $('#result_search-' + project_id).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Отправка коммуникации
$(body).on('click', '.send-communication', function (e) {

    var url = $(this).attr('href');
    var container = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            if (response.success) {

                if (response.type === 100) {
                    $(container).html('<div class="text-success">Запрос сделан</div>');
                }

                else {

                    $.ajax({
                        url: '/' + module + '/communications/get-communications?id=' + response.project_id,
                        method: 'POST',
                        cache: false,
                        success: function (response) {

                            $('#expertise_task-' + response.project_id).find('.hereAddDataOfProject > .block-tasks-content').html(response.renderAjax);
                        }
                    });


                    if (response.type === 300 || response.type === 350) {

                        // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
                        var blockCountUnreadCommunications = $(body).find('.countUnreadCommunications');
                        var newQuantityAfterRead = response.countUnreadCommunications;
                        $(blockCountUnreadCommunications).html(newQuantityAfterRead);
                        if (newQuantityAfterRead < 1) $(blockCountUnreadCommunications).removeClass('active');
                    }
                }
            }
        }
    });

    e.preventDefault();
    return false;
});


// Получить форму выбора типов эксперта при назначении на проект
$(body).on('click', '.get-form-types-expert', function (e) {

    var url = $(this).attr('href');
    var modal = $('#expert_types_modal');
    var container = $(modal).find('.modal-body');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            $(modal).modal('show');
            $(container).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Назначение эксперта на проект и сохранение формы выбора типов эксперта
$(body).on('beforeSubmit', '#form_types_expert', function (e) {

    var url = $(this).attr('action');
    var data = $(this).serialize();
    var modal = $('#expert_types_modal');

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function (response) {

            if (response.success) {

                $.ajax({
                    url: '/' + module + '/communications/get-communications?id=' + response.project_id,
                    method: 'POST',
                    cache: false,
                    success: function (response) {

                        $('#expertise_task-' + response.project_id).find('.hereAddDataOfProject > .block-tasks-content').html(response.renderAjax);
                        $(modal).modal('hide');
                    }
                });

                // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
                var blockCountUnreadCommunications = $(body).find('.countUnreadCommunications');
                var newQuantityAfterRead = response.countUnreadCommunications;
                $(blockCountUnreadCommunications).html(newQuantityAfterRead);
                if (newQuantityAfterRead < 1) $(blockCountUnreadCommunications).removeClass('active');
            }
        }
    });

    e.preventDefault();
    return false;
});


// Ссылка на профиль эксперта
$(body).on('click', '.column-user-fio', function () {
    var id = $(this).attr('id').split('-')[1];
    window.location.href = '/expert/profile/index?id=' + id;
});

