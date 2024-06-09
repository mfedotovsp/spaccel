// Установка Simple ScrollBar для блока выбора беседы
const simpleBarConversations = new SimpleBar(document.getElementById('conversation-list-menu'));
// Установка Simple ScrollBar для блока сообщений
const simpleBarDataChatUser = new SimpleBar(document.getElementById('data-chat'));
// Получаем id беседы пользователя с админом
var conversation_id = window.location.search.split('=')[1];


var body = $('body');
var id_page = window.location.search.split('=')[1];

// Прокрутка блока сообщений (во время работы прелоадера)
window.addEventListener('DOMContentLoaded', function() {

    // Прокрутка до блока активной беседы
    var linkAllConversation = $('.containerContractorConversations').find('#contractorConversation-'+id_page);
    simpleBarConversations.getScrollElement().scrollTop = $(linkAllConversation).offset().top - 211;
    // Первое непрочитанное сообщение для пользователя
    var unreadmessage = $(body).find('.addressee-user.unreadmessage:first');
    if ($(unreadmessage).length)
        simpleBarDataChatUser.getScrollElement().scrollTop = $(unreadmessage).offset().top - $(unreadmessage).height() - $('.data-chat').height();
    else
        simpleBarDataChatUser.getScrollElement().scrollTop = simpleBarDataChatUser.getScrollElement().scrollHeight;
});


// Установка прелоадера
$(function () {
    var step = '',
        block_loading = $('#loading'),
        text = $(block_loading).text();

    function changeStep () {
        $(block_loading).text(text + step);
        if (step === '...') step = '';
        else step += '.';
    }

    var interval = setInterval(changeStep, 500);

    $(document).ready(function () {
        setTimeout(function () {
            clearInterval(interval);
            $('#preloader').fadeOut('500','swing');
        }, 3000);
    });
});


// Переход на страницу диалога
$(body).on('click', '.container-user_messages', function () {
    var id = $(this).attr('id').split('-')[1];
    if ($(this).attr('id').split('-')[0] === 'adminConversation') {
        window.location.href = '/message/view?id='+id;
    }
    else if ($(this).attr('id').split('-')[0] === 'conversationTechnicalSupport') {
        window.location.href = '/message/technical-support?id='+id;
    }
    else if ($(this).attr('id').split('-')[0] === 'expertConversation') {
        window.location.href = '/message/expert?id='+id;
    }
    else if ($(this).attr('id').split('-')[0] === 'contractorConversation') {
        window.location.href = '/message/contractor?id='+id;
    }
});


// Открытие и закрытие меню профиля на малых экранах
$(body).on('click', '.link_open_and_close_menu_profile', function () {
    $('.hide_block_menu_profile').toggle('display');
    if ($('.conversation-list-menu').css('position') === 'fixed') $('.button_open_close_list_users').toggle('display');
    if ($(this).html() === 'Открыть меню профиля') $(this).html('Закрыть меню профиля');
    else $(this).html('Открыть меню профиля');
});


// Открытие и закрытие списка пользователей на малых экранах
$(body).on('click', '.button_open_close_list_users', function () {

    $('.link_open_and_close_menu_profile').toggle('display');
    var conversation_list_menu = $('.conversation-list-menu');
    if ($(conversation_list_menu).hasClass('active')) {
        $(this).html('Открыть список пользователей');
        $(this).css('background', '#707F99');
        $(conversation_list_menu).removeClass('active');
    }
    else {
        $(this).html('Закрыть список пользователей');
        $(this).css('background', '#4F4F4F');
        $(conversation_list_menu).addClass('active');
    }
});


