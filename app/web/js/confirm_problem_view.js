//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));
const confirmId = window.location.search.split('=')[1];
const isContractorModule = (window.location.pathname).split('/')[1] === 'contractor';
const action = !isContractorModule ? window.location.pathname.split('/')[2] : window.location.pathname.split('/')[3];
var body = $('body');


//Выполнить при загрузке страницы
$(document).ready(function () {
    if ($(window).width() <= '480') {
        $('.confirm-problem-view').remove();
        $('.confirm-hypothesis-step-three-mobile').toggle('display')
    } else {
        $('.confirm-hypothesis-view-mobile').remove()
    }

    getQueryResponds()
});

$(window).resize(function () {
    if ($(window).width() <= '480' && $('.confirm-problem-view').length > 0) {
        location.reload();
        $('.confirm-problem-view').remove();
    } else if ($(window).width() > '480' && $('.confirm-hypothesis-view-mobile').length > 0) {
        location.reload();
        $('.confirm-hypothesis-view-mobile').remove();
    }
});


// Получение списка респондентов
function getQueryResponds() {
    var url
    if ($(window).width() <= '480') {
        if (action === 'view-trash') {
            url = '/responds/get-query-responds?stage=4&id=' + confirmId + '&isMobile=true&isOnlyNotDelete=false&isModuleContractor=' + isContractorModule;
        } else {
            url = '/responds/get-query-responds?stage=4&id=' + confirmId + '&isMobile=true&isModuleContractor=' + isContractorModule;
        }
    } else {
        if (action === 'view-trash') {
            url = '/responds/get-query-responds?stage=4&id=' + confirmId + '&isOnlyNotDelete=false&isModuleContractor=' + isContractorModule;
        } else {
            url = '/responds/get-query-responds?stage=4&id=' + confirmId + '&isModuleContractor=' + isContractorModule;
        }
    }
    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if ($(window).width() <= '480') {
                $('.confirm-hypothesis-step-three-mobile > .content_responds_ajax').html(response.ajax_data_responds);
            } else {
                $('.content_responds_ajax').html(response.ajax_data_responds);
            }
        }
    });
}


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
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Этап 4. Подтверждение гипотез проблем сегментов</div>');
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


//Возвращение скролла первого модального окна после закрытия
$('#confirm_closing_update_modal').on('hidden.bs.modal', function(){
    $(body).addClass('modal-open');
});
$('#error_respond_modal').on('hidden.bs.modal', function(){
    $(body).addClass('modal-open');
});


//Редактирование исходных даннных подтверждения (Шаг 1)
$(body).on('beforeSubmit', '#update_data_confirm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            if (response.success) {
                getQueryResponds()
            }
        }
    });

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


//Получить форму редактирования вопроса для интервью (Шаг 2)
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


//Отмена редактирования вопроса для интервью (Шаг 2)
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


//Редактирование вопроса для интервью (Шаг 2)
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


//Удаление вопроса для интервью (Шаг 2)
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

    if (!isContractorModule && action !== 'view-trash') {
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


//Отслеживаем изменения в форме создания респондента и записываем их в кэш
$(body).on('change', 'form#new_respond_form', function(){

    var url = '/responds/save-cache-creation-form?stage=4&id=' + confirmId;
    var data = $(this).serialize();
    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        error: function(){
            alert('Ошибка');
        }
    });
});


//Поиск респондентов в мобильной версии
$(body).on('input', 'input#search_input_responds_mobile', function () {
    var search = $('input#search_input_responds_mobile').val();
    var url = '/responds/get-query-responds?stage=4&id=' + confirmId + '&search=' + search + '&isMobile=true&isModuleContractor=' + isContractorModule;

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $('.confirm-hypothesis-step-three-mobile > .content_responds_ajax').html(response.ajax_data_responds);
        }
    });
});


