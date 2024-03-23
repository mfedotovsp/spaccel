//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));
const taskId = window.location.search.split('=')[1];
const action = window.location.pathname.split('/')[3];
var body = $('body');


//Выполнить при загрузке страницы
$(document).ready(function () {
    if ($(window).width() <= '480') {
        $('.interview-view').remove();
        $('.confirm-hypothesis-step-three-mobile').toggle('display');
    } else {
        $('.confirm-hypothesis-view-mobile').remove();
    }

    getQueryResponds();
    getQueryProducts();

    if ($(window).width() <= '480') {
        $(body).find('.header-title-confirm-hypothesis-mobile').after(function () {
            return '<div class="switches-between-responds-products">' +
                '<div class="switch-button active" id="showMobileListResponds">Респонденты</div>' +
                '<div class="switch-button" id="showMobileListProducts">Продукты</div>' +
                '</div>';
        });
    }
});

$(window).resize(function () {
    if ($(window).width() <= '480' && $('.interview-view').length > 0) {
        location.reload();
        $('.interview-view').remove();
    } else if ($(window).width() > '480' && $('.confirm-hypothesis-view-mobile').length > 0) {
        location.reload();
        $('.confirm-hypothesis-view-mobile').remove();
    }
});

// Получение списка респондентов
function getQueryResponds() {
    var url;
    if ($(window).width() <= '480') {
        if (action === 'view-trash') {
            url = '/responds/get-query-responds?stage=2&id=' + taskId + '&isMobile=true&isOnlyNotDelete=false&isModuleContractor=true';
        } else {
            url = '/responds/get-query-responds?stage=2&id=' + taskId + '&isMobile=true&isModuleContractor=true' ;
        }
    } else {
        if (action === 'view-trash') {
            url = '/responds/get-query-responds?stage=2&id=' + taskId + '&isOnlyNotDelete=false&isModuleContractor=true';
        } else {
            url = '/responds/get-query-responds?stage=2&id=' + taskId + '&isModuleContractor=true';
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


// Получение списка продуктов
function getQueryProducts() {
    var url;
    if ($(window).width() <= '480') {
        url = '/contractor/products/get-product-list?taskId=' + taskId + '&isMobile=true';
    } else {
        url = '/contractor/products/get-product-list?taskId=' + taskId;
    }

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if ($(window).width() <= '480') {
                $('.confirm-stage-mobile-list-products > .content_products_ajax').html(response.renderAjax);
            } else {
                $('.content_products_ajax').html(response.renderAjax);
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
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Этап 2. Подтверждение гипотез целевых сегментов</div>');
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


//Поиск респондентов в мобильной версии
$(body).on('input', 'input#search_input_responds_mobile', function () {
    var search = $('input#search_input_responds_mobile').val();
    var url = '/responds/get-query-responds?stage=2&id=' + taskId + '&search=' + search + '&isMobile=true&isModuleContractor=true';

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $('.confirm-hypothesis-step-three-mobile > .content_responds_ajax').html(response.ajax_data_responds);
        }
    });
});


//Получение формы редактирования данных репондента
$(body).on('click', '.showRespondUpdateForm', function(e){

    var id = $(this).attr('id').split('-')[1];
    var url = '/contractor/responds/get-data-update-form?stage=2&id=' + id + '&isOnlyNotDelete=false';

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


//Получение формы редактирования интервью респондента
$(body).on('click', '.showDescInterviewUpdateForm', function(e){

    var id = $(this).attr('id').split('-')[1];
    var url = '/interviews/get-data-update-form?stage=2&id=' + id + '&isOnlyNotDelete=false';
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


//Показать таблицу ответов на вопросы интервью
$(body).on('click', '.openTableQuestionsAndAnswers', function (e) {

    var url = $(this).attr('href') + '&isOnlyNotDelete=false';
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


//Получение формы создания продукта
$(body).on('click', '#showProductCreateForm', function(e){

    var url = $(this).attr('href');
    var productCreate_modal = $('#productCreate_modal');
    $(body).append($(productCreate_modal).first());

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(productCreate_modal).find('.modal-body').html(response.renderAjax);
            $(productCreate_modal).modal('show');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//При создании нового продукта из формы
$(body).on('beforeSubmit', '#formCreateContractorTaskProduct', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    if ($(window).width() <= '480') {
        url = url + '&isMobile=true';
    }
    var productCreate_modal = $('#productCreate_modal');

    $.ajax({

        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            $(productCreate_modal).modal('hide');
            if ($(window).width() <= '480') {
                $('.confirm-stage-mobile-list-products > .content_products_ajax').html(response.renderAjax);
            } else {
                $('.content_products_ajax').html(response.renderAjax);
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Показ и скрытие описания продукта
$(body).on('click', '.container-one_product', function(){
    var taskProductId = $(this).attr('id');
    if ($(this).hasClass('active')) {
        $(this).removeClass('active');
    } else {
        $(this).addClass('active');
    }

    $('.' + taskProductId).toggle('display');
});


//Получение формы редактирования продукта
$(body).on('click', '.showTaskProductUpdateForm', function(e){

    var id = $(this).attr('id').split('-')[1];
    var url = $(this).attr('href');
    var update_product_modal = $('#productUpdate_modal');
    $(body).append($(update_product_modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(update_product_modal).find('.modal-body').html(response.renderAjax);
            $(update_product_modal).modal('show');
        }
    });

    e.preventDefault();
    return false;
});


//При редактировании продукта из формы
$(body).on('beforeSubmit', '#formUpdateContractorTaskProduct', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    if ($(window).width() <= '480') {
        url = url + '&isMobile=true';
    }
    var update_product_modal = $('#productUpdate_modal');

    $.ajax({

        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            $(update_product_modal).modal('hide');
            if ($(window).width() <= '480') {
                $('.confirm-stage-mobile-list-products > .content_products_ajax').html(response.renderAjax);
            } else {
                $('.content_products_ajax').html(response.renderAjax);
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Вызов модального окна удаления продукта
$(body).on('click', '.showDeleteTaskProductModal', function(e){

    var url = $(this).attr('href');
    var delete_product_modal = $('#delete-product-modal');
    $(body).append($(delete_product_modal).first());

    $(delete_product_modal).find('.modal-body h4').html('Вы уверены, что хотите удалить продукт ?');
    $(delete_product_modal).find('.modal-footer #confirm-delete-product').attr('href', url);
    $(delete_product_modal).modal('show');

    e.preventDefault();
    return false;
});


// CONFIRM PRODUCT DELETE
$(body).on('click', '#confirm-delete-product', function(e) {

    $.ajax({

        url: $(this).attr('href'),
        method: 'POST',
        cache: false,
        success: function(response) {

            if (response.success) {
                getQueryProducts();
                //Закрываем окно подтверждения
                $('#delete-product-modal').modal('hide');
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// CANCEL PRODUCT DELETE
$(body).on('click', '#cancel-delete-product', function(e) {

    //Закрываем окно подтверждения
    $('#delete-product-modal').modal('hide');

    e.preventDefault();
    return false;
});


// Показ и скрытие списка респондентов
$(body).on('click', '#buttonToggleResponds', function() {

    $('.content_responds_ajax').toggle('display');
    $('.tableRespondHeaders').toggle('display');
    if ($(this).text() === 'Показать список') {
        $(this).text('Скрыть список');
        $('.row.row_products_header_data').css('margin-top', '-120px');
    } else {
        $(this).text('Показать список');
        $('.row.row_products_header_data').css('margin-top', 0);
    }
});


// Показать список респондентов
$(body).on('click', '#showMobileListResponds', function() {
    if (!$(this).hasClass('active')) {
        $('.confirm-stage-mobile-list-products').toggle('display');
        $('.confirm-stage-mobile-list-responds').toggle('display');
        $('#showMobileListProducts').removeClass('active');
        $(this).addClass('active');
    }
});


// Показать список продуктов
$(body).on('click', '#showMobileListProducts', function() {
    if (!$(this).hasClass('active')) {
        $('.confirm-stage-mobile-list-responds').toggle('display');
        $('.confirm-stage-mobile-list-products').toggle('display');
        $('#showMobileListResponds').removeClass('active');
        $(this).addClass('active');
    }
});
