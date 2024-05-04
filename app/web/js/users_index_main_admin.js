//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var module = (window.location.pathname).split('/')[1];

// Ссылка на профиль пользователя
$(body).on('click', '.column-user-fio', function () {
    var id = $(this).attr('id').split('-')[1];
    var page = window.location.pathname.split('/')[3];
    if (page === 'index') location.href = '/profile/index?id=' + id;
    else if (page === 'group') location.href = '/profile/index?id=' + id;
    else if (page === 'admins') location.href = '/' + module + '/profile/index?id=' + id;
    else if (page === 'experts') location.href = '/expert/profile/index?id=' + id;
    else if (page === 'managers') location.href = '/admin/profile/index?id=' + id;
    else if (page === 'contractors') location.href = '/contractor/profile/index?id=' + id;
});


// Вызов модального окна для назначения админа пользователю
$(body).on('click', '.open_add_admin_modal', function () {

    var id = $(this).attr('id').split('-')[1];
    var url = '/' + module + '/users/get-modal-add-admin-to-user?id=' + id;
    var modal = $('#add_admin_modal');
    var container = $(modal).find('.modal-body');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            if (response.messageError) {
                $(modal).modal('show');
                $(container).html('<h4 class="text-center">' + response.messageError + '</h4>');
            } else {
                $(modal).modal('show');
                $(container).html(response.renderAjax);
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });
});


// Сохранение админа для пользователя
$(body).on('beforeSubmit', '#formAddAdminToUser', function (e) {

    var id_admin = $('#selectAddAdminToUser').val();
    var data = $(this).serialize();
    var url = $(this).attr('action') + id_admin;

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            // Изменение кнопки с логином админа
            var button = $('#open_add_admin_modal-' + response.user.id);
            $(button).html(response.admin.username);
            if ($(button).hasClass('btn-default')) {
                $(button).toggleClass('btn-success btn-default');
                $(button).css('background', '#52BE7F');
            }
            // Закрытие модального окна
            $('#add_admin_modal').modal('hide');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Вызов модального окна для изменения статуса пользователя
$(body).on('click', '.open_change_status_modal', function () {

    var id = $(this).attr('id').split('-')[1];
    var url = '/' + module + '/users/get-modal-update-status?id=' + id;
    var modal = $('#change_status_modal');
    var container = $(modal).find('.modal-body');

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            if (response.messageError) {
                $(modal).modal('show');
                $(container).html('<h4 class="text-center">' + response.messageError + '</h4>');
            } else {
                $(modal).modal('show');
                $(container).html(response.renderAjax);
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });
});


// Сохранение статуса пользователя
$(body).on('beforeSubmit', '#formStatusUpdate', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            // Изменение кнопки статуса пользователя
            var button = $('#open_change_status_modal-' + response.model.id);
            if (response.model.status === '0') {
                $(button).html('Заблокирован');
                if ($(button).hasClass('btn-default')) {
                    $(button).toggleClass('btn-danger btn-default');
                    $(button).css('background', '#d9534f');
                }
                else if ($(button).hasClass('btn-success')) {
                    $(button).toggleClass('btn-danger btn-success');
                    $(button).css('background', '#d9534f');
                }
            }
            else if (response.model.status === '10') {
                $(button).html('Активирован');
                if ($(button).hasClass('btn-default')) {
                    $(button).toggleClass('btn-success btn-default');
                    $(button).css('background', '#52BE7F');
                }
                else if ($(button).hasClass('btn-danger')) {
                    $(button).toggleClass('btn-success btn-danger');
                    $(button).css('background', '#52BE7F');
                }
            }

            // Закрытие модального окна
            $('#change_status_modal').modal('hide');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Обновляем колонку пользователей
setInterval(function(){

    $(body).find('.column-user-fio').each(function (index, item) {

        var id_user = $(item).attr('id').split('link_user_profile-')[1];

        $.ajax({
            url: '/' + module + '/users/update-data-column-user?id=' + id_user,
            method: 'POST',
            cache: false,
            success: function(response){

                $(item).html(response.renderAjax);
            }
        });

    });

}, 30000);
