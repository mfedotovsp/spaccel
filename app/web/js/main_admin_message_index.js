//Установка Simple ScrollBar для блока выбора беседы
const simpleBarConversations = new SimpleBar(document.getElementById('conversation-list-menu'));
// Получаем id пользователя
var user_id = window.location.search.split('=')[1];

var module = (window.location.pathname).split('/')[1];


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


var body = $('body');

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
    if (($(this).attr('id').split('-')[0] === 'adminConversation')) {
        window.location.href = '/' + module + '/message/view?id='+id;
    }
    else if (($(this).attr('id').split('-')[0] === 'expertConversation')) {
        window.location.href = '/expert/message/view?id='+id;
    }
    else if (($(this).attr('id').split('-')[0] === 'managerConversation')) {
        window.location.href = '/' + module + '/message/view?id=' + id + '&type=manager';
    }
});


// Переход на страницу диалога через выбор в списке
$(body).on('click', '.container-user_messages', function () {

    var id = $(this).attr('id').split('-')[1];

    if ($(this).attr('id').split('-')[0] === 'conversationTechnicalSupport') {
        window.location.href = '/' + module + '/message/technical-support?id='+id;
    }
    else if ($(this).attr('id').split('-')[0] === 'adminConversation') {
        window.location.href = '/' + module + '/message/view?id='+id;
    }
    else if (($(this).attr('id').split('-')[0] === 'expertConversation')) {
        window.location.href = '/expert/message/view?id='+id;
    }
    else if (($(this).attr('id').split('-')[0] === 'managerConversation')) {
        window.location.href = '/' + module + '/message/view?id=' + id + '&type=manager';
    }
});


// Обновляем данные на странице
setInterval(function(){

    // Обновляем беседы админа
    $.ajax({
        url: '/' + module + '/message/get-list-update-conversations?id=' + user_id + '&pathname=index',
        method: 'POST',
        cache: false,
        success: function(response){

            var conversation_list_menu = $('#conversation-list-menu');
            $(conversation_list_menu).find(response.blockConversationDevelopment).html(response.conversationDevelopmentForAdminMainAjax);
            $(conversation_list_menu).find('.containerForAllConversations').html(response.conversationsAdminForAdminMainAjax);
            $(conversation_list_menu).find('.containerForExpertConversations').html(response.conversationsExpertForAdminMainAjax);
            $(conversation_list_menu).find('.containerForManagerConversations').html(response.conversationsManagerForAdminMainAjax);
        }
    });

}, 30000);