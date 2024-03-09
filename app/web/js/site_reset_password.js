var body = $('body');

//Вернуться к отправке почты для восстановления пароля
$(body).on('click', '#go_back_password_recovery_for_email', function(){
    $('.style_answer_for_password_recovery').hide();
    $('.style_password_recovery_for_email').show();
});

//Отправка формы для получения письма на почту для сены пароля
$(body).on('beforeSubmit', '#form_send_email', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({

        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            var style_answer_for_password_recovery = $('.style_answer_for_password_recovery');

            if(response.success) {

                $('.style_password_recovery_for_email').hide();
                $(style_answer_for_password_recovery).find('.title').html(response.message.title);
                $(style_answer_for_password_recovery).find('.text').html(response.message.text);
                $(style_answer_for_password_recovery).find('.link_back').hide();
                $(style_answer_for_password_recovery).show();
            }

            if(response.error) {

                $('.style_password_recovery_for_email').hide();
                $(style_answer_for_password_recovery).find('.title').html(response.message.title);
                $(style_answer_for_password_recovery).find('.text').html(response.message.text);
                $(style_answer_for_password_recovery).find('.link_back').show();
                $(style_answer_for_password_recovery).show();
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});