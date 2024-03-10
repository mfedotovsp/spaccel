//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));


var body = $('body'),
    id_page = window.location.search.split('=')[1],
    confirm_count_respond = $(body).find('input#confirm_count_respond'),
    confirm_add_count_respond = $(body).find('input#confirm_add_count_respond'),
    confirm_count_positive = $(body).find('input#confirm_count_positive');


//Форма создания модели подтверждения
$('#new_confirm_gcp').on('beforeSubmit', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            if (response.success) {

                window.location.href = '/confirm-gcp/add-questions?id=' + response.id;
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Отслеживаем изменения в поле add_count_respond
$(confirm_add_count_respond).change(function () {

    var value1 = $(this).val(),
        value2 = $(confirm_count_respond).val(),
        value3 = $(confirm_count_positive).val(),
        valueMax = 100,
        valueMin = 1;

    if (parseInt(value1) > (parseInt(valueMax) - parseInt(value2))){
        value1 = (parseInt(valueMax) - parseInt(value2));
        $(this).val(value1);
    }
    if (parseInt(value1) < parseInt(valueMin)){
        value1 = valueMin;
        $(this).val(value1);
    }

    if (parseInt(value3) > (parseInt(value1) + parseInt(value2))) {
        value3 = (parseInt(value1) + parseInt(value2));
        $(confirm_count_positive).val(value3);
    }
});


// Отслеживаем изменения в поле count_positive
$(confirm_count_positive).change(function () {

    var valueMin = 1,
        value1 = $(this).val(),
        value2;

    if ($(confirm_add_count_respond).val() !== ''){
        value2 = (parseInt($(confirm_count_respond).val()) + parseInt($(confirm_add_count_respond).val()));
    } else {
        value2 = parseInt($(confirm_count_respond).val());
    }

    if (parseInt(value1) > parseInt(value2)){
        value1 = value2;
        $(this).val(value1);
    }

    if (parseInt(value1) < parseInt(valueMin)){
        value1 = valueMin;
        $(this).val(value1);
    }
});


// Переключатель для поля добавления новых респондентов
$(body).on('click', '#switch_add_count_respond', function () {

    var field = $('#confirm_add_count_respond'),
        form = $('form#new_confirm_problem'),
        changeBtnContent = $(this).find('.changeBtnContent');

    if (typeof $(field).attr('readonly') === 'undefined') {
        $(field).attr('readonly', true).val(0);
        if (parseInt($(confirm_count_respond).val()) < parseInt($(confirm_count_positive).val()))
            $(confirm_count_positive).val($(confirm_count_respond).val());
        // Обновляем кэш на формы
        $(form).trigger('change');
        $(changeBtnContent).html('Добавить новых респондентов');
    } else {
        $(field).removeAttr('readonly');
        $(changeBtnContent).html('Не добавлять новых респондентов');
    }
});


// Если в кэше есть значение для поля add_count_respond,
// то делаем его активным
$(document).ready(function () {
    if ($(confirm_add_count_respond).val() !== '') {
        $('#switch_add_count_respond').trigger('click');
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


//Отслеживаем изменения в форме создания подтверждения и записываем их в кэш
$(body).on('change', 'form#new_confirm_gcp', function(){

    var url = '/confirm-gcp/save-cache-creation-form?id=' + id_page;
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


var modal_next_step_error = $('#next_step_error');

//Показываем модальное окно - запрет перехода на следующий шаг
$(body).on('click', '.show_modal_next_step_error', function (e) {

    $(body).append($(modal_next_step_error).first());
    $(modal_next_step_error).modal('show');

    e.preventDefault();
    return false;
});
