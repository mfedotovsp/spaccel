//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');


// При клике по строке с названием проекта
// Показываем коммуникации
$(body).on('click', '.container-one_hypothesis', function () {

    var project_id = $(this).parent().attr('id').split('-')[1];
    var block_data = $(this).parent().find('.hereAddProjectCommunications');

    if ($(block_data).is(':hidden')){

        var url = '/contractor/communications/get-communications?project_id=' + project_id;

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function(response){
                // Добавляем коммуникации по проекту в блок контента
                $(block_data).html(response.renderAjax);
            }
        });

        // Делаем активный блок неактиным
        $(body).find('.container-one_hypothesis.active').trigger('click');
        // Делаем выбранный блок активным
        $(this).addClass('active');
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#7F9FC5',
            'border-radius' : '12px 12px 0px 0px',
        });
    }
    if ($(block_data).is(':visible')) {
        // Делаем выбранный блок неактивным
        $(this).removeClass('active');
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#707F99',
            'border-radius' : '12px',
        });
    }

    // Меняем видимость блока
    $(block_data).toggle('display');
});


// Прочтение уведомления
$(body).on('click', '.link-read-notification', function (e) {

    var communication_id = $(this).attr('id').split('-')[1],
        url = '/contractor/communications/read-communication?id=' + communication_id,
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
            // Меняем кол-во непрочитанных коммуникаций по проекту
            var blockCountUnreadCommunicationsByProject = $('#communications_project-' + response.project_id).find('.countUnreadCommunicationsByProject');
            var countUnreadCommunicationsByProject = response.countUnreadCommunicationsByProject;
            $(blockCountUnreadCommunicationsByProject).html(countUnreadCommunicationsByProject);
            if (countUnreadCommunicationsByProject < 1) $(blockCountUnreadCommunicationsByProject).hide();
        }
    });

    e.preventDefault();
    return false;
});


// Показать форму для ответа на коммуникацию
$(body).on('click', '.link-notification-response', function (e) {

    var communication_id = $(this).attr('id').split('-')[1],
        url = '/contractor/communications/get-form-communication-response?id=' + communication_id,
        container = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            $(container).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Отмена создания ответа на коммуникацию
$(body).on('click', '.cancel-create-response-communication', function (e) {

    var project_id = $(this).attr('id').split('-')[1],
        block_data = $(this).parents('.hereAddProjectCommunications'),
        url = '/contractor/communications/get-communications?project_id=' + project_id;

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            // Обновляем содержимое блока для коммуникаций
            $(block_data).html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Отслеживаем выбор значения в поле "Ответ на вопрос"
// в запросе о готовности присоединиться к работае над проектом
$(body).on('change', '.communication-response-answer', function (e) {

    var positiveAnswer = '543';
    if ($(this).val() != positiveAnswer) {
        $(this).parents('.form-create-response-communication').find('.communication-response-expert-types-block').hide();
    } else {
        $(this).parents('.form-create-response-communication').find('.communication-response-expert-types-block').show();
    }
})


// Сохранение формы ответа на коммуникацию
$(body).on('beforeSubmit', '#formCreateResponseCommunication', function (e) {

    // Если ответ на запрос о готовности провести экспертизу положительный,
    // то проверяем, что поле "типы деятельности" заполнено
    var answer = $(this).find('.communication-response-answer').val(),
        expertTypes = $(this).find('.communication-response-expert-types').val(),
        positiveAnswer = '543';
    if (answer == positiveAnswer && expertTypes == '') {
        e.preventDefault();
        return false;
    }

    var block_data = $(this).parents('.hereAddProjectCommunications'),
        url = $(this).attr('action'),
        data = $(this).serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            // Обновляем содержимое блока для коммуникаций
            $(block_data).html(response.renderAjax);
            // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
            var blockCountUnreadCommunications = $(body).find('.countUnreadCommunications');
            var newQuantityAfterRead = response.countUnreadCommunications;
            $(blockCountUnreadCommunications).html(newQuantityAfterRead);
            if (newQuantityAfterRead < 1) $(blockCountUnreadCommunications).removeClass('active');
            // Меняем кол-во непрочитанных коммуникаций по проекту
            var blockCountUnreadCommunicationsByProject = $('#communications_project-' + response.project_id).find('.countUnreadCommunicationsByProject');
            var countUnreadCommunicationsByProject = response.countUnreadCommunicationsByProject;
            $(blockCountUnreadCommunicationsByProject).html(countUnreadCommunicationsByProject);
            if (countUnreadCommunicationsByProject < 1) $(blockCountUnreadCommunicationsByProject).hide();
        }
    });

    e.preventDefault();
    return false;
})
