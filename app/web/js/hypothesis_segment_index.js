//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));
var module = (window.location.pathname).split('/')[1];

$(document).ready(function() {

    // Проверка установленного значения B2C/B2B
    setInterval(function(){

        if($('#select2-type-interaction-container').html() === 'B2C'){

            $('.form-template-b2b').hide();
            $('.form-template-b2c').show();
        }

        else {

            $('.form-template-b2b').show();
            $('.form-template-b2c').hide();
        }

    }, 1000);


    //Фон для модального окна информации (сегмент с таким именем уже существует)
    var segment_already_exists_modal = $('#segment_already_exists').find('.modal-content');
    segment_already_exists_modal.css('background-color', '#707F99');

    //Фон для модального окна информации (данные не загружены)
    var data_not_loaded_modal = $('#data_not_loaded').find('.modal-content');
    data_not_loaded_modal.css('background-color', '#707F99');


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
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Генерация гипотез целевых сегментов</div>');
            }
            $(modal).find('.modal-body').html(response);
            $(modal).modal('show');
        }
    });

    e.preventDefault();
    return false;
});


//Отслеживаем изменения в форме создания сегмента и записываем их в кэш
$(body).on('change', 'form#hypothesisCreateForm', function(){

    var url, data;
    if (module === 'contractor') {
        url = '/segments/save-cache-creation-form?id=0&taskId=' + id_page;
    } else {
        url = '/segments/save-cache-creation-form?id=' + id_page;
    }
    data = $(this).serialize();
    $.ajax({url: url, data: data, method: 'POST'});
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


//Отслеживаем изменения значения чекбокса use_wish_list в форме создания сегмента
$(body).on('input', '#use_wish_list', function(){

    var useWishListValue;
    if ($('#use_wish_list:checked').val() == 1) {
        useWishListValue = true;
    } else {
        useWishListValue = false;
    }
    var url = $('#showHypothesisToCreate').attr('href') + '&useWishList=' + useWishListValue;
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    $(body).append($(hypothesis_create_modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(hypothesis_create_modal).find('.modal-body').html(response.renderAjax);

            // Расчет платежеспособности B2B
            var incomeFromB2B = parseInt($("input#income_from_b2b").val());
            var incomeToB2B = parseInt($("input#income_to_b2b").val());
            var quantityB2B = parseInt($("input#quantity_b2b").val());
            if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                $("input#market_volume_b2b").val(Math.round(resB2B));
            }
        }
    });
});


// Отслеживаем выбор запроса b2b компании в форме создания сегмента
$(body).on('click', '.select-requirement', function(e){

    $('.form-create-segment-b2b-with-requirement').toggle('display');
    var url = $(this).attr('href');
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    $(body).append($(hypothesis_create_modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(hypothesis_create_modal).find('.modal-body').html(response.renderAjax);
            $('form#hypothesisCreateForm').trigger('change');

            // Расчет платежеспособности B2B
            var incomeFromB2B = parseInt($("input#income_from_b2b").val());
            var incomeToB2B = parseInt($("input#income_to_b2b").val());
            var quantityB2B = parseInt($("input#quantity_b2b").val());
            if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                $("input#market_volume_b2b").val(Math.round(resB2B));
            }
        }
    });

    e.preventDefault();
    return false;
});


$(body).on('click', '.show-details-select-requirement', function(e){

    $('.details-select-requirement').toggle('display');
    if ($(this).text() === 'Подробнее о запросе') {
        $(this).text('Скрыть детали запроса');
    } else {
        $(this).text('Подробнее о запросе');
    }

    e.preventDefault();
    return false;
});


//При нажатии на кнопку новый сегмент
$(body).on('click', '#showHypothesisToCreate', function(e){

    var url = $(this).attr('href');
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    $(body).append($(hypothesis_create_modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(hypothesis_create_modal).modal('show');
            $(hypothesis_create_modal).find('.modal-body').html(response.renderAjax);

            // Расчет платежеспособности B2C
            var incomeFrom = parseInt($("input#income_from").val());
            var incomeTo = parseInt($("input#income_to").val());
            var quantity = parseInt($("input#quantity").val());
            if (incomeFrom > 0 && incomeTo > 0 && quantity > 0) {
                var res = ((incomeFrom + incomeTo) * 6) * quantity / 1000000;
                $("input#market_volume_b2c").val(Math.round(res));
            }

            // Расчет платежеспособности B2B
            var incomeFromB2B = parseInt($("input#income_from_b2b").val());
            var incomeToB2B = parseInt($("input#income_to_b2b").val());
            var quantityB2B = parseInt($("input#quantity_b2b").val());
            if (incomeFromB2B > 0 && incomeToB2B > 0 && quantityB2B > 0) {
                var resB2B = ((incomeFromB2B + incomeToB2B) / 2) * quantityB2B;
                $("input#market_volume_b2b").val(Math.round(resB2B));
            }
        }
    });

    e.preventDefault();
    return false;
});


