var identifyingBlock; // Блок для индентификации получателя сообщения
var id_user; // ID пользователя, который смотрит страницу


// Обновляем данные на странице
setInterval(function(){

    var body = $('body');
    id_user = $(body).find('.wrap').attr('id').split('-')[1];
    identifyingBlock = $(body).find('#identifying_recipient_new_message-' + id_user);

    // Извещаем получателя о новом сообщении
    if (id_user !== '0' && $(identifyingBlock).length) {

        $.ajax({
            url: '/message/get-count-unread-messages?id=' + id_user,
            method: 'POST',
            cache: false,
            success: function (response) {

                // Меняем в шапке сайта в иконке количество непрочитанных сообщений
                var countUnreadMessages = $(body).find('.countUnreadMessages');
                var existUnreadBlock = $(body).find('.existUnreadMessagesOrCommunications');
                if (response.countUnreadMessages > 0) {

                    if ($(countUnreadMessages).hasClass('active')) {
                        $(countUnreadMessages).html(response.countUnreadMessages);
                    } else {
                        $(countUnreadMessages).addClass('active');
                        $(countUnreadMessages).html(response.countUnreadMessages);
                    }

                    if (!$(existUnreadBlock).hasClass('active')) {
                        $(existUnreadBlock).addClass('active');
                    }
                } else {
                    if ($(countUnreadMessages).hasClass('active')) {
                        $(countUnreadMessages).removeClass('active');
                    }
                    if (!$(body).find('.countUnreadCommunications').hasClass('active')) {
                        if ($(existUnreadBlock).hasClass('active')) {
                            $(existUnreadBlock).removeClass('active');
                        }
                    } else {
                        if (!$(existUnreadBlock).hasClass('active')) {
                            $(existUnreadBlock).addClass('active');
                        }
                    }
                }
            }
        });
    }

}, 30000);
