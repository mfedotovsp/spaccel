//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));


var body = $('body'),
    id_page = window.location.search.split('=')[1],
    confirm_count_respond = $(body).find('input#confirm_count_respond'),
    confirm_add_count_respond = $(body).find('input#confirm_add_count_respond'),
    confirm_count_positive = $(body).find('input#confirm_count_positive'),
    exist_desc = window.location.search.split('=')[2] ? window.location.search.split('=')[2] : false,
    selectedSourceOptions = $('.select-confirm-source').val();


//Форма создания модели подтверждения
$(body).on('beforeSubmit', '#new_confirm_problem', function(e){

    var form = $(this);
    var formData = new FormData(form[0]);
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response){

            if (response.success) {
                if (!exist_desc) {
                    window.location.href = '/confirm-problem/add-questions?id=' + response.id;
                } else {
                    $.ajax({
                        url: '/confirm-problem/exist-confirm?id=' + response.id,
                        method: 'POST',
                    });
                }
            } else {
                var errStr = 'Обратите внимание!<br/>';
                response.errors.forEach((val, index) => {
                    errStr += (index+1) + '. ' + val + ' <br/>';
                });
                $(body).find('.errors').html(errStr);
            }
        }
    });

    e.preventDefault();
    return false;
});


// Отслеживаем изменения в поле add_count_respond
$(confirm_add_count_respond).change(function () {

    var value1 = $(this).val(),
        value2 = $(confirm_count_respond).val(),
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
});


// Отслеживаем изменения в поле count_positive
$(confirm_count_positive).change(function () {

    var valueMin = 1,
        valueMax = 100,
        value = $(this).val();

    if (parseInt(value) > parseInt(valueMax)){
        value = valueMax;
        $(this).val(value);
    }

    if (parseInt(value) < parseInt(valueMin)){
        value = valueMin;
        $(this).val(value);
    }
});


// Переключатель для поля добавления новых респондентов
$(body).on('click', '#switch_add_count_respond', function () {

    var field = $('#confirm_add_count_respond'),
        form = $('form#new_confirm_problem'),
        changeBtnContent = $(this).find('.changeBtnContent');

    if (typeof $(field).attr('readonly') === 'undefined') {
        $(field).attr('readonly', true).val(0);
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


//Отслеживаем изменения в форме создания подтверждения и записываем их в кэш
$(body).on('change', 'form#new_confirm_problem', function(){

    var url = '/confirm-problem/save-cache-creation-form?id=' + id_page + '&existDesc=' + exist_desc;
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
var information_add_new_responds_modal = $('#information-add-new-responds');

//Показываем модальное окно - запрет перехода на следующий шаг
$(body).on('click', '.show_modal_next_step_error', function (e) {

    $(body).append($(modal_next_step_error).first());
    $(modal_next_step_error).modal('show');

    e.preventDefault();
    return false;
});


//Добавление файлов
$(body).on('change', 'input[type=file]',function() {

    for (var i = 0; i < this.files.length; i++) {
        console.log(this.files[i].name);
    }

    //Количество добавленных файлов
    var add_count = this.files.length;

    if(add_count > 5) {
        //Сделать кнопку отправки формы не активной
        $(body).find('#save_create_form').attr('disabled', true);
        $(body).find('.error_files_count').show();
    }else {
        //Сделать кнопку отправки формы активной
        $(body).find('#save_create_form').attr('disabled', false);
        $(body).find('.error_files_count').hide();
    }
});


// Отслеживаем выбор источников информации для описания подтверждения
$(body).on('change', '.select-confirm-source', function () {
    var action = 'add';
    var diff, values;

    values = Array.from(this.selectedOptions).map(({ value }) => value);
    if (values.length < selectedSourceOptions.length) action = 'delete';
    if (action === 'add') diff = values.filter(element => !selectedSourceOptions.includes(element));
    else diff = selectedSourceOptions.filter(element => !values.includes(element));
    diff.forEach(val => $('.select-confirm-source-option-' + val).toggle('display'));
    selectedSourceOptions = values;
});
