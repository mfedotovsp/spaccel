//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

$(document).ready(function() {

    //Фон для модального окна (при создании Бизнес-модели - недостаточно данных)
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
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Этап 9. Разработка бизнес-модели</div>');
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


//Отслеживаем изменения в форме создания Бизнес-модели и записываем их в кэш
$(body).on('change', 'form#hypothesisCreateForm', function(){

    var url = '/business-model/save-cache-creation-form?id=' + id_page;
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


//При попытке добавить Бизнес-модель проверяем существуют ли необходимые данные
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

            if (response.success) {
                $('.hypothesis_create_modal').modal('hide');
                location.href = '/business-model/index?id=' + id;
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
//Отслеживаем изменения в форме редактирования Бизнес-модели
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

//Подтверждение закрытия окна редактирования Бизнес-модели
$(body).on('click', '#button_confirm_closing_modal', function (e) {
    catchChange = false;
    $('#confirm_closing_update_modal').modal('hide');
    $('.hypothesis_update_modal').modal('hide');
    e.preventDefault();
    return false;
});


//Редактирование Бизнес-модели
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
            $('.container-business_model').html(response.renderAjax);
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

            $('.container-business_model').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});