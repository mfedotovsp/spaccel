//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');


// Вызов модального окна для назначения менеджера организации
$(body).on('click', '.open_change_manager_modal', function (e) {

    var url = $(this).attr('href');
    var modal = $('#change_manager_modal');
    var container = $(modal).find('.modal-body');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(modal).modal('show');
            $(container).html(response.renderAjax);
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Сохранение назначения менеджера организации
$(body).on('beforeSubmit', '#formChangeManagerToClient', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            if (response){
                $('.client_container_number-' + response.client_id).html(response.renderAjax);
            }
            // Закрытие модального окна
            $('#change_manager_modal').modal('hide');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Вызов модального окна для назначения тарифного плана организации
$(body).on('click', '.open_change_rates_plan_modal', function (e) {

    var url = $(this).attr('href');
    var modal = $('#change_rates_plan_modal');
    var container = $(modal).find('.modal-body');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $(modal).modal('show');
            $(container).html(response.renderAjax);
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Сохранение назначения тарифного плана организации
$(body).on('beforeSubmit', '#formChangeRatesPlanToClient', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            if (response){
                $('.client_container_number-' + response.client_id).html(response.renderAjax);
            }
            // Закрытие модального окна
            $('#change_rates_plan_modal').modal('hide');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Изменение статуса организации (клиента)
$(body).on('click', '.change_status_client', function (e) {

    var data = $(this).serialize();
    var url = $(this).attr('href');

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            if (response){
                $('.client_container_number-' + response.client_id).html(response.renderAjax);
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});