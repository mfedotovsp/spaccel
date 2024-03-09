//Установка Simple ScrollBar для блока выбора беседы
const simpleBarConversations = new SimpleBar(document.getElementById('conversation-list-menu'));
//Установка Simple ScrollBar для блока сообщений
const simpleBarDataChatAdmin = new SimpleBar(document.getElementById('data-chat'));
// Получаем id беседы пользователя с админом
var conversation_id = window.location.search.split('=')[1];


var body = $('body');
var id_page = window.location.search.split('=')[1];


// Прокрутка во время работы прелоадера
window.addEventListener('DOMContentLoaded', function() {
    // Прокрутка до блока активной беседы
    var linkAllConversation = $('.containerForAllConversations').find('#conversation-'+id_page);
    simpleBarConversations.getScrollElement().scrollTop = $(linkAllConversation).offset().top - 211;
    // Прокрутка блока сообщений
    var unreadmessage = $(body).find('.addressee-admin.unreadmessage:first');
    if ($(unreadmessage).length) // Первое непрочитанное сообщение для пользователя
        simpleBarDataChatAdmin.getScrollElement().scrollTop = $(unreadmessage).offset().top - $(unreadmessage).height() - $('.data-chat').height();
    else
        simpleBarDataChatAdmin.getScrollElement().scrollTop = simpleBarDataChatAdmin.getScrollElement().scrollHeight;
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


//Открытие и закрытие списка поиска пользователей
$(body).on('click', '#search_conversation', function(e){

    if ($(this).css('border-bottom-width') === '1px') {
        $(this).css({
            'border-bottom-width': '0',
            'border-radius': '12px 12px 0 0',
            'box-shadow': 'inset rgba(0,0,0,.6) 0 1px 3px',
        });
    } else {
        $(this).css({
            'border-bottom-width': '1px',
            'border-radius': '12px',
            'box-shadow': 'inset rgba(0,0,0,.6) 0 -1px 3px',
        });
    }

    // Скрываем и показываем блок с результатом поиска
    $('.conversations_query').toggle('display');
    // Если поле поиска ещё пусто, то выводим всех пользователей поиска
    if ($(this).val() === '') {
        $(this).val(' '); $('form#search_user_conversation').trigger('input'); $(this).val('');
    }

    e.preventDefault();
    return false;
});


// Отслеживаем клик вне поля поиска
$(document).mouseup(function (e){ // событие клика по веб-документу

    var search = $('#search_conversation'); // поле поиска
    var conversations_query = $('.conversations_query'); // блок вывода поиска

    //если клик был не полю поиска и не по его дочерним элементам и не по блоку результата поиска
    if (!search.is(e.target) && search.has(e.target).length === 0 && !conversations_query.is(e.target) && conversations_query.has(e.target).length === 0) {

        $(search).css({'border-width': '1px', 'border-radius': '12px', 'box-shadow': 'inset rgba(0,0,0,.6) 0 -1px 3px'}); // возвращаем стили для поля ввода
        if ($(conversations_query).css('display') === 'block') $(conversations_query).toggle('display'); // скрываем блок вывода поиска
    }
});


// Отслеживаем ввод в строку поиск и выводим найденные беседы
$(body).on('input', 'form#search_user_conversation', function(e) {

    var conversations_query = $('.conversations_query'); // блок вывода поиска
    var input = $('input#search_conversation');
    if ($(conversations_query).css('display') === 'none') {
        $(conversations_query).toggle('display'); // показываем блок вывода поиска
        $(input).css({
            'border-bottom-width': '0',
            'border-radius': '12px 12px 0 0',
            'box-shadow': 'inset rgba(0,0,0,.6) 0 1px 3px',
        });
    }

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){

            // Выводим результаты поиска
            $('.conversations_query').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


//Запрет на отправку формы поиска
$(body).keydown('form#search_user_conversation', function(e) {
    if (e.keyCode === 13) e.preventDefault();
});


// Переход на страницу диалога через выбор в поиске
$(body).on('click', '.conversation-link', function () {
    var id = $(this).attr('id').split('-')[1];
    if (($(this).attr('id').split('-')[0] === 'conversation')) {
        window.location.href = '/message/view?id='+id;
    }
    else if (($(this).attr('id').split('-')[0] === 'expertConversation')) {
        window.location.href = '/expert/message/view?id='+id;
    }
});


// Переход на страницу диалога через выбор в списке
$(body).on('click', '.container-user_messages', function () {

    var id = $(this).attr('id').split('-')[1];

    if ($(this).attr('id').split('-')[0] === 'adminMainConversation') {
        window.location.href = '/client/message/view?id='+id;
    }
    else if ($(this).attr('id').split('-')[0] === 'conversationTechnicalSupport') {
        window.location.href = '/client/message/technical-support?id='+id;
    }
    else if (($(this).attr('id').split('-')[0] === 'conversation')) {
        window.location.href = '/message/view?id='+id;
    }
    else if (($(this).attr('id').split('-')[0] === 'expertConversation')) {
        window.location.href = '/expert/message/view?id='+id;
    }
});


// Открытие и закрытие списка пользователей на малых экранах
$(body).on('click', '.button_open_close_list_users', function () {

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
    var scrollHeight = simpleBarDataChatAdmin.getScrollElement().scrollHeight; // Высота скролла
    if (scrollHeight <= heightScreen - 290) {

        var chat = $(body).find('.data-chat');
        if(timeoutReadMessage) clearTimeout(timeoutReadMessage);
        timeoutReadMessage = setTimeout(function() { //чтобы не искать одно и то же несколько раз

            $(chat).find('.addressee-admin.unreadmessage').each(function (index, item) {

                var message_id = $(item).attr('id').split('-')[1];
                var url = '/message/read-message-admin?id=' + message_id;

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
    simpleBarDataChatAdmin.getScrollElement().addEventListener('scroll', function () {

        var chat = $(body).find('.data-chat');
        if(timeoutReadMessage) clearTimeout(timeoutReadMessage);
        timeoutReadMessage = setTimeout(function() { //чтобы не искать одно и то же несколько раз

            $(chat).find('.addressee-admin.unreadmessage').each(function (index, item) {

                var scrollTop = simpleBarDataChatAdmin.getScrollElement().scrollTop,
                    scrollHeight = simpleBarDataChatAdmin.getScrollElement().scrollHeight,
                    posTop = $(item).offset().top;

                if (posTop + ($(item).height() / 2) <= $(chat).height() || scrollTop + $(item).height() > scrollHeight - $(chat).height()) {

                    var message_id = $(item).attr('id').split('-')[1];
                    var url = '/message/read-message-admin?id=' + message_id;

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
    var scrollHeight = simpleBarDataChatAdmin.getScrollElement().scrollHeight; // Высота скролла
    var chat = $(body).find('.data-chat');
    if (scrollHeight <= heightScreen - 290) {

        if(timeoutReadMessage) clearTimeout(timeoutReadMessage);
        timeoutReadMessage = setTimeout(function() { //чтобы не искать одно и то же несколько раз

            $(chat).find('.addressee-admin.unreadmessage').each(function (index, item) {

                var message_id = $(item).attr('id').split('-')[1];
                var url = '/message/read-message-admin?id=' + message_id;

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

    // Обновляем беседы админа
    $.ajax({
        url: '/message/get-list-update-conversations?id=' + conversation_id + '&pathname=view',
        method: 'POST',
        cache: false,
        success: function(response){

            var conversation_list_menu = $('#conversation-list-menu');
            var conversation_id = $(conversation_list_menu).find('.active-message').attr('id');
            conversation_id = '#' + conversation_id;

            $(conversation_list_menu).find(response.blockConversationAdminMain).html(response.conversationAdminMainForAdminAjax);
            $(conversation_list_menu).find(response.blockConversationDevelopment).html(response.conversationDevelopmentForAdminAjax);
            $(conversation_list_menu).find('.containerForAllConversations').html(response.conversationsUserForAdminAjax);
            if (!$(conversation_list_menu).find(conversation_id).hasClass('active-message')) $(conversation_list_menu).find(conversation_id).addClass('active-message');

            $(conversation_list_menu).find('.containerForExpertConversations').html(response.conversationsExpertForAdminAjax);
        }
    });


    // Проверяем прочитал ли пользователь сообщения
    $(chat).find('.addressee-user.unreadmessage').each(function (index, item) {

        var message_id = $(item).attr('id').split('-')[1];
        var url = '/message/checking-unread-message-admin?id=' + message_id;

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