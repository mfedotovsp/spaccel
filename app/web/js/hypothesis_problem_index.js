//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));
var module = (window.location.pathname).split('/')[1];

$(document).ready(function() {

    //Фон для модального окна информации при отказе в добавлении ГПС
    var info_hypothesis_create_modal_error = $('.hypothesis_create_modal_error');
    info_hypothesis_create_modal_error.find('.modal-content').css('background-color', '#707F99');


    //Возвращение скролла первого модального окна после закрытия второго
    $('.modal').on('hidden.bs.modal', function () {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) - 10);
            $('body').addClass('modal-open');
        }
    }).on('show.bs.modal', function () {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop.in').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) + 10);
            $(this).css('z-index', parseInt($('.modal-backdrop.in').first().css('z-index')) + 10);
        }
    });

});


var body = $('body');
var id_page = window.location.search.split('=')[1];


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
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Генерация гипотез проблем сегментов</div>');
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


//Отслеживаем изменения в форме создания проблемы и записываем их в кэш
$(body).on('change', 'form#hypothesisCreateForm', function(){

    var url, data
    if (module === 'contractor') {
        url = '/problems/save-cache-creation-form?id=0&taskId=' + id_page;
    } else {
        url = '/problems/save-cache-creation-form?id=' + id_page;
    }
    data = $(this).serialize();
    $.ajax({url: url, data: data, method: 'POST', cache: false});
});


