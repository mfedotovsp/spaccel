//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var module = (window.location.pathname).split('/')[1];


// Прочтение уведомления
$(body).on('click', '.link-read-duplicate-notification', function (e) {

    var communication_id = $(this).attr('id').split('-')[1],
        url = '/' + module + '/communications/read-duplicate-communication?id=' + communication_id,
        container = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $(container).hide();
            // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
            var blockCountUnreadCommunications = $(body).find('.countUnreadCommunications');
            var newQuantityAfterRead = response.countUnreadCommunications;
            $(blockCountUnreadCommunications).html(newQuantityAfterRead);
            if (newQuantityAfterRead < 1) $(blockCountUnreadCommunications).removeClass('active');
        }
    });

    e.preventDefault();
    return false;
});


// Создание беседы с экспертом
$(body).on('click', '.link-create-conversation', function (e) {

    var url = $(this).attr('href'),
        container = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if (response.success) {
                $(container).html('В сообщениях создана беседа с экспертом.');
            }
        }
    });

    e.preventDefault();
    return false;
});