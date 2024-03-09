//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var basic;
var body = $('body');
var user_id = window.location.search.split('=')[1];
var delete_unused_image = true; // Проверка на необходимость удалить неиспользованное загруженное фото

// Скрыть редактирование профиля и показать форму изменения пароля
$(body).on('click', '#show_form_change_password', function () {
    console.log($('.update_user_form').html())
    $('.update_user_form').hide();
    $('.change_password_content').show();
});

// Скрыть все формы и показать просмотр профиля
$(body).on('click', '.show_form_update_profile', function () {
    $('.change_password_content').hide();
    $('.update_user_form').show();
});

// Сохранение формы редактирования профиля
$(body).on('beforeSubmit', '#update_data_profile', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    var modal_error = $('.data_profile_error');
    $(body).append($(modal_error).first());
    $(modal_error).find('.modal-body').html('');

    console.log(data)

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            if (response.error_uniq_email) {
                $(modal_error).modal('show');
                $(modal_error).find('.modal-body').append('<h4> - почтовый адрес уже зарегистрирован;</h4>');
            }
            if (response.error_uniq_username) {
                $(modal_error).modal('show');
                $(modal_error).find('.modal-body').html('<h4> - логин уже зарегистрирован;</h4>');
            }
            if (response.error_match_username) {
                $(modal_error).modal('show');
                $(modal_error).find('.modal-body').append('<h4> - логин может содержать только латинские символы, цифры и специальные символы "@._-", так же не допускается использование пробелов;</h4>');
            }
            if (response.error_send_email) {
                $(modal_error).modal('show');
                $(modal_error).find('.modal-body').append('<h4> - на указанный почтовый адрес не отправляются письма, возможно вы указали некорректный адрес;</h4>');
            }
            if (response.success) {
                // Меняем название ссылки выхода
                $('a[href^=\'/logout\']').find('span').html('Выход (' + response.user.username + ')');
                var navbarHeaderContent = $('body').find('.navbar-header-content').html();
                $('body').find('.navbar-header-content').html(navbarHeaderContent.split(':')[0] + ': ' + response.user.username);
            }

        }, error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});

// Сохранение формы изменения пароля
$(body).on('beforeSubmit', '#form_change_password_user', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');
    var modal_error = $('.data_profile_error');
    $(body).append($(modal_error).first());
    $(modal_error).find('.modal-body').html('');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            if (response.errorCurrentPassword) {
                $(modal_error).modal('show');
                $(modal_error).find('.modal-body').append('<h4> - актуальный пароль введен не верно;</h4>');
            }

            if (response.success) {
                // Скрыть форму изменения пароля и показать просмотр профиля
                $('#show_form_view_data').trigger('click');
                // Очистить форму после сохранения
                $('#form_change_password_user')[0].reset();
            }

        }, error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Открытие окна загрузки новой аватарки
$(body).on('click', '.add_image', function () {
    $('#loadImageAvatar').trigger('click');
    return false;
});


// Сохраниние миниатюры аватарки
$(body).on('click', '#save_avatar_image', function () {

    var url = '/contractor/profile/load-avatar-image?id=' + user_id;

    basic.croppie('result','base64').then(function(html) {
        $.ajax({

            url: url,
            method: 'POST',
            data: 'imageMin=' + html + "&imageMax=" + $('input[name="AvatarForm[imageMax]"]').val(),
            cache: false,
            success: function (response) {
                if(response.success) {
                    delete_unused_image = false;
                    // Обновляем контент страницы
                    $('.data_user_content').html(response.renderAjax);
                    // Закрываем модальное окно
                    $('.profile-modal-photo').modal('hide');
                    // Изменение иконки пользователя в правом верхнем углу
                    var icon_user_avatar = $('img.user_profile_picture');
                    if ($(icon_user_avatar).hasClass('icon_user_avatar_default')) $(icon_user_avatar).toggleClass('icon_user_avatar icon_user_avatar_default');
                    $(icon_user_avatar).attr('src', '/upload/user-'+response.user.id+'/avatar/'+response.user.avatar_image);
                }
            }, error: function(){
                alert('Ошибка');
            }
        });
    });
});


// Отслеживаем выбор фото для аватарки
// Загружаем его на сервер и передаем данные в модальное окно
$(body).on('change', '#loadImageAvatar', function (e) {

    var data = new FormData();
    data.append('file', $(this)[0].files[0]);
    var url = '/contractor/profile/load-avatar-image?id=' + user_id;

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response){

            if (response.success) {

                $('input[name="AvatarForm[imageMax]"]').val(response.imageMax);
                var profile_photo = $('img.profile_photo_i');
                $(profile_photo).attr('src', response.path_max);

                basic = $(profile_photo).croppie({
                    viewport : {
                        width : 320,
                        height : 320,
                        type : 'square'
                    },
                    boundary : {
                        height : 500
                    }
                });

                var modal_profile_foto = $('.profile-modal-photo');
                $(body).append($(modal_profile_foto).first());
                $(modal_profile_foto).modal('show');
                delete_unused_image = true;
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// При закрытии модального окна создания аватарки очистить croppie
$(body).on('hide.bs.modal', '.profile-modal-photo', function () {

    if (delete_unused_image) {

        $.ajax({
            url: '/contractor/profile/delete-unused-image?id=' + user_id,
            method: 'POST',
            data: 'imageMax=' + $('input[name="AvatarForm[imageMax]"]').val(),
            error: function() {alert('Ошибка');}
        });
    }
    $('img.profile_photo_i').croppie('destroy');
});


// Редактирование аватарки
$(body).on('click', '.update_image', function (e) {

    var url = '/contractor/profile/get-data-avatar?id=' + user_id;

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            var profile_photo = $('img.profile_photo_i');
            $(profile_photo).attr('src', response.path_max);

            basic = $(profile_photo).croppie({
                viewport : {
                    width : 320,
                    height : 320,
                    type : 'square'
                },
                boundary : {
                    height : 500
                }
            });

            var modal_profile_foto = $('.profile-modal-photo');
            $(body).append($(modal_profile_foto).first());
            $(modal_profile_foto).modal('show');
            delete_unused_image = false;

        }, error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Удаление аватарки
$(body).on('click', '.delete_image', function (e) {

    $.ajax({
        url: $(this).attr('href'),
        method: 'POST',
        cache: false,
        success: function(response){
            if(response.success) {
                // Обновляем контент страницы
                $('.data_user_content').html(response.renderAjax);
                // Изменение иконки пользователя в правом верхнем углу
                var icon_user_avatar = $('img.user_profile_picture');
                $(icon_user_avatar).toggleClass('icon_user_avatar_default icon_user_avatar');
                $(icon_user_avatar).attr('src', '/images/icons/button_user_menu.png');
            }
        }, error: function(){
            alert('Ошибка');
        }
    });
    e.preventDefault();
    return false;
});


// Обновление информации о последнем визите пользователя
setInterval(function(){

    if ($(body).find('.user_is_online').length > 0) {

        var url = '/contractor/profile/get-user-is-online?id='+user_id;

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function(response){
                if(response.user_online)
                    $(body).find('.user_is_online').html(response.message);
                else if (response.user_logout)
                    $(body).find('.user_is_online').html(response.message);
            }, error: function(){
                alert('Ошибка');
            }
        });
    }
}, 180000);