//При нажатии на кнопку Добавить респондента
$(body).on('click', '#showRespondCreateForm', function(e){

    if (isContractorModule) {
        $('#formUpdateRespond').remove();
    }
    var url = $(this).attr('href');
    var respondCreate_modal = $('#respondCreate_modal');
    $(body).append($(respondCreate_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(respondCreate_modal).find('.modal-body').html(response.renderAjax);
            $(respondCreate_modal).modal('show');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//В мобильной версии
$(body).on('click', '.show_add_respond', function(e){

    if (isContractorModule) {
        $('#formUpdateRespond').remove();
    }
    $('.add_respond_block_mobile').toggle('display');
    $('.row_header_data_generation_mobile').toggle('display');
    if ($(this).hasClass('link_add_respond_text')) {
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function(response){
                $('.add_respond_block_mobile').html(response.renderAjax);
            }
        });
    }
    e.preventDefault();
    return false;
});


//При создании нового респондента из формы
$(body).on('beforeSubmit', '#new_respond_form', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    var error_respond_modal = $('#error_respond_modal');
    $(body).append($(error_respond_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            if (!response.limit_count_respond) {

                if (!response.error) {

                    //Загружаем данные респондентов (Шаг 3)
                    getQueryResponds()

                    //Закрываем окно создания нового респондента
                    if ($(window).width() <= '480') {
                        $('.confirm-hypothesis-step-one-mobile > .block-ajax-data-confirm').html(response.ajax_data_confirm);
                        $('.add_respond_block_mobile').toggle('display');
                        $('.row_header_data_generation_mobile').toggle('display');
                        simpleBar.getScrollElement().scrollBy({top: $('.container-one_hypothesis_mobile:last').offset().top, behavior: 'smooth'});
                    } else {
                        $('#respondCreate_modal').modal('hide');
                        //Обновление данных подтверждения (Шаг 1)
                        $('#step_one').html(response.ajax_data_confirm);
                        simpleBar.getScrollElement().scrollBy({top: $('.container-one_respond:last').offset().top, behavior: 'smooth'});
                    }

                } else {

                    //Показываем окно с информацией
                    $(error_respond_modal).find('.modal-header h3').html('Внимание');
                    $(error_respond_modal).find('.modal-body h4').html('Респондент с таким именем уже есть.<br>Имя респондента должно быть уникальным.');
                    $(error_respond_modal).modal('show');
                }

            }else {

                //Закрываем окно создания нового респондента
                $('#respondCreate_modal').modal('hide');
                //Показываем окно с информацией
                $(error_respond_modal).find('.modal-header h3').html('Создание нового респондента заблокировано');
                $(error_respond_modal).find('.modal-body h4').html('Действует ограничение. Вы не можете добавить больше существующего количества респондентов.');
                $(error_respond_modal).modal('show');
            }

        }
    });

    e.preventDefault();
    return false;
});


