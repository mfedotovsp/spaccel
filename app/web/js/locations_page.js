//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

// Показать и скрыть форму создания локации
$(body).on('click', '#showLocationToCreate', function (e) {
    $('.form_create_location').toggle('display');
    e.preventDefault();
    return false;
});

//Форма создания локации
$(body).on('beforeSubmit', 'form#createLocationForm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            if (response.success) {
                $('.data-locations').html(response.renderAjax);
                $('form#createLocationForm')[0].reset();
                $('#showLocationToCreate').trigger('click');
            } else {
                alert(response.message);
            }
        }
    });

    e.preventDefault();
    return false;
});

// Показать форму редактирования
$(body).on('click', '.update-location', function (e){

    var url = $(this).attr('href');
    var parent = $(this).parents('.data-location');

    if ($('.updateLocationForm').length) {
        $('.updateLocationForm').remove();
    }

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $(parent).append(response.renderAjax);
        }
    });
    e.preventDefault();
    return false;
});

// Отмена редактирования
$(body).on('click', '.cancel-location-update', function (e){
    $('.updateLocationForm').remove();
});

//Форма редактирования локации
$(body).on('beforeSubmit', 'form#updateLocationForm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            if (response.success) {
                $('.data-locations').html(response.renderAjax);
            } else {
                alert(response.message);
            }
        }
    });

    e.preventDefault();
    return false;
});
