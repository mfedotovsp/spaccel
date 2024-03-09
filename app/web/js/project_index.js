//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

$(document).ready(function() {

    //Фон для модального окна информации (проект с таким именем уже существует)
    var project_already_exists_modal = $('#project_already_exists').find('.modal-content');
    project_already_exists_modal.css('background-color', '#707F99');

});


var body = $('body');
var id_page = window.location.search.split('=')[1];


var hypothesis_create_modal = $('.hypothesis_create_modal');
$(body).append($(hypothesis_create_modal).first());
var hypothesis_update_modal = $('.hypothesis_update_modal');
$(body).append($(hypothesis_update_modal).first());


//Возвращение скролла первого модального окна после закрытия
$('#project_already_exists').on('hidden.bs.modal', function(){
    $(body).addClass('modal-open');
});
$('#confirm_closing_update_modal').on('hidden.bs.modal', function(){
    $(body).addClass('modal-open');
});
$('.modal_instruction_page').on('hidden.bs.modal', function(){
    $(body).addClass('modal-open');
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
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Формулировка проекта</div>');
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


// Показать форму поиска проектов
$(body).on('click', '.show_search_projects', function (e) {

    $('.search_block_mobile').toggle('display');
    $('.row_header_data_generation_mobile').toggle('display');
    e.preventDefault();
    return false;
});


//Отслеживаем изменения в форме создания проекта и записываем их в кэш
$(body).on('change', 'form#project_create_form', function(){

    var url = '/projects/save-cache-creation-form?id=' + id_page;
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


// Показать данные корзины
$(body).on('click', '#show_trash_list', function(e){

    var url = $(this).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $('.block_all_projects_user').html(response.renderAjax);
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

            $('.block_all_projects_user').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


//При нажатии на кнопку Новый проект
$(body).on('click', '#showHypothesisToCreate', function(e){

    var url = $(this).attr('href');

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(hypothesis_create_modal).modal('show');
            $(hypothesis_create_modal).find('.modal-body').html(response.renderAjax);

            //Заполнение полей формы данными из кэша
            if (response.cache) {

                // Данные из кэша к полям модели Authors
                var form = response.cache.Authors;
                // Перезаписать ключи массива,
                // т.к. некоторые элементы могут быть удалены
                // и идти не порядку и в этом случае не будут показаны
                var formAuthors = [];
                $.each(form, function(index, val) {
                    formAuthors.push( val );
                });

                // Добавляем формы для авторов, если их больше одного
                var countOfAdditionalForms = formAuthors.length - 1;
                if (countOfAdditionalForms > 0) {
                    for (var i = 0; i < countOfAdditionalForms; i++) {
                        $('.add_author_create_form').trigger('click');
                    }
                }

                // Добаляем данные из кэша к полям модели Authors
                formAuthors.forEach(function(item, i) {
                    $(document.getElementsByName('Authors['+i+'][fio]')).val(item.fio);
                    $(document.getElementsByName('Authors['+i+'][role]')).val(item.role);
                    $(document.getElementsByName('Authors['+i+'][experience]')).val(item.experience);
                });
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});



//Сохранение нового проекта из формы
$(body).on('beforeSubmit', '#project_create_form', function(e){

    var form = $(this);
    var url = form.attr('action');
    var id = url.split('=')[1];
    var formData = new FormData(form[0]);
    formData.append('type_sort_id', $('#listType').val());

    $.ajax({

        url: url,
        method: 'POST',
        processData: false,
        contentType: false,
        data:  formData,
        cache: false,
        success: function(response){

            //Если данные загружены и проверены
            if(response.success){

                if (response.count === 1) {
                    $(hypothesis_create_modal).modal('hide');
                    location.href = '/projects/index?id=' + id;
                } else {
                    $(hypothesis_create_modal).modal('hide');
                    $(body).find('.block_all_projects_user').html(response.renderAjax);
                }
            }

            //Если проект с таким именем уже существует
            if(response.project_already_exists){

                var project_already_exists = $('#project_already_exists');
                $(body).append($(project_already_exists).first());
                $(project_already_exists).modal('show');
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
//Отслеживаем изменения в форме редактирования проекта
$(body).on('change', 'form#project_update_form', function(){
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

//Подтверждение закрытия окна редактирования проекта
$(body).on('click', '#button_confirm_closing_modal', function (e) {
    catchChange = false;
    $('#confirm_closing_update_modal').modal('hide');
    $('.hypothesis_update_modal').modal('hide');
    e.preventDefault();
    return false;
});


//Редактирование проекта
$(body).on('beforeSubmit', '#project_update_form', function(e){

    var form = $(this);
    var url = form.attr('action');
    var formData = new FormData(form[0]);
    formData.append('type_sort_id', $('#listType').val());

    $.ajax({

        url: url,
        method: 'POST',
        processData: false,
        contentType: false,
        data:  formData,
        cache: false,
        success: function(response){

            //Если данные загружены и проверены
            if(response.success){

                if (catchChange === true) catchChange = false;
                $(hypothesis_update_modal).modal('hide');
                $('.block_all_projects_user').html(response.renderAjax);
            }

            //Если проект с таким именем уже существует
            if(response.project_already_exists){

                var project_already_exists = $('#project_already_exists');
                $(body).append($(project_already_exists).first());
                $(project_already_exists).modal('show');
            }
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

            $('.block_all_projects_user').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


//Добавление файлов при создании проекта
$(hypothesis_create_modal).on('change', 'input[type=file]',function(){

    for (var i = 0; i < this.files.length; i++) {
        console.log(this.files[i].name);
    }

    //Количество добавленных файлов
    var add_count = this.files.length;

    if(add_count > 5) {
        //Сделать кнопку отправки формы не активной
        $(hypothesis_create_modal).find('#save_create_form').attr('disabled', true);
        $(hypothesis_create_modal).find('.error_files_count').show();
    }else {
        //Сделать кнопку отправки формы активной
        $(hypothesis_create_modal).find('#save_create_form').attr('disabled', false);
        $(hypothesis_create_modal).find('.error_files_count').hide();
    }

});


//Добавление файлов при редактировании проекта
$(hypothesis_update_modal).on('change', 'input[type=file]',function(){

    for (var i = 0; i < this.files.length; i++) {
        console.log(this.files[i].name);
    }

    //Количество файлов уже загруженных
    var count_exist_files = $(hypothesis_update_modal).find('.block_all_files').children('div').length;
    //Общее количество файлов
    var countAllFiles = this.files.length + count_exist_files;

    if(countAllFiles > 5) {
        //Сделать кнопку отправки формы не активной
        $(hypothesis_update_modal).find('#save_update_form').attr('disabled', true);
        $(hypothesis_update_modal).find('.error_files_count').show();
    }else {
        //Сделать кнопку отправки формы активной
        $(hypothesis_update_modal).find('#save_update_form').attr('disabled', false);
        $(hypothesis_update_modal).find('.error_files_count').hide();
    }

});


//Удаление файла из проекта
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
                $(hypothesis_update_modal).find('.one_block_file-' + deleteFileId).remove();

                if (response.count_files === 4){
                    $(hypothesis_update_modal).find('.add_files').show();
                    $(hypothesis_update_modal).find('.add_max_files_text').hide();
                }
            }
        }, error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});



//Добавление формы автора проекта при создании
$(body).on('click', '.add_author_create_form', function(){

    var numberName = $('.item-authors').children('.row-author').last();
    numberName = $(numberName).children('.form-group').last();
    numberName = $(numberName).children('div').last();
    numberName = $(numberName).find('textarea');
    numberName = $(numberName).attr('id');
    var lastNumberItem = numberName.toString().slice(-1);
    lastNumberItem = Number.parseInt(lastNumberItem);
    var id = lastNumberItem + 1;

    var fio_id = 'author_fio_create-' + id;
    var author_fio = $('#author_fio-');
    $(author_fio).attr('name', 'Authors['+id+'][fio]');
    $(author_fio).attr('id', fio_id);

    var role_id = 'author_role_create-' + id;
    var author_role = $('#author_role-');
    $(author_role).attr('name', 'Authors['+id+'][role]');
    $(author_role).attr('id', role_id);

    var experience_id = 'author_experience_create-' + id;
    var author_experience = $('#author_experience-');
    $(author_experience).attr('name', 'Authors['+id+'][experience]');
    $(author_experience).attr('id', experience_id);

    var buttonRemoveId = 'remove-author-form-create-' + id;
    var remove_author = $('#remove-author-');
    $(remove_author).addClass('remove_author_for_create');
    $(remove_author).attr('id', buttonRemoveId);

    var form_authors = $('#form_authors');

    $(form_authors).find('.form_authors_inputs').find('.row-author').toggleClass('row-author-').toggleClass('row-author-form-create-' + id);
    var str = $(form_authors).find('.form_authors_inputs').html();
    $(str).find('.row-author').toggleClass('row-author-').toggleClass('row-author-form-create-' + id);
    $(hypothesis_create_modal).find('.item-authors').append(str);

    $(form_authors).find('.form_authors_inputs').find('.row-author').toggleClass('row-author-form-create-' + id).toggleClass('row-author-');
    $(form_authors).find('#author_fio_create-' + id).attr('name', 'Authors[0][fio]');
    $(form_authors).find('#author_role_create-' + id).attr('name', 'Authors[0][role]');
    $(form_authors).find('#author_experience_create-' + id).attr('name', 'Authors[0][experience]');

    $(form_authors).find('#author_fio_create-' + id).attr('id', 'author_fio-');
    $(form_authors).find('#author_role_create-' + id).attr('id', 'author_role-');
    $(form_authors).find('#author_experience_create-' + id).attr('id', 'author_experience-');
    $(form_authors).find('#remove-author-form-create-' + id).removeClass('remove_author_for_create');
    $(form_authors).find('#remove-author-form-create-' + id).attr('id', 'remove-author-');

});



//Добавление формы автора проекта в редактировании
$(body).on('click', '.add_author', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[1];

    var item_authors = $('.item-authors-' + numberId);
    var numberName = $(item_authors).children('.row-author').last();
    numberName = $(numberName).children('.form-group').last();
    numberName = $(numberName).children('div').last();
    numberName = $(numberName).find('textarea');
    numberName = $(numberName).attr('id');
    var lastNumberItem = numberName.toString().slice(-1);
    lastNumberItem = Number.parseInt(lastNumberItem);
    var id = lastNumberItem + 1;

    var fio_id = 'author_fio-' + id;
    var author_fio = $('#author_fio-');
    $(author_fio).attr('name', 'Authors['+id+'][fio]');
    $(author_fio).attr('id', fio_id);

    var role_id = 'author_role-' + id;
    var author_role = $('#author_role-');
    $(author_role).attr('name', 'Authors['+id+'][role]');
    $(author_role).attr('id', role_id);

    var experience_id = 'author_experience-' + id;
    var author_experience = $('#author_experience-');
    $(author_experience).attr('name', 'Authors['+id+'][experience]');
    $(author_experience).attr('id', experience_id);

    var buttonRemoveId = 'remove-author-' + numberId + '_' + id;
    $('#remove-author-').attr('id', buttonRemoveId);

    $(item_authors).find('.row-author').toggleClass('row-author-').toggleClass('row-author-' + numberId + '_' + id);


    var form_authors = $('#form_authors');
    var str = $(form_authors).find('.form_authors_inputs').html();

    $(hypothesis_update_modal).find('.item-authors-' + numberId).append(str);

    $(form_authors).find('#author_fio-' + id).attr('name', 'Authors[0][fio]');
    $(form_authors).find('#author_role-' + id).attr('name', 'Authors[0][role]');
    $(form_authors).find('#author_experience-' + id).attr('name', 'Authors[0][experience]');

    $(form_authors).find('#author_fio-' + id).attr('id', 'author_fio-');
    $(form_authors).find('#author_role-' + id).attr('id', 'author_role-');
    $(form_authors).find('#author_experience-' + id).attr('id', 'author_experience-');
    $(form_authors).find('#remove-author-' + numberId + '_' + id).attr('id', 'remove-author-');
    $(item_authors).find('.row-author').toggleClass('row-author-' + numberId + '_' + id).toggleClass('row-author-');
});



//Удаление формы автора проекта при создании
$(body).on('click', '.remove_author_for_create', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[4];

    $(hypothesis_create_modal).find('.row-author-form-create-' + numberId).remove();
    $('form#project_create_form').trigger('change');
});



//Удаление формы автора проекта в редактировании
$(body).on('click', '.remove-author', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[2];

    if(arrId[3]) {

        var worker_id = arrId[3];
        var url = '/projects/delete-author?id=' + worker_id;

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            error: function(){
                alert('Ошибка');
            }
        });
    }

    $(hypothesis_update_modal).find('.row-author-' + numberId).remove();
});