//Получение формы редактирования данных репондента
$(body).on('click', '.showRespondUpdateForm', function(e){

    var id = $(this).attr('id').split('-')[1];
    if (!isContractorModule) {
        if (action !== 'view-trash') {
            var url = '/responds/get-data-update-form?stage=4&id=' + id;
        } else {
            var url = '/responds/get-data-update-form?stage=4&id=' + id + '&isOnlyNotDelete=false';
        }
    } else {
        $('#formUpdateRespond').remove();
        if (action !== 'view-trash') {
            var url = '/contractor/responds/get-data-update-form?stage=4&id=' + id;
        } else {
            var url = '/contractor/responds/get-data-update-form?stage=4&id=' + id + '&isOnlyNotDelete=false';
        }
    }

    var respond_update_modal = $('#respond_update_modal');
    $(body).append($(respond_update_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(respond_update_modal).find('.modal-body').html(response.renderAjax);
            $(respond_update_modal).modal('show');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


var catchChange = false;
//Отслеживаем изменения в форме редактирования дынных респондента
$(body).on('change', 'form#formUpdateRespond', function(){
    if (catchChange === false) catchChange = true;
});

//Если в форме редактирования были внесены изменения,
//то при любой попытке закрыть окно показать окно подтверждения
$(body).on('hide.bs.modal', '#respond_update_modal', function(e){
    if(catchChange === true) {
        $('#confirm_closing_update_modal').appendTo('body').modal('show');
        e.stopImmediatePropagation();
        e.preventDefault();
        return false;
    }
});

//Подтверждение закрытия окна редактирования
$(body).on('click', '#button_confirm_closing_modal', function (e) {
    catchChange = false;
    $('.modal').modal('hide');
    e.preventDefault();
    return false;
});


//Сохранении данных из формы редактирование данных респондента
$(body).on('beforeSubmit', '#formUpdateRespond', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    var error_respond_modal = $('#error_respond_modal');
    $(body).append($(error_respond_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            if (!response.error) {

                getQueryResponds()
                //Закрываем окно редактирования
                if (catchChange === true) catchChange = false;
                $('#respond_update_modal').modal('hide');

                if (isContractorModule) {
                    $('#respondCreate_modal').modal('hide');
                    if ($('.add_respond_block_mobile').is(":visible")) {
                        $('.add_respond_block_mobile').toggle('display');
                        $('.row_header_data_generation_mobile').toggle('display');
                    }
                }

            } else {

                //Показываем окно с информацией
                $(error_respond_modal).find('.modal-header h3').html('Внимание');
                $(error_respond_modal).find('.modal-body h4').html('Респондент с таким именем уже есть.<br>Имя респондента должно быть уникальным.');
                $(error_respond_modal).modal('show');
            }
        }
    });

    e.preventDefault();
    return false;
});


//Отслеживаем изменения в форме создания интервью и записываем их в кэш
$(body).on('change', 'form#formCreateDescInterview', function(){

    var respond_id = $(this).attr('action').split('id=')[1];
    var url = '/interviews/save-cache-creation-form?stage=4&id=' + respond_id;
    var data = $(this).serialize();
    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        error: function(){
            alert('Ошибка');
        }
    });
});


//Получение формы создания интервью для респондента
$(body).on('click', '.showDescInterviewCreateForm', function(e){

    var url_1 = $(this).attr('href');
    var id = $(this).attr('id').split('-')[1];
    var url_2 = '/interviews/get-data-create-form?stage=4&id=' + id;
    var error_respond_modal = $('#error_respond_modal');
    $(body).append($(error_respond_modal).first());
    var create_descInterview_modal = $('#create_descInterview_modal');
    $(body).append($(create_descInterview_modal).first());

    $.ajax({

        url: url_1,
        method: 'POST',
        cache: false,
        success: function(response){
            if (!response.error) {

                //Показываем окно создания интервью
                $.ajax({
                    url: url_2,
                    method: 'POST',
                    cache: false,
                    success: function(response){

                        $(create_descInterview_modal).find('.modal-body').html(response.renderAjax);
                        $(create_descInterview_modal).modal('show');
                    }
                });

            } else {

                //Показываем окно с информацией
                $(error_respond_modal).find('.modal-header h3').html('Информация');
                $(error_respond_modal).find('.modal-body h4').html('Для перехода к созданию интервью, необходимо заполнить вводные данные по всем заданным респондентам.');
                $(error_respond_modal).modal('show');
            }
        }
    });

    e.preventDefault();
    return false;
});


//После выбора файла в форме создания интервью выводим его имя на экран
$(body).on('change', '#formCreateDescInterview input[type=file]',function(){

    var filename = $(this).val().split('\\').pop();
    $('.title_file').html(filename);
});


//Сохранении данных из формы при создании интервью
$(body).on('beforeSubmit', '#formCreateDescInterview', function(e){

    var data = new FormData(this);
    var url = $(this).attr('action');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response){

            getQueryResponds()
            //Закрываем модальное окно с формой
            $('#create_descInterview_modal').modal('hide');
        }
    });

    e.preventDefault();
    return false;
});


