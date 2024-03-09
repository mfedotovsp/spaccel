//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var basic;
var body = $('body');
var delete_unused_image = true; // Проверка на необходимость удалить неиспользованное загруженное фото

// Скрыть просмотр профиля и показать форму редактирования профиля
$(body).on('click', '#show_form_update_data', function () {
    $('.view_client_form').hide();
    $('.update_client_form').show();
});

// Скрыть форму редактирования и показать просмотр профиля
$(body).on('click', '#show_form_view_data', function () {
    $('.update_client_form').hide();
    $('.view_client_form').show();
});

// Сохранение формы редактирования профиля
$(body).on('beforeSubmit', '#update_data_profile', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            // Скрыть форму редактирования профиля и показать просмотр профиля
            $('#show_form_view_data').trigger('click');
            // Обновляем контент страницы
            $('.data_client_content').html(response.renderAjax);
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

    var url = '/client/client-profile/load-avatar-image';

    basic.croppie('result','base64').then(function(html) {
        $.ajax({

            url: url,
            method: 'POST',
            data: 'imageMin=' + html + "&imageMax=" + $('input[name="AvatarCompanyForm[imageMax]"]').val(),
            cache: false,
            success: function (response) {
                if(response.success) {
                    delete_unused_image = false;
                    // Обновляем контент страницы
                    $('.data_client_content').html(response.renderAjax);
                    // Закрываем модальное окно
                    $('.profile-modal-photo').modal('hide');
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
    var url = '/client/client-profile/load-avatar-image';

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response){

            if (response.success) {

                $('input[name="AvatarCompanyForm[imageMax]"]').val(response.imageMax);
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
            url: '/client/client-profile/delete-unused-image',
            method: 'POST',
            data: 'imageMax=' + $('input[name="AvatarCompanyForm[imageMax]"]').val(),
            error: function() {alert('Ошибка');}
        });
    }
    $('img.profile_photo_i').croppie('destroy');
});


// Редактирование аватарки
$(body).on('click', '.update_image', function (e) {

    var url = '/client/client-profile/get-data-avatar';

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
                $('.data_client_content').html(response.renderAjax);
            }
        }, error: function(){
            alert('Ошибка');
        }
    });
    e.preventDefault();
    return false;
});