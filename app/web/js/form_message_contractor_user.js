var body = $('body');
var chat = $(body).find('#data-chat');
// Автоматическое обновление блока сообшений
var autoUpdateDataChat = true;


// Передаем событие клика по кнопке сохранить сообщение
$(body).on('click', '.send_message_button', function () {
    $('#submit_send_message').trigger('click');
});


// Сохраняем форму отправки сообщения
$(body).on('beforeSubmit', '#create-message-contractor', function (e) {

    var form = $(this);
    var idLastMessage; // ID последнего сообщения на странице
    var lastMessage = $(chat).find('.message:last');
    if ($(lastMessage).length) { idLastMessage = $(lastMessage).attr('id').split('-')[1]; }
    else { idLastMessage = 0; }
    var url = form.attr('action') + '&idLastMessageOnPage=' + idLastMessage;
    var formData = new FormData(form[0]);

    $.ajax({

        url: url,
        method: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        cache: false,
        success: function(response){

            // Обновляем кол-во непрочитанных сообщений в шапке сайта
            var countUnreadMessages = $(body).find('.countUnreadMessages');
            if (response.countUnreadMessages > 0) {

                if ($(countUnreadMessages).hasClass('active')) {
                    $(countUnreadMessages).html(response.countUnreadMessages);
                } else {
                    $(countUnreadMessages).addClass('active');
                    $(countUnreadMessages).html(response.countUnreadMessages);
                }
            } else {
                if ($(countUnreadMessages).hasClass('active'))
                    $(countUnreadMessages).removeClass('active');
            }

            // Обновляем беседы на странице
            var conversation_list_menu = $('#conversation-list-menu');
            var conversation_id = $(conversation_list_menu).find('.active-message').attr('id');
            conversation_id = '#' + conversation_id;
            if (response.sender === 'user') $(conversation_list_menu).find('.containerContractorConversations').html(response.conversationsContractorForUser);
            else if (response.sender === 'contractor') $(conversation_list_menu).find('.containerForAllUsersConversations').html(response.conversationsUserForContractorAjax);
            if (!$(conversation_list_menu).find(conversation_id).hasClass('active-message')) $(conversation_list_menu).find(conversation_id).addClass('active-message');

            // Обновляем сообщения на странице
            var chat = $(body).find('#data-chat');
            // Если в беседе ранее не было сообщений, то удаляем этот блок с текстом
            if ($(chat).find('.block_not_exist_message').length)
                $(chat).find('.block_not_exist_message').remove();
            // Добавляем новые сообщения в конец чата
            $(chat).find('.simplebar-content').append(response.addNewMessagesAjax);

            // Делаем скролл чата на странице отправителя
            if (response.sender === 'user')
                simpleBarDataChatUser.getScrollElement().scrollTop = simpleBarDataChatUser.getScrollElement().scrollHeight;
            else if (response.sender === 'contractor')
                simpleBarDataChatContractor.getScrollElement().scrollTop = simpleBarDataChatContractor.getScrollElement().scrollHeight;

            // Очищаем форму сообщений
            $('#create-message-contractor')[0].reset();
            $('#input_send_message').html('');
            // Очищаем и скрываем блок для показа загруженных файлов
            var block_attach_files = $('.block_attach_files');
            if ($(block_attach_files).css('display') === 'block') {
                $(block_attach_files).html('');
                $(block_attach_files).css('display', 'none');
            }
            // Корректируем высоту блока сообщений и поля textarea
            var heightScreen = $(body).height(); // Высота экрана
            $(chat).css('height', (heightScreen - 290));
            $('textarea#input_send_message').css('height', '51px').attr('required', true);

        },
        error: function(){
            alert('Ошибка');
        },
        beforeSend: function() {
            autoUpdateDataChat = false;
        },
        complete: function () {
            autoUpdateDataChat = true;
        }
    });

    e.preventDefault();
    return false;
});


// Отслеживаем высоту textarea и изменяем высоту блока сообщений
$(body).on('input', 'textarea#input_send_message', function () {

    var changeHeightTetxtarea = $(this).css('height').split('px')[0] - 64,
        heightScreen = $(body).height(),
        block_attach_files = $('.block_attach_files'),
        heightBlockAttachFiles = 0;

    // Корректируем высоту блока сообщений
    if ($(block_attach_files).css('display') === 'block') heightBlockAttachFiles = $(block_attach_files).css('height').split('px')[0];
    $('.data-chat').css('height', (heightScreen - changeHeightTetxtarea - heightBlockAttachFiles - 290));


    // Сохраняем данные в кэш
    var conversation_id = window.location.search.split('=')[1];
    var url = '/message/save-cache-message-contractor-form?id=' + conversation_id;
    var data = $(this).serialize();
    $.ajax({url: url, data: data, method: 'POST'});

});