//Получение формы редактирования интервью респондента
$(body).on('click', '.showDescInterviewUpdateForm', function(e){

    var id = $(this).attr('id').split('-')[1];
    if (action !== 'view-trash') {
        var url = '/interviews/get-data-update-form?stage=4&id=' + id;
    } else {
        var url = '/interviews/get-data-update-form?stage=4&id=' + id + '&isOnlyNotDelete=false';
    }
    var update_descInterview_modal = $('#update_descInterview_modal');
    $(body).append($(update_descInterview_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(update_descInterview_modal).find('.modal-body').html(response.renderAjax);
            $(update_descInterview_modal).modal('show');
        }
    });

    e.preventDefault();
    return false;
});


//После выбора файла в форме редактирования интервью выводим его имя на экран
$(body).on('change', '#formUpdateDescInterview input[type=file]',function(){

    var filename = $(this).val().split('\\').pop();
    $('.file_name_update_form').html(filename);
});


//Отслеживаем изменения в форме редактирования интервью
$(body).on('change', 'form#formUpdateDescInterview', function(){
    if (catchChange === false) catchChange = true;
});

//Если в форме редактирования были внесены изменения,
//то при любой попытке закрыть окно показать окно подтверждения
$(body).on('hide.bs.modal', '#update_descInterview_modal', function(e){
    if(catchChange === true) {
        $('#confirm_closing_update_modal').appendTo('body').modal('show');
        e.stopImmediatePropagation();
        e.preventDefault();
        return false;
    }
});


//Редактирование интервью при сохранении данных из формы
$(body).on('beforeSubmit', '#formUpdateDescInterview', function(e){

    var data = new FormData(this);
    var url = $(this).attr('action');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response){

            getQueryResponds();
            //Закрываем модальное окно с формой
            if (catchChange === true) catchChange = false;
            $('#update_descInterview_modal').modal('hide');
        }
    });

    e.preventDefault();
    return false;
});


