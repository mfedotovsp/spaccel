//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');


//Отслеживаем изменения в форме роли пользователя
$(body).on('change', '#formClientAndRole_clientId', function(){

    var url = $(this).attr('action');
    var data = $(this).serialize();
    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            if ($(window).width() > 1500 && $(window).width() < 1700) {
                $('.wrap').css('margin-bottom', '0');
            } else {
                $('.wrap').css('margin-bottom', '20px');
            }

            $('.block-form-registration').html('');
            $('.block-form-user-role').html(response.renderAjax);

        }, error: function(){
            alert('Ошибка');
        }
    });
});


//Отслеживаем изменения в форме роли пользователя
$(body).on('change', '#formClientAndRole_role', function(){

    var url = $(this).attr('action');
    var data = $(this).serialize();
    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            if ($(window).width() > 1000 && $(window).width() < 1700) {
                $('.wrap').css('margin-bottom', '0');
            } else {
                $('.wrap').css('margin-bottom', '20px');
            }
            $('.block-form-registration').html(response.renderAjax);

        }, error: function(){
            alert('Ошибка');
        }
    });
});


// Отслеживаем изменения в поле отправки кода для регистрации
$(body).on('input', '#clientCodeInput', function(){
    if ($('#clientCodeInput').val() !== '') {
        $(body).find('#button-formClientCode').attr('disabled', false);
    } else {
        $(body).find('#button-formClientCode').attr('disabled', true);
    }
});


// Отслеживаем отправку в форме отправки кода для регистрации
$(body).on('beforeSubmit', '#formClientCode', function(e){

    var url = $(this).attr('action');
    var data = $(this).serialize();

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            $('#formClientCode').toggle('display');

            if (!response.errorMessage) {
                if ($(window).width() > 1000 && $(window).width() < 1700) {
                    $('.wrap').css('margin-bottom', '0');
                } else {
                    $('.wrap').css('margin-bottom', '20px');
                }
                $('.block-form-registration').html(response.renderAjax);
            } else {
                $('.block-form-registration').html('<div class="text-center">' + response.errorMessage + '</div>');
            }

        }, error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault(e);
    return false;
});


//Отправка формы регистрации пользователя
$(body).on('beforeSubmit', '#form_user_singup', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    var error_user_singup_modal = $('#error_user_singup').find('.modal-body');
    error_user_singup_modal.html('');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            if(response.error_uniq_email) {
                error_user_singup_modal.append('<\h4 style=\"color: #F2F2F2; padding: 0 30px;\"> - почтовый адрес уже зарегистрирован<\/h4>');
            }

            if(response.error_exist_agree) {
                error_user_singup_modal.append('<\h4 style=\"color: #F2F2F2; padding: 0 30px;\"> - необходимо согласие с настоящей Политикой конфиденциальности и условиями обработки персональных данных<\/h4>');
            }

            if(response.error_uniq_email || response.error_exist_agree) {
                $(body).append($('#error_user_singup').first());
                $('#error_user_singup').modal('show');
            }

            var result_singup = $('#result_singup');

            if(response.success_singup){
                $('.result-registration').html('<\h3 style=\"color: #FFFFFF;\" class=\"text-center\">' + response.message + '<\/h3>')
                $('.wrap').css('margin-bottom', '20px');
            }

            if(response.error_singup_send_email){
                $(result_singup).find('.modal-body').find('h4').html('');
                $(result_singup).find('.modal-body').find('h4').html(response.message);
                $(body).append($(result_singup).first());
                $(result_singup).modal('show');
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// Отслеживаем изменения в чекбоксе "Имею опыт работы"
// в форме регистрации исполнителя
$(body).on('change', '#exist_experience_checkbox', function(){
    $('.block-for-experience').toggle('display');
});
