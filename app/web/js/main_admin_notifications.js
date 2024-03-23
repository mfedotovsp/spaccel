//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var containerAppointsExpertProject;
var module = (window.location.pathname).split('/')[1];


// Получить форму выбора типов эксперта при назначении на проект
$(body).on('click', '.get-form-types-expert', function (e) {

    var url = $(this).attr('href');
    var modal = $('#expert_types_modal');
    var container = $(modal).find('.modal-body');
    containerAppointsExpertProject = $(this).parents('.response-action-to-communication');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            $(modal).modal('show');
            $(container).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Назначение эксперта на проект и сохранение формы выбора типов эксперта
$(body).on('beforeSubmit', '#form_types_expert', function (e) {

    var url = $(this).attr('action');
    var data = $(this).serialize();
    var modal = $('#expert_types_modal');

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function (response) {

            if (response.success) {

                // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
                var blockCountUnreadCommunications = $(body).find('.countUnreadCommunications');
                var newQuantityAfterRead = response.countUnreadCommunications;
                $(blockCountUnreadCommunications).html(newQuantityAfterRead);
                if (newQuantityAfterRead < 1) $(blockCountUnreadCommunications).removeClass('active');

                $(containerAppointsExpertProject).html('<div class="text-success">Назначен(-а) на проект</div>');
                $(modal).modal('hide');
            }
        }
    });

    e.preventDefault();
    return false;
});


// Отказ в назначении на проект
$(body).on('click', '.send-communication', function (e) {

    var url = $(this).attr('href');
    var container = $(this).parents('.response-action-to-communication');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
            var blockCountUnreadCommunications = $(body).find('.countUnreadCommunications');
            var newQuantityAfterRead = response.countUnreadCommunications;
            $(blockCountUnreadCommunications).html(newQuantityAfterRead);
            if (newQuantityAfterRead < 1) $(blockCountUnreadCommunications).removeClass('active');
            if (response.type == 350) $(container).html('<div class="text-danger">Отказано</div>');
        }
    });

    e.preventDefault();
    return false;
});


// Прочтение уведомления
$(body).on('click', '.link-read-notification', function (e) {

    var communication_id = $(this).attr('id').split('-')[1],
        url = '/' + module + '/communications/read-communication?id=' + communication_id,
        container = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $(container).html('<div class="text-success">Прочитано</div>');
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