//Удаление файла из формы редактирования интервью
$(body).on('click', '#link_delete_file', function(e){

    var url = $(this).attr('href');

    $.ajax({

        type:'POST',
        cache: false,
        url: url,
        success: function() {

            $('.interview_file_update').hide();
            $('.link-delete').hide();
            $('.title_name_update_form').hide();
            $('.feed-exp').show();
        },
        error: function() {
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Вызов модального окна удаления респондента
$(body).on('click', '.showDeleteRespondModal', function(e){

    var id = $(this).attr('id').split('-')[1];
    var url = '/responds/get-data-model?stage=4&id=' + id;
    var delete_respond_modal = $('#delete-respond-modal');
    $(body).append($(delete_respond_modal).first());

    $.ajax({

        type:'POST',
        cache: false,
        url: url,
        success: function(response){

            $(delete_respond_modal).find('.modal-body h4').html('Вы уверены, что хотите удалить все данные<br>о респонденте «' + response.name + '»?');
            $(delete_respond_modal).find('.modal-footer #confirm-delete-respond').attr('href', '/responds/delete?stage=4&id=' + response.id);
            $(delete_respond_modal).modal('show');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// CONFIRM RESPOND DELETE
$(body).on('click', '#confirm-delete-respond', function(e) {

    var url = $(this).attr('href');
    var count_respond = $('.block_all_responds').children('.container-one_respond').length;
    var error_respond_modal = $('#error_respond_modal');
    $(body).append($(error_respond_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response) {

            if (!response.success) {

                if (response.zero_value_responds) {

                    //Закрываем окно подтверждения
                    $('#delete-respond-modal').modal('hide');
                    //Показываем окно с ошибкой
                    $(error_respond_modal).find('.modal-header h3').html('Удаление респондента отклонено');
                    $(error_respond_modal).find('.modal-body h4').html('Удаление последнего респондента запрещено.');
                    $(error_respond_modal).modal('show');
                }
                else if (response.number_less_than_allowed) {

                    //Закрываем окно подтверждения
                    $('#delete-respond-modal').modal('hide');
                    //Показываем окно с ошибкой
                    $(error_respond_modal).find('.modal-header h3').html('Удаление респондента отклонено');
                    $(error_respond_modal).find('.modal-body h4').html('Общее количество респондентов не должно быть меньше необходимого количества респондентов, которые должны подтвердить проблему.');
                    $(error_respond_modal).modal('show');
                }
            }
            else if (response.success) {

                getQueryResponds();

                //Закрываем окно подтверждения
                $('#delete-respond-modal').modal('hide');
                //Обновление данных подтверждения (Шаг 1)
                if ($(window).width() <= '480') {
                    $('.confirm-hypothesis-step-one-mobile > .block-ajax-data-confirm').html(response.ajax_data_confirm);
                } else {
                    $('#step_one').html(response.ajax_data_confirm);
                }
            }
        }
    });

    e.preventDefault();
    return false;
});


// CANCEL RESPOND DELETE
$(body).on('click', '#cancel-delete-respond', function(e) {

    //Закрываем окно подтверждения
    $('#delete-respond-modal').modal('hide');

    e.preventDefault();
    return false;
});


//Переход к генерации ГЦП по кнопке Далее
$(body).on('click', '#button_MovingNextStage', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('href');
    var id = url.split('=')[1];
    var error_respond_modal = $('#error_respond_modal');
    $(body).append($(error_respond_modal).first());
    var not_exist_confirm_modal = $('#not_exist-confirm-modal');
    $(body).append($(not_exist_confirm_modal).first());
    var exist_confirm_modal = $('#exist-confirm-modal');
    $(body).append($(exist_confirm_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            if (!response.error) {

                if (!response.not_completed_descInterviews) {

                    if (response.exist_confirm === 1) {
                        window.location.href = '/gcps/index?id=' + id;
                    }
                    else if (response.exist_confirm === null || response.exist_confirm === 0) {
                        $(exist_confirm_modal).modal('show');
                    }

                } else {

                    //Показываем окно с информацией
                    $(error_respond_modal).find('.modal-header h3').html('Информация');
                    $(error_respond_modal).find('.modal-body h4').html('Для продолжения Вам необходимо опросить всех заданных респондентов. Если были созданы задания для исполнителей, то они должны быть готовы или отозваны.');
                    $(error_respond_modal).modal('show');
                }

            } else {

                //Показываем окно выбора
                $(not_exist_confirm_modal).modal('show');
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Отмена завершения неудачного подтверждения ГПС
$(body).on('click', '#cancel-not_exist-confirm', function(e) {

    //Закрываем окно
    $('#not_exist-confirm-modal').modal('hide');

    e.preventDefault();
    return false;
});


//Показать таблицу ответов на вопросы интервью
$(body).on('click', '.openTableQuestionsAndAnswers', function (e) {

    var url
    if (action !== 'view-trash') {
        url = $(this).attr('href');
    } else {
        var url = $(this).attr('href') + '&isOnlyNotDelete=false';
    }
    var showQuestionsAndAnswers_modal = $('#showQuestionsAndAnswers');
    $(body).append($(showQuestionsAndAnswers_modal).first());
    var container = $(showQuestionsAndAnswers_modal).find('.modal-body');

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(container).html(response.ajax_questions_and_answers);
            $(showQuestionsAndAnswers_modal).modal('show');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


$(body).on('click', '.show_search_responds', function (e) {

    $('.search_block_mobile').toggle('display');
    $('.row_header_data_generation_mobile').toggle('display');
    e.preventDefault();
    return false;
});


// Показать форму завершения задания
$(body).on('click', '.buttonShowTaskCompleteForm', function (e) {

    $('.blockTaskCompleteForm').toggle('display');
    $(this).toggle('display');

    e.preventDefault();
    return false;
});


// Сохранение формы завершения задания исполнителем
$(body).on('beforeSubmit', 'form#completeTaskForm', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            if (response.success) {
                location.reload();
            }
        }
    });

    e.preventDefault();
    return false;
});
