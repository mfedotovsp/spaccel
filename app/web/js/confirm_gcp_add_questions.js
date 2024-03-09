//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

$(document).ready(function () {
    if ($(window).width() <= '480') {
        $('.confirm-gcp-add_questions').remove();
        $('.confirm-hypothesis-step-two-mobile').toggle('display')
    } else {
        $('.confirm-hypothesis-add-questions-mobile').remove();
    }
});

$(window).resize(function () {
    if ($(window).width() <= '480' && $('.confirm-gcp-add_questions').length > 0) {
        location.reload();
        $('.confirm-gcp-add_questions').remove();
    } else if ($(window).width() > '480' && $('.confirm-hypothesis-add-questions-mobile').length > 0) {
        location.reload();
        $('.confirm-hypothesis-add-questions-mobile').remove();
    }
});


// Показать инструкцию для стадии разработки
$(body).on('click', '.open_modal_instruction_page', function (e) {

    var url = $(this).attr('href');
    var modal = $('.modal_instruction_page');
    $(body).append($(modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            if ($(modal).find('.modal-header').find('.modal-header-text-append').length === 0) {
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Этап 6. Подтверждение гипотез ценностных предложений</div>');
            }
            $(modal).find('.modal-body').html(response);
            $(modal).modal('show');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Редактирование исходных даннных подтверждения (Шаг 1)
$(body).on('beforeSubmit', '#update_data_confirm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    $.ajax({ url: url, method: 'POST', data: data, cache: false});
    e.preventDefault();
    return false;
});


//Показываем и скрываем форму добавления вопроса
//при нажатии на кнопку добавить вопрос (Шаг 2)
$(body).on('click', '#buttonAddQuestion', function(e){

    //Вырезаем и вставляем форму добавления вопроса (Шаг 2)
    var form_newQuestion_panel = $('.form-newQuestion-panel');
    $(form_newQuestion_panel).append($('.form-newQuestion').first());
    $(form_newQuestion_panel).toggle();

    e.preventDefault();
    return false;
});


//Передаем выбранное значение из select в поле ввода
$(body).on('select2:select', '#add_new_question_confirm', function(){
    $('#add_text_question_confirm').val($(this).val());
    $(this).val('');
});


//Создание нового вопроса (Шаг 2)
$(body).on('beforeSubmit', '#addNewQuestion', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            //Обновляем список вопросов на странице
            $('#QuestionsTable-container').html(response.ajax_questions_confirm);

            //Обновляем список вопросов для добавления (Шаг 2)
            var queryQuestions = response.queryQuestions;
            var addNewQuestionForm = $('#addNewQuestion');
            $(addNewQuestionForm).find('select').html('');
            $(addNewQuestionForm).find('select').prepend('<\option style=\"font - weight:700;\" value=\"\">Выберите вариант из списка готовых вопросов<\/option>');
            $.each(queryQuestions, function(index, value) {
                $(addNewQuestionForm).find('select').append('<\option value=\"' + value.title + '\">' + value.title + '<\/option>');
            });

            //Очищием форму добавления вопроса
            $(addNewQuestionForm)[0].reset();
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Получить форму редактирования вопроса для анкеты (Шаг 2)
$(body).on('click', '.showQuestionUpdateForm', function (e) {

    var url = $(this).attr('href');
    var id = url.split('id=')[1];

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            //Обновляем список вопросов на странице
            $('#QuestionsTable-container').html(response.ajax_questions_confirm);

            //Добавляем форму редактирования для выбранного вопроса
            $('.string_question-' + id).html(response.renderAjax);

            //Устанавливаем курсор в поле формы
            var input = $('#update_text_question_confirm');
            var inputVal = input.val();
            input.val('').focus().val(inputVal);
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Отмена редактирования вопроса для анкеты (Шаг 2)
$(body).on('click', '.submit_update_question_cancel', function (e) {

    var url = $(this).attr('href');

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            //Обновляем список вопросов на странице
            $('#QuestionsTable-container').html(response.ajax_questions_confirm);
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Редактирование вопроса для анкеты (Шаг 2)
$(body).on('beforeSubmit', '#updateQuestionForm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            //Обновляем список вопросов на странице
            $('#QuestionsTable-container').html(response.ajax_questions_confirm);

            //Обновляем список вопросов для добавления
            var queryQuestions = response.queryQuestions;
            var addNewQuestionForm = $('#addNewQuestion');
            $(addNewQuestionForm).find('select').html('');
            $(addNewQuestionForm).find('select').prepend('<\option style=\"font - weight:700;\" value=\"\">Выберите вариант из списка готовых вопросов<\/option>');
            $.each(queryQuestions, function(index, value) {
                $(addNewQuestionForm).find('select').append('<\option value=\"' + value.title + '\">' + value.title + '<\/option>');
            });
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Удаление вопроса для анкеты (Шаг 2)
$(body).on('click', '.delete-question-confirm-hypothesis', function(e){

    var url = $(this).attr('href');

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            //Обновляем список вопросов на странице
            $('#QuestionsTable-container').html(response.ajax_questions_confirm);

            //Обновляем список вопросов для добавления (Шаг 2)
            var queryQuestions = response.queryQuestions;
            var addNewQuestionForm = $('#addNewQuestion');
            $(addNewQuestionForm).find('select').html('');
            $(addNewQuestionForm).find('select').prepend('<\option style=\"font - weight:700;\" value=\"\">Выберите вариант из списка готовых вопросов<\/option>');
            $.each(queryQuestions, function(index, value) {
                $(addNewQuestionForm).find('select').append('<\option value=\"' + value.title + '\">' + value.title + '<\/option>');
            });

        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//события для select2 https://select2.org/programmatic-control/events
//Открытие и закрытие списка вопросов для добавления в анкету
$(body).on('click', '#button_add_text_question_confirm', function(e){

    if(!$(this).hasClass('openDropDownList')){

        $('#add_new_question_confirm').select2('open');
        $(this).addClass('openDropDownList');
        $(this).css('border-width', '0');
        $(this).find('.triangle-bottom').css('transform', 'rotate(180deg)');

        var position_button = $('#button_add_text_question_confirm').offset().top;
        var position_select = $('.select2-container--krajee .select2-dropdown').offset().top;

        if (position_button < position_select) {
            $('#add_text_question_confirm').css({'border-bottom-width': '0', 'border-radius': '12px 12px 0 0'});
        } else {
            $('#add_text_question_confirm').css({'border-top-width': '0', 'border-radius': '0 0 12px 12px'});
        }

    }else {

        $('#add_new_question_confirm').select2('close');
        $(this).removeClass('openDropDownList');
        $(this).css('border-width', '0 0 0 1px');
        $(this).find('.triangle-bottom').css('transform', 'rotate(0deg)');
        $('#add_text_question_confirm').css({'border-width': '1px', 'border-radius': '12px'});
    }

    e.preventDefault();
    return false;
});

//Проверяем позицию кнопки и select при скролле страницы и задаем стили для поля ввода
$(window).on('scroll', function() {

    var button = $('#button_add_text_question_confirm');
    var select = $('.select2-container--krajee .select2-dropdown');

    if($(button).length > 0 && $(select).length > 0) {

        var position_button = $(button).offset().top;
        var position_select = $(select).offset().top;

        if (position_button < position_select) {

            $('#add_text_question_confirm').css({
                'border-top-width': '1px',
                'border-bottom-width': '0',
                'border-radius': '12px 12px 0 0',
            });
        } else {

            $('#add_text_question_confirm').css({
                'border-bottom-width': '1px',
                'border-top-width': '0',
                'border-radius': '0 0 12px 12px',
            });
        }
    }
});

// Отслеживаем клик вне поля Select
$(document).mouseup(function (e){ // событие клика по веб-документу

    var search = $('.select2-container--krajee .select2-search--dropdown .select2-search__field'); // поле поиска в select
    var button = $('#button_add_text_question_confirm'); // кнопка открытия и закрытия списка select

    if (!search.is(e.target) && !button.is(e.target) // если клик был не полю поиска и не по кнопке
        && search.has(e.target).length === 0 && button.has(e.target).length === 0) { // и не их по его дочерним элементам

        $('#add_new_question_confirm').select2('close'); // скрываем список select
        $(button).removeClass('openDropDownList'); // убираем класс открытового списка у кнопки открытия и закрытия списка select

        $(button).css('border-width', '0 0 0 1px'); // возвращаем стили кнопке
        $(this).find('.triangle-bottom').css('transform', 'rotate(0deg)'); // возвращаем стили кнопке
        $('#add_text_question_confirm').css({'border-width': '1px', 'border-radius': '12px'}); // возвращаем стили для поля ввода
    }
});


//Если задано, что count_respond < count_positive, то count_respond = count_positive
$(body).on('change', 'input#confirm_count_respond', function () {
    var value1 = $('input#confirm_count_positive').val();
    var value2 = $(this).val();
    var valueMax = 100;
    var valueMin = 1;

    if (parseInt(value2) < parseInt(value1)){
        value2 = value1;
        $(this).val(value2);
    }

    if (parseInt(value2) > parseInt(valueMax)){
        value2 = valueMax;
        $(this).val(value2);
    }

    if (parseInt(value2) < parseInt(valueMin)){
        value2 = valueMin;
        $(this).val(value2);
    }
});

//Если задано, что count_positive > count_respond, то count_positive = count_respond
$(body).on('change', 'input#confirm_count_positive', function () {
    var value1 = $(this).val();
    var value2 = $('input#confirm_count_respond').val();
    var valueMax = 100;
    var valueMin = 1;

    if (parseInt(value1) > parseInt(value2)){
        value1 = value2;
        $(this).val(value1);
    }

    if (parseInt(value1) > parseInt(valueMax)){
        value1 = valueMax;
        $(this).val(value1);
    }

    if (parseInt(value1) < parseInt(valueMin)){
        value1 = valueMin;
        $(this).val(value1);
    }
});


//Показываем модальное окно - запрет перехода на следующий шаг
var modal_next_step_error = $('#next_step_error');

$(body).on('click', '.show_modal_next_step_error', function (e) {

    $(body).append($(modal_next_step_error).first());
    $(modal_next_step_error).modal('show');

    e.preventDefault();
    return false;
});