// Показать данные корзины
$(body).on('click', '#show_trash_list', function(e){

    var url = $(this).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $('.block_all_hypothesis').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Показать список гипотез этапа проекта
$(body).on('click', '#show_list', function(e){

    var url = $(this).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $('.block_all_hypothesis').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


//При попытке добавить ГПС проверяем существуют ли необходимые данные
//Если данных достаточно - показываем окно с формой
//Если данных недостаточно - показываем окно с сообщением error
$(body).on('click', '#checking_the_possibility', function(){

    var url = $(this).attr('href');
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    $(body).append($(hypothesis_create_modal).first());
    var hypothesis_create_modal_error = $('.hypothesis_create_modal_error');
    $(body).append($(hypothesis_create_modal_error).first());

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if(response.success){
                $(hypothesis_create_modal).modal('show');
                $(hypothesis_create_modal).find('.modal-body').html(response.renderAjax);
                // Если есть кэш по вопросам для проверки гипотезы проблемы и ответам на них
                if (response.cacheExpectedResultsInterview) {

                    // Данные из кэша к полям модели формы
                    var cacheExpectedResults = response.cacheExpectedResultsInterview;
                    // Перезаписать ключи массива,
                    // т.к. некоторые элементы могут быть удалены
                    // и идти не порядку и в этом случае не будут показаны
                    var cacheExpectedResultsInterview = [];
                    $.each(cacheExpectedResults, function(index, val) {
                        cacheExpectedResultsInterview.push( val );
                    });

                    // Добавляем формы для вопросов и ответов
                    var countExpectedResultsForms = cacheExpectedResultsInterview.length - 1;
                    if (countExpectedResultsForms > 0) {
                        for (var i = 0; i < countExpectedResultsForms; i++) {
                            $('.add_expectedResults_create_form').trigger('click');
                        }
                    }

                    // Добаляем данные из кэша к полям модели формы
                    cacheExpectedResultsInterview.forEach(function(item, i) {
                        $(document.getElementsByName('FormCreateProblem[_expectedResultsInterview]['+i+'][question]')).val(item.question);
                        $(document.getElementsByName('FormCreateProblem[_expectedResultsInterview]['+i+'][answer]')).val(item.answer);
                    });
                }
            }else{
                $(hypothesis_create_modal_error).modal('show');
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    return false;
});


//Сохронение новой гипотезы из формы
$(body).on('beforeSubmit', '#hypothesisCreateForm', function(e){

    var url = $(this).attr('action');
    var data = $(this).serialize();
    var id = url.split('=')[1];

    $.ajax({

        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            if (response.count === '1') {
                $('.hypothesis_create_modal').modal('hide');
                if (module === 'contractor') {
                    location.href = '/contractor/problems/task?id=' + id;
                } else {
                    location.href = '/problems/index?id=' + id;
                }
            } else {
                $('.hypothesis_create_modal').modal('hide');
                $('.block_all_hypothesis').html(response.renderAjax);
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//При нажатии на кнопку редактировать
$(body).on('click', '.update-hypothesis', function(e){

    var url = $(this).attr('href');
    var hypothesis_update_modal = $('.hypothesis_update_modal');
    $(body).append($(hypothesis_update_modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(hypothesis_update_modal).modal('show');
            $(hypothesis_update_modal).find('.modal-header').find('span').html(response.model.title);
            $(hypothesis_update_modal).find('.modal-body').html(response.renderAjax);
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


var catchChange = false;
//Отслеживаем изменения в форме редактирования проблемы
$(body).on('change', 'form#hypothesisUpdateForm', function(){
    if (catchChange === false) catchChange = true;
});

//Если в форме редактирования были внесены изменения,
//то при любой попытке закрыть окно показать окно подтверждения
$(body).on('hide.bs.modal', '.hypothesis_update_modal', function(e){
    if(catchChange === true) {
        $('#confirm_closing_update_modal').appendTo('body').modal('show');
        e.stopImmediatePropagation();
        e.preventDefault();
        return false;
    }
});

//Подтверждение закрытия окна редактирования проблемы
$(body).on('click', '#button_confirm_closing_modal', function (e) {
    catchChange = false;
    $('#confirm_closing_update_modal').modal('hide');
    $('.hypothesis_update_modal').modal('hide');
    e.preventDefault();
    return false;
});


//Редактирование гипотезы проблемы сегмента
$(body).on('beforeSubmit', '#hypothesisUpdateForm', function(e){

    var url = $(this).attr('action');
    var data = $(this).serialize();

    $.ajax({

        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            if (catchChange === true) catchChange = false;
            $('.hypothesis_update_modal').modal('hide');
            $('.block_all_hypothesis').html(response.renderAjax);
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// При нажатии на иконку разрешить экспертизу
$(body).on('click', '.link-enable-expertise', function (e) {

    $.ajax({
        url: $(this).attr('href'),
        method: 'POST',
        cache: false,
        success: function(response){

            $('.block_all_hypothesis').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


//При попытке посмотреть данные интервью представителя сегмента
$(body).on('click', '.get_interview_respond',  function(e){

    var url = $(this).attr('href');
    var respond_positive_view_modal = $('.respond_positive_view_modal');
    $(body).append($(respond_positive_view_modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(respond_positive_view_modal).modal('show');
            $(respond_positive_view_modal).find('.modal-body').html(response.renderAjax);
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Добавление формы вопрос/ответ для проверки гипотезы проблемы
$(body).on('click', '.add_expectedResults_create_form', function(){

    var hypothesis_create_modal = $('.hypothesis_create_modal');
    var numberName = $('.item-expectedResults').children('.rowExpectedResults').last();
    numberName = $(numberName).children('div.field-EXR').last();
    numberName = $(numberName).children('.form-group').last();
    numberName = $(numberName).find('textarea');
    numberName = $(numberName).attr('id');
    var lastNumberItem = numberName.toString().slice(-1);
    lastNumberItem = Number.parseInt(lastNumberItem);
    var id = lastNumberItem + 1;

    var question_id = '_expectedResults_question_create-' + id;
    var expectedResultsInterview_question = $('#_expectedResults_question-');
    $(expectedResultsInterview_question).attr('name', 'FormCreateProblem[_expectedResultsInterview]['+id+'][question]');
    $(expectedResultsInterview_question).attr('id', question_id);

    var answer_id = '_expectedResults_answer_create-' + id;
    var expectedResultsInterview_answer = $('#_expectedResults_answer-');
    $(expectedResultsInterview_answer).attr('name', 'FormCreateProblem[_expectedResultsInterview]['+id+'][answer]');
    $(expectedResultsInterview_answer).attr('id', answer_id);

    var buttonRemoveId = 'remove-expectedResults-form-create-' + id;
    var remove_EXR = $('#remove-expectedResults-');
    $(remove_EXR).addClass('remove_expectedResults_for_create');
    $(remove_EXR).attr('id', buttonRemoveId);

    var formExpectedResults = $('#formExpectedResults');
    $(formExpectedResults).find('#' + question_id).html('');
    $(formExpectedResults).find('#' + answer_id).html('');

    $(formExpectedResults).find('.formExpectedResults_inputs').find('.rowExpectedResults').toggleClass('rowExpectedResults-').toggleClass('row-expectedResults-form-create-' + id);
    var str = $(formExpectedResults).find('.formExpectedResults_inputs').html();
    $(str).find('.rowExpectedResults').toggleClass('rowExpectedResults-').toggleClass('row-expectedResults-form-create-' + id);
    $(hypothesis_create_modal).find('.item-expectedResults').append(str);

    $(formExpectedResults).find('.formExpectedResults_inputs').find('.rowExpectedResults').toggleClass('row-expectedResults-form-create-' + id).toggleClass('rowExpectedResults-');
    $(formExpectedResults).find('#_expectedResults_question_create-' + id).attr('name', 'FormCreateProblem[_expectedResultsInterview][0][question]');
    $(formExpectedResults).find('#_expectedResults_answer_create-' + id).attr('name', 'FormCreateProblem[_expectedResultsInterview][0][answer]');

    $(formExpectedResults).find('#_expectedResults_question_create-' + id).attr('id', '_expectedResults_question-');
    $(formExpectedResults).find('#_expectedResults_answer_create-' + id).attr('id', '_expectedResults_answer-');

    $(formExpectedResults).find('#remove-expectedResults-form-create-' + id).removeClass('remove_expectedResults_for_create');
    $(formExpectedResults).find('#remove-expectedResults-form-create-' + id).attr('id', 'remove-expectedResults-');

});


//Добавление формы вопрос/ответ для проверки гипотезы проблемы в редактировании
$(body).on('click', '.add_expectedResults', function(){

    var hypothesis_update_modal = $('.hypothesis_update_modal');
    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[1];

    var item_expectedResults = $('.item-expectedResults-' + numberId);
    var numberName = $(item_expectedResults).children('.rowExpectedResults').last();
    numberName = $(numberName).children('div.field-EXR').last();
    numberName = $(numberName).children('.form-group').last();
    numberName = $(numberName).find('textarea');
    numberName = $(numberName).attr('id');
    var lastNumberItem = numberName.toString().slice(-1);
    lastNumberItem = Number.parseInt(lastNumberItem);
    var id = lastNumberItem + 1;

    var question_id = '_expectedResults_question-' + id;
    var expectedResultsInterview_question = $('#_expectedResults_question-');
    $(expectedResultsInterview_question).attr('name', 'FormUpdateProblem[_expectedResultsInterview]['+id+'][question]');
    $(expectedResultsInterview_question).attr('id', question_id);

    var answer_id = '_expectedResults_answer-' + id;
    var expectedResultsInterview_answer = $('#_expectedResults_answer-');
    $(expectedResultsInterview_answer).attr('name', 'FormUpdateProblem[_expectedResultsInterview]['+id+'][answer]');
    $(expectedResultsInterview_answer).attr('id', answer_id);

    var buttonRemoveId = 'remove-expectedResults-' + numberId + '_' + id;
    $('#remove-expectedResults-').attr('id', buttonRemoveId);

    var formExpectedResults = $('#formExpectedResults');
    $(formExpectedResults).find('.rowExpectedResults').toggleClass('rowExpectedResults-').toggleClass('row-expectedResults-' + numberId + '_' + id);
    var str = $(formExpectedResults).find('.formExpectedResults_inputs').html();
    $(hypothesis_update_modal).find('.item-expectedResults-' + numberId).append(str);

    $(formExpectedResults).find('#_expectedResults_question-' + id).attr('name', 'FormCreateProblem[_expectedResultsInterview][0][question]');
    $(formExpectedResults).find('#_expectedResults_answer-' + id).attr('name', 'FormCreateProblem[_expectedResultsInterview][0][answer]');

    $(formExpectedResults).find('#_expectedResults_question-' + id).attr('id', '_expectedResults_question-');
    $(formExpectedResults).find('#_expectedResults_answer-' + id).attr('id', '_expectedResults_answer-');

    $(formExpectedResults).find('#remove-expectedResults-' + numberId + '_' + id).attr('id', 'remove-expectedResults-');
    $(formExpectedResults).find('.rowExpectedResults').toggleClass('row-expectedResults-' + numberId + '_' + id).toggleClass('rowExpectedResults-');
});


//Удаление формы вопрос/ответ для проверки гипотезы проблемы
$(body).on('click', '.remove_expectedResults_for_create', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[4];

    var hypothesis_create_modal = $('.hypothesis_create_modal');
    $(hypothesis_create_modal).find('.row-expectedResults-form-create-' + numberId).remove();
    $('form#hypothesisCreateForm').trigger('change');
});


//Удаление формы вопрос-ответ
$(body).on('click', '.remove-expectedResults', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[2];

    if(arrId[3] || arrId[4]) {

        var expectedResultId;
        arrId[4] ? expectedResultId = arrId[4] : expectedResultId = arrId[3];
        var url = '/problems/delete-expected-results-interview?id=' + expectedResultId;

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            error: function(){
                alert('Ошибка');
            }
        });
    }

    var hypothesis_update_modal = $('.hypothesis_update_modal');
    $(hypothesis_update_modal).find('.row-expectedResults-' + numberId).remove();
});



// Показать форму завершения задания
$(body).on('click', '.showTaskFormComplete', function (e) {

    $('.blockTaskCompleteForm').toggle('display');
    $(this).toggle('display');

    e.preventDefault();
    return false;
});


// Скрыть форму завершения задания
$(body).on('click', '.hiddenTaskFormComplete', function (e) {

    $('.blockTaskCompleteForm').toggle('display');
    $('.showTaskFormComplete').toggle('display');

    e.preventDefault();
    return false;
});


// Сохранение формы завершения задания исполнителем
$(body).on('beforeSubmit', 'form#completeTaskForm', function (e) {

    var form = $(this);
    var url = $(this).attr('action');
    var formData = new FormData(form[0]);

    $.ajax({
        url: url,
        method: 'POST',
        processData: false,
        contentType: false,
        data:  formData,
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


//Добавление файлов при завершении задания
$(body).on('change', 'input[type=file]',function(){

    for (var i = 0; i < this.files.length; i++) {
        console.log(this.files[i].name);
    }

    //Количество добавленных файлов
    var add_count = this.files.length;

    if(add_count > 5) {
        //Сделать кнопку отправки формы не активной
        $(body).find('#submitTaskComplete').attr('disabled', true);
        $(body).find('.error_files_count').show();
    }else {
        //Сделать кнопку отправки формы активной
        $(body).find('#submitTaskComplete').attr('disabled', false);
        $(body).find('.error_files_count').hide();
    }

});


//Добавление файлов при повторном завершении задания
$(body).on('change', 'input[type=file]',function(){

    for (var i = 0; i < this.files.length; i++) {
        console.log(this.files[i].name);
    }

    //Количество файлов уже загруженных
    var count_exist_files = $(body).find('.block_all_files').children('div').length;
    //Общее количество файлов
    var countAllFiles = this.files.length + count_exist_files;

    if(countAllFiles > 5) {
        //Сделать кнопку отправки формы не активной
        $(body).find('#submitTaskComplete').attr('disabled', true);
        $(body).find('.error_files_count').show();
    }else {
        //Сделать кнопку отправки формы активной
        $(body).find('#submitTaskComplete').attr('disabled', false);
        $(hypothesis_update_modal).find('.error_files_count').hide();
    }

});


//Удаление файла при повторном завершении задания
$(body).on('click', '.delete_file', function(e){

    var deleteFileId = $(this).attr('id');
    deleteFileId = deleteFileId.split('-');
    deleteFileId = deleteFileId[1];
    var url = $(this).attr('href');

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            if (response.success) {

                //Удаляем блок с файлом
                $(body).find('.one_block_file-' + deleteFileId).remove();

                if (response.count_files === 4){
                    $(body).find('.add_files').show();
                    $(body).find('.add_max_files_text').hide();
                }
            }
        }, error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});