// Показать/Скрыть список запросов b2b компаний в форме создания сегмента
$(body).on('click', '#showListRequirements', function(e){

    $('.form-create-segment-b2b-with-requirement').toggle('display');
    var url = $(this).attr('href');
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    var listRequirements = $(hypothesis_create_modal).find('.modal-body').find('.list-requirements');

    if ($(this).text() === 'Показать список запросов') {

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function(response){
                $(listRequirements).html(response.renderAjax);
            }
        });

        $(this).text('Назад');
        $(this).css('width', '100px');

    } else {
        $(listRequirements).html('');
        $(this).text('Показать список запросов');
        $(this).css('width', '240px');
    }

    e.preventDefault();
    return false;
});

// Показать информацию о запросе b2b компании
$(body).on('click', '.container-one_requirement', function(){
    if ($(this).hasClass('active')) {
        $(this).removeClass('active');
    } else {
        $(this).addClass('active');
    }
    $(this).find('.details-requirement').toggle('display');
});

//При нажатии на кнопку добавить фильтры по запросам B2B компаний
$(body).on('click', '#addFiltersForListRequirements', function (){
    $('.addFiltersForListRequirements').toggle('display');
    $('.buttonsFiltersForListRequirements').toggle('display');
    $('.requirement-filters').toggle('display');
});

//Применение фильтров по запросам B2B компаний
$(body).on('beforeSubmit', 'form#filtersRequirement', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    var listRequirements = $(hypothesis_create_modal).find('.modal-body').find('.list-requirements');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            $(listRequirements).html(response.renderAjax);
            $('#addFiltersForListRequirements').trigger('click');
        }
    });

    e.preventDefault();
    return false;
});

// Сброс фильтров по запросам B2B компаний
$(body).on('click', '#resetFiltersForListRequirements', function (e){

    var url = $(this).attr('href');
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    var listRequirements = $(hypothesis_create_modal).find('.modal-body').find('.list-requirements');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $(listRequirements).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});

//Пагинация списка запросов B2B компаний
$(body).on('click', '.admin-projects-result-pagin-list li a', function (e){

    var url = $(this).attr('href');
    var data = $('form#filtersRequirement').serialize();
    var hypothesis_create_modal = $('.hypothesis_create_modal');
    var listRequirements = $(hypothesis_create_modal).find('.modal-body').find('.list-requirements');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            $(listRequirements).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});

//Сохранение новой гипотезы из формы
$(body).on('beforeSubmit', '#hypothesisCreateForm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    var id = url.split('=')[1];

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            //Если данные загружены и проверены
            if(response.success){

                if (response.count === '1') {
                    $('.hypothesis_create_modal').modal('hide');
                    if (module === 'contractor') {
                        location.href = '/contractor/segments/task?id=' + id;
                    } else {
                        location.href = '/segments/index?id=' + id;
                    }
                } else {
                    $('.hypothesis_create_modal').modal('hide');
                    $('.block_all_hypothesis').html(response.renderAjax);
                }
            }

            //Если сегмент с таким именем уже существует
            if(response.segment_already_exists){

                var segment_already_exists = $('#segment_already_exists');
                $(body).append($(segment_already_exists).first());
                $(segment_already_exists).modal('show');
            }

            //Если данные не загружены
            if(response.data_not_loaded){

                var data_not_loaded = $('#data_not_loaded');
                $(body).append($(data_not_loaded).first());
                $(data_not_loaded).modal('show');
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
            $(hypothesis_update_modal).find('.modal-body').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


var catchChange = false;
//Отслеживаем изменения в форме редактирования сегмента
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


//Подтверждение закрытия окна редактирования сегмента
$(body).on('click', '#button_confirm_closing_modal', function (e) {
    catchChange = false;
    $('#confirm_closing_update_modal').modal('hide');
    $('.hypothesis_update_modal').modal('hide');
    e.preventDefault();
    return false;
});


//Редактирование гипотезы целевого сегмента
$(body).on('beforeSubmit', '#hypothesisUpdateForm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            //Если данные загружены и проверены
            if(response.success){

                if (catchChange === true) catchChange = false;
                $('.hypothesis_update_modal').modal('hide');
                $('.block_all_hypothesis').html(response.renderAjax);
            }

            //Если сегмент с таким именем уже существует
            if(response.segment_already_exists){

                var segment_already_exists = $('#segment_already_exists');
                $(body).append($(segment_already_exists).first());
                $(segment_already_exists).modal('show');
            }

            //Если данные не загружены
            if(response.data_not_loaded){

                var data_not_loaded = $('#data_not_loaded');
                $(body).append($(data_not_loaded).first());
                $(data_not_loaded).modal('show');
            }
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


// Показать форму поиска сегментов
$(body).on('click', '.show_search_segments', function (e) {

    $('.search_block_mobile').toggle('display');
    $('.row_header_data_generation_mobile').toggle('display');
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
