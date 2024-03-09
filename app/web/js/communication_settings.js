//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');


// Шаблоны коммуникации о готовности эксперта провести экспертизу
$(body).on('click', '#show_form_pattern_CARCE', function (e) {

    $('.form-pattern-CARCE').toggle('display');
    e.preventDefault();
    return false;
});

$(body).on('beforeSubmit', '#create_pattern_CARCE', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function (response) {

            $('.all-patterns-CARCE').html(response.renderAjax);
            $('.form-pattern-CARCE').toggle('display');
            $('#create_pattern_CARCE')[0].reset();
        }
    });
    e.preventDefault();
    return false;
});


// Шаблоны коммуникации отмена запроса о готовности эксперта провести экспертизу
$(body).on('click', '#show_form_pattern_CWRARCE', function (e) {

    $('.form-pattern-CWRARCE').toggle('display');
    e.preventDefault();
    return false;
});

$(body).on('beforeSubmit', '#create_pattern_CWRARCE', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function (response) {

            $('.all-patterns-CWRARCE').html(response.renderAjax);
            $('.form-pattern-CWRARCE').toggle('display');
            $('#create_pattern_CWRARCE')[0].reset();
        }
    });
    e.preventDefault();
    return false;
});


// Шаблоны коммуникации назначение экперта на проект
$(body).on('click', '#show_form_pattern_CAEP', function (e) {

    $('.form-pattern-CAEP').toggle('display');
    e.preventDefault();
    return false;
});

$(body).on('beforeSubmit', '#create_pattern_CAEP', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function (response) {

            $('.all-patterns-CAEP').html(response.renderAjax);
            $('.form-pattern-CAEP').toggle('display');
            $('#create_pattern_CAEP')[0].reset();
        }
    });
    e.preventDefault();
    return false;
});


// Шаблоны коммуникации отказ эксперту в назначении на проект
$(body).on('click', '#show_form_pattern_CDNAEP', function (e) {

    $('.form-pattern-CDNAEP').toggle('display');
    e.preventDefault();
    return false;
});

$(body).on('beforeSubmit', '#create_pattern_CDNAEP', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function (response) {

            $('.all-patterns-CDNAEP').html(response.renderAjax);
            $('.form-pattern-CDNAEP').toggle('display');
            $('#create_pattern_CDNAEP')[0].reset();
        }
    });
    e.preventDefault();
    return false;
});


// Шаблоны коммуникации отзыв эксперта с проекта
$(body).on('click', '#show_form_pattern_CWEFP', function (e) {

    $('.form-pattern-CWEFP').toggle('display');
    e.preventDefault();
    return false;
});

$(body).on('beforeSubmit', '#create_pattern_CWEFP', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function (response) {

            $('.all-patterns-CWEFP').html(response.renderAjax);
            $('.form-pattern-CWEFP').toggle('display');
            $('#create_pattern_CWEFP')[0].reset();
        }
    });
    e.preventDefault();
    return false;
});


// Получить форму редактирования шаблона коммуникации
$(body).on('click', '.update-communication-pattern', function (e) {

    // Закрыть все формы редактирования шаблонов
    $('.cancel-edit-pattern').each(function(i, obj) {
        $(obj).trigger('click');
    });

    var url = $(this).attr('href');
    var grandparent = $(this).parents('.style-row-pattern');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            // Открыть форму редактирования шаблона коммуникации
            $(grandparent).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Отмена редактирования шаблона коммуникации
$(body).on('click', '.cancel-edit-pattern', function (e) {

    var url = $(this).attr('href');
    var parent = $(this).parents('.form-edit-pattern');
    var grandparent = $(this).parents('.style-row-pattern');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            // Убрать форму редактирования шаблона
            $(parent).remove();
            // Воостановить представление шаблона
            $(grandparent).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Редактирование шаблона коммуникации
$(body).on('beforeSubmit', '#update_pattern', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');
    var parent = $(this).parents('.form-edit-pattern');
    var grandparent = $(this).parents('.style-row-pattern');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function (response) {

            // Убрать форму редактирования шаблона
            $(parent).remove();
            // Воостановить представление шаблона
            $(grandparent).html(response.renderAjax);
        }
    });
    e.preventDefault();
    return false;
});


// Активация шаблона коммуникации
$(body).on('click', '.activate-communication-pattern', function (e) {

    var url = $(this).attr('href');
    var grandparent = $(this).parents('.style-row-pattern').parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            // Обновить представления всех шаблонов данного типа
            $(grandparent).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Дективация шаблона коммуникации
$(body).on('click', '.deactivate-communication-pattern', function (e) {

    var url = $(this).attr('href');
    var grandparent = $(this).parents('.style-row-pattern');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            // Обновить представление шаблона
            $(grandparent).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Удаление шаблона коммуникации из списка
$(body).on('click', '.delete-communication-pattern', function (e) {

    var url = $(this).attr('href');
    var grandparent = $(this).parents('.style-row-pattern');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            $(grandparent).remove();
        }
    });

    e.preventDefault();
    return false;
});