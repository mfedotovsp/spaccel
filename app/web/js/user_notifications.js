//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

// Прочтение уведомления
$(body).on('click', '.link-read-notification', function (e) {

    var communication_id = $(this).attr('id').split('-')[1],
        url = '/communications/read-communication?id=' + communication_id,
        container = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $(container).parent().find('.notification_no_read-description').css('font-weight', 400);
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