// При клике на иконку прикрепления файлов
// имитировать нажатие на настоящую кнопку
$(body).on('click', '.attach_files_button', function () {
    $('#input_message_files').trigger('click');
});


// Отслеживаем изменения в поле загрузки файлов
$(body).on('change', '#input_message_files', function () {

    var block_attach_files = $('.block_attach_files'), // Блок для показа загруженных файлов
        heightScreen = $(body).height(), // Высота экрана
        input_send_message = $('textarea#input_send_message').css('height').split('px')[0] - 64; // Высота поля description

    // Очищаем блок для показа загруженных файлов
    $(block_attach_files).html('');
    //Количество добавленных файлов
    var add_count = this.files.length;

    if (6 > add_count > 0) {
        // Если загружены файлы делаем поле description необязательным
        $('#input_send_message').attr('required', false);
        // Показываем загруженные файлы
        for (var i = 0; i < this.files.length; i++) $(block_attach_files).append('<div>' + this.files[i].name + '</div>');
        $(block_attach_files).css('display', 'block');
    } else if(add_count > 5) {
        // Поле description обязательно
        $('#input_send_message').attr('required', true);
        // Очищаем массив files
        $(this)[0].value = "";
        // Показываем сообщение о превышении лимита на загрузку файлов
        $(block_attach_files).append('<div class="text-danger">Максимальное количество - 5 файлов</div>');
        $(block_attach_files).css('display', 'block');
    }else {
        // Поле description обязательно
        $('#input_send_message').attr('required', true);
    }

    // Корректируем высоту блока сообщений
    $('.data-chat').css('height', (heightScreen - input_send_message - $(block_attach_files).css('height').split('px')[0] - 290));

});


//Постраничная навигация сообщений (вывод предыдущих сообщений)
$(body).on('click', '.pagination-messages .messages-pagination-list li.next a', function(e){

    var conversation_id = window.location.search.split('=')[1];
    var pagination_active_page = $('.pagination-messages .messages-pagination-list li.pagination_active_page a');
    var number_active_page = $(pagination_active_page).html();
    var next_page = Number.parseInt(number_active_page);
    var idFirstMessagePreviosPage = $('.message:first').attr('id').split('-')[1];
    var url = '/message/get-page-message-contractor?id=' + conversation_id + '&page=' + next_page + '&final=' + idFirstMessagePreviosPage;

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            // Удаляем старые элементы пагинации
            var oldPaginationMessages = $('.pagination-messages'); $(oldPaginationMessages).remove();
            var oldBlockForLinkNextPageMasseges = $('.block_for_link_next_page_masseges'); $(oldBlockForLinkNextPageMasseges).remove();
            // Добавляем на страницу предыдущие сообщения
            $('#data-chat').find('.simplebar-content').prepend(response.nextPageMessageAjax);

            if (response.lastPage){
                // На последней странице удаляем ссылку для показа предыдущих сообщений
                var newBlockForLinkNextPageMasseges = $(body).find('.block_for_link_next_page_masseges');
                $(newBlockForLinkNextPageMasseges).remove();
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


// При клике на этот элемент показываем предыдущие сообщения
$(body).on('click', '.button_next_page_masseges', function (e) {

    $('.pagination-messages .messages-pagination-list li.next a').trigger('click');
    e.preventDefault();
    return false;
});


// Обновляем данные на странице
setInterval(function(){

    // Проверяем, есть ли сообщения, у которых id больше,
    // чем у последнего на странице, если есть, то добавить их в конец чата
    var chat = $(body).find('.data-chat');
    var conversation_id = window.location.search.split('=')[1];
    var idLastMessage; // ID последнего сообщения на странице
    var lastMessage = $(chat).find('.message:last');
    if ($(lastMessage).length) idLastMessage = $(lastMessage).attr('id').split('-')[1];
    else idLastMessage = 0;

    var url = '/message/check-new-messages-contractor?id=' + conversation_id + '&idLastMessageOnPage=' + idLastMessage;

    if (autoUpdateDataChat === true) {

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function (response) {

                if (response.checkNewMessages === true) {
                    // Если в беседе ранее не было сообщений, то удаляем этот блок с текстом
                    if ($(chat).find('.block_not_exist_message').length)
                        $(chat).find('.block_not_exist_message').remove();
                    // Добавляем новые сообщения в конец чата
                    $(chat).find('.simplebar-content').append(response.addNewMessagesAjax);
                }
            }
        });
    }


}, 30000);