$(document).ready(function () {

    // Если высота блока сообщений не имеет скролла, то при открытии
    // страницы непрочитанные сообщения станут прочитанными
    var timeoutReadMessage;
    var heightScreen = $(body).height(); // Высота экрана
    var scrollHeight = simpleBarDataChatUser.getScrollElement().scrollHeight; // Высота скролла
    if (scrollHeight <= heightScreen - 290) {

        var chat = $(body).find('.data-chat');
        if(timeoutReadMessage) clearTimeout(timeoutReadMessage);
        timeoutReadMessage = setTimeout(function() { //чтобы не искать одно и то же несколько раз

            $(chat).find('.addressee-user.unreadmessage').each(function (index, item) {

                var message_id = $(item).attr('id').split('-')[1];
                var url = '/message/read-message-contractor?id=' + message_id;

                $.ajax({
                    url: url,
                    method: 'POST',
                    cache: false,
                    success: function(response){
                        // Меняем стили для прочитанного сообщения
                        if (response.success) $(item).removeClass('unreadmessage');
                        // Меняем в шапке сайта в иконке количество непрочитанных сообщений
                        var countUnreadMessagesAfterRead = $(body).find('.countUnreadMessages');
                        var newQuantityAfterRead = response.countUnreadMessages;
                        $(countUnreadMessagesAfterRead).html(newQuantityAfterRead);
                        if (newQuantityAfterRead < 1) $(countUnreadMessagesAfterRead).removeClass('active');
                        // Меняем в блоке бесед кол-во непрочитанных сообщений для конкретной беседы
                        var blockConversation = $('#conversation-list-menu').find(response.blockConversation);
                        var blockCountUnreadMessagesConversation = $(blockConversation).find('.countUnreadMessagesSender');
                        var countUnreadMessagesForConversation = response.countUnreadMessagesForConversation;
                        $(blockCountUnreadMessagesConversation).html(countUnreadMessagesForConversation);
                        if (countUnreadMessagesForConversation < 1) $(blockCountUnreadMessagesConversation).removeClass('active');
                    }
                });
            });
        },100);
    }

    // Отслеживаем скролл непрочитанных сообщений
    simpleBarDataChatUser.getScrollElement().addEventListener('scroll', function () {

        var chat = $(body).find('.data-chat');
        if(timeoutReadMessage) clearTimeout(timeoutReadMessage);
        timeoutReadMessage = setTimeout(function() { //чтобы не искать одно и то же несколько раз

            $(chat).find('.addressee-user.unreadmessage').each(function (index, item) {

                var scrollTop = simpleBarDataChatUser.getScrollElement().scrollTop,
                    scrollHeight = simpleBarDataChatUser.getScrollElement().scrollHeight,
                    posTop = $(item).offset().top;

                if (posTop + ($(item).height() / 2) <= $(chat).height() || scrollTop + $(item).height() > scrollHeight - $(chat).height()) {

                    var message_id = $(item).attr('id').split('-')[1];
                    var url = '/message/read-message-contractor?id=' + message_id;

                    $.ajax({
                        url: url,
                        method: 'POST',
                        cache: false,
                        success: function(response){
                            // Меняем стили для прочитанного сообщения
                            if (response.success) $(item).removeClass('unreadmessage');
                            // Меняем в шапке сайта в иконке количество непрочитанных сообщений
                            var countUnreadMessagesAfterRead = $(body).find('.countUnreadMessages');
                            var newQuantityAfterRead = response.countUnreadMessages;
                            $(countUnreadMessagesAfterRead).html(newQuantityAfterRead);
                            if (newQuantityAfterRead < 1) $(countUnreadMessagesAfterRead).removeClass('active');
                            // Меняем в блоке бесед кол-во непрочитанных сообщений для конкретной беседы
                            var blockConversation = $('#conversation-list-menu').find(response.blockConversation);
                            var blockCountUnreadMessagesConversation = $(blockConversation).find('.countUnreadMessagesSender');
                            var countUnreadMessagesForConversation = response.countUnreadMessagesForConversation;
                            $(blockCountUnreadMessagesConversation).html(countUnreadMessagesForConversation);
                            if (countUnreadMessagesForConversation < 1) $(blockCountUnreadMessagesConversation).removeClass('active');
                        }
                    });
                }
            });
        },100);
    });

});


// Обновляем данные на странице
setInterval(function(){


    // Если высота блока сообщений не имеет скролла, то при открытии
    // страницы непрочитанные сообщения станут прочитанными
    var timeoutReadMessage;
    var heightScreen = $(body).height(); // Высота экрана
    var scrollHeight = simpleBarDataChatUser.getScrollElement().scrollHeight; // Высота скролла
    var chat = $(body).find('.data-chat');
    if (scrollHeight <= heightScreen - 290) {

        if(timeoutReadMessage) clearTimeout(timeoutReadMessage);
        timeoutReadMessage = setTimeout(function() { //чтобы не искать одно и то же несколько раз

            $(chat).find('.addressee-user.unreadmessage').each(function (index, item) {

                var message_id = $(item).attr('id').split('-')[1];
                var url = '/message/read-message-contractor?id=' + message_id;

                $.ajax({
                    url: url,
                    method: 'POST',
                    cache: false,
                    success: function(response){
                        // Меняем стили для прочитанного сообщения
                        if (response.success) $(item).removeClass('unreadmessage');
                        // Меняем в шапке сайта в иконке количество непрочитанных сообщений
                        var countUnreadMessagesAfterRead = $(body).find('.countUnreadMessages');
                        var newQuantityAfterRead = response.countUnreadMessages;
                        $(countUnreadMessagesAfterRead).html(newQuantityAfterRead);
                        if (newQuantityAfterRead < 1) $(countUnreadMessagesAfterRead).removeClass('active');
                        // Меняем в блоке бесед кол-во непрочитанных сообщений для конкретной беседы
                        var blockConversation = $('#conversation-list-menu').find(response.blockConversation);
                        var blockCountUnreadMessagesConversation = $(blockConversation).find('.countUnreadMessagesSender');
                        var countUnreadMessagesForConversation = response.countUnreadMessagesForConversation;
                        $(blockCountUnreadMessagesConversation).html(countUnreadMessagesForConversation);
                        if (countUnreadMessagesForConversation < 1) $(blockCountUnreadMessagesConversation).removeClass('active');
                    }
                });
            });
        },100);
    }

    // Обновляем беседы пользователя
    $.ajax({
        url: '/message/get-list-update-conversations?id=' + conversation_id + '&pathname=contractor',
        method: 'POST',
        cache: false,
        success: function(response){

            var conversation_list_menu = $('#conversation-list-menu');
            var conversation_id = $(conversation_list_menu).find('.active-message').attr('id');
            conversation_id = '#' + conversation_id;

            $(conversation_list_menu).find(response.blockConversationAdmin).html(response.conversationAdminForUserAjax);
            $(conversation_list_menu).find(response.blockConversationDevelopment).html(response.conversationDevelopmentForUserAjax);
            $(conversation_list_menu).find('.containerContractorConversations').html(response.conversationsContractorForUser);
            if (!$(conversation_list_menu).find(conversation_id).hasClass('active-message')) $(conversation_list_menu).find(conversation_id).addClass('active-message');
        }
    });


    // Проверяем прочитал ли админ сообщения
    $(chat).find('.addressee-contractor.unreadmessage').each(function (index, item) {

        var message_id = $(item).attr('id').split('-')[1];
        var url = '/message/checking-unread-message-contractor?id=' + message_id;

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function(response){

                if (response.checkRead === true) {
                    $(item).removeClass('unreadmessage');
                }
            }
        });
    });


}, 30000);
