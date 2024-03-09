//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));
var module = (window.location.pathname).split('/')[1];

$(document).ready(function() {

    //Фон для модального окна (при создании MVP - недостаточно данных)
    var info_hypothesis_create_modal_error = $('.hypothesis_create_modal_error').find('.modal-content');
    info_hypothesis_create_modal_error.css('background-color', '#707F99');

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
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Разработка MVP</div>');
            }
            $(modal).find('.modal-body').html(response);
            $(modal).modal('show');
        }
    });

    e.preventDefault();
    return false;
});


//Отслеживаем изменения в форме создания MVP и записываем их в кэш
$(body).on('change', 'form#hypothesisCreateForm', function(){

    var url, data
    if (module === 'contractor') {
        url = '/mvps/save-cache-creation-form?id=0&taskId=' + id_page;
    } else {
        url = '/mvps/save-cache-creation-form?id=' + id_page;
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


//При попытке добавить MVP проверяем существуют ли необходимые данные
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
            }else{
                $(hypothesis_create_modal_error).modal('show');
            }
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
                    location.href = '/contractor/mvps/task?id=' + id;
                } else {
                    location.href = '/mvps/index?id=' + id;
                }
            } else {
                $('.hypothesis_create_modal').modal('hide');
                $('.block_all_hypothesis').html(response.renderAjax);
            }
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
        }
    });

    e.preventDefault();
    return false;
});


var catchChange = false;
//Отслеживаем изменения в форме редактирования MVP
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

//Подтверждение закрытия окна редактирования MVP
$(body).on('click', '#button_confirm_closing_modal', function (e) {
    catchChange = false;
    $('#confirm_closing_update_modal').modal('hide');
    $('.hypothesis_update_modal').modal('hide');
    e.preventDefault();
    return false;
});


//Редактирование гипотезы ценностного предложения
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