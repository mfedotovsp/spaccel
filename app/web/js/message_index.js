// Установка Simple ScrollBar для блока выбора беседы
const simpleBarConversations = new SimpleBar(document.getElementById('conversation-list-menu'));
// Получаем id пользователя
var user_id = window.location.search.split('=')[1];


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
});

// Открытие и закрытие меню профиля на малых экранах
$(body).on('click', '.link_open_and_close_menu_profile', function () {
    $('.hide_block_menu_profile').toggle('display');
    if ($(this).html() === 'Открыть меню профиля') $(this).html('Закрыть меню профиля');
    else $(this).html('Открыть меню профиля');
});


// Обновляем беседы пользователя
setInterval(function(){

    $.ajax({
        url: '/message/get-list-update-conversations?id=' + user_id + '&pathname=index',
        method: 'POST',
        cache: false,
        success: function(response){

            var blockConversationAdmin = $(body).find('#conversation-list-menu').find(response.blockConversationAdmin);
            var blockConversationDevelopment = $(body).find('#conversation-list-menu').find(response.blockConversationDevelopment);
            var blockExpertConversations = $(body).find('.containerExpertConversations');

            $(blockConversationAdmin).html(response.conversationAdminForUserAjax);
            $(blockConversationDevelopment).html(response.conversationDevelopmentForUserAjax);
            $(blockExpertConversations).html(response.conversationsExpertForUser);
        }
    });

}, 30000);