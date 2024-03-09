//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var contractor_id = window.location.search.split('=')[1];


$(document).ready(function() {

    // Получаем коммуникаци по проектам
    var allHypothesis = $(body).find('.allHypothesis').children('.hypothesis')
    if (allHypothesis.length > 0) {
        $.each(allHypothesis, function(){
            var projectId = $(this).attr('id').split('hypothesis-')[1]
            $.ajax({
                url: '/contractors/get-communication-by-project?contractorId='+contractor_id+'&projectId='+projectId,
                method: 'POST',
                cache: false,
                success: function(response){
                    if (response.success) {
                        $('#hypothesis-' + projectId).find('.hereAddProjectCommunications').html(response.renderAjax);
                    }
                }
            })
        })
    }
});


// При клике по строке с названием проекта
// Показываем и скрываем коммуникации по проекту
$(body).on('click', '.container-one_hypothesis', function () {
    var block_data_project = $(this).parent().find('.hereAddProjectCommunications');

    if ($(block_data_project).is(':hidden')){
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#7F9FC5',
            'border-radius' : '12px 12px 0px 0px',
        });
        $(this).find('.informationAboutAction').html('Закрыть коммуникации по проекту');
    }
    if ($(block_data_project).is(':visible')) {
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#707F99',
            'border-radius' : '12px',
        });
        $(this).find('.informationAboutAction').html('Посмотреть коммуникации по проекту');
    }

    $(block_data_project).toggle('display');
})


// Назначение исполнителя на проект, отзыв запроса о готовности участвовать в проекте, отказ в назначении на проект, отзыв исполнителя с проекта
$(body).on('click', '.appoints-contractor-project', function (e) {

    var url = $(this).attr('href'),
        textButton = $(this).text(),
        container = $(this).parents('.response-action-to-communication');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {

            if (response.success) {

                if (textButton === 'Назначить' || textButton === 'Отказать') {
                    // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
                    var blockCountUnreadCommunicationsFromContractors = $(body).find('.countUnreadCommunicationsFromContractors');
                    var newQuantityAfterRead = response.countUnreadCommunications;
                    $(blockCountUnreadCommunicationsFromContractors).html(newQuantityAfterRead);
                    if (newQuantityAfterRead < 1) $(blockCountUnreadCommunicationsFromContractors).removeClass('active');
                    // Меняем кол-во непрочитанных коммуникаций по проекту
                    var blockCountUnreadCommunicationsByProject = $('#hypothesis-' + response.project_id).find('.countUnreadCommunicationsByProject');
                    var countUnreadCommunicationsByProject = response.countUnreadCommunicationsByProject;
                    $(blockCountUnreadCommunicationsByProject).html(countUnreadCommunicationsByProject);
                    if (countUnreadCommunicationsByProject < 1) $(blockCountUnreadCommunicationsByProject).hide();
                }

                if (textButton === 'Назначить') {
                    $(container).html('<div class="text-success pl-15">Назначен(-а) на проект</div>')
                }
                if (textButton === 'Отказать') {
                    $(container).html('<div class="text-danger pl-15">Отказано</div>')
                }
                if (textButton === 'Отозвать запрос') {
                    $(container).html('<div class="text-danger pl-15">Запрос отозван</div>')
                }
                if (textButton === 'Отозвать с проекта') {
                    $(container).html('<div class="text-danger pl-15">Отозван с проекта</div>')
                }
            }
        }
    });

    e.preventDefault();
    return false;
});


// Прочтение уведомления
$(body).on('click', '.link-read-notification', function (e) {

    var communication_id = $(this).attr('id').split('-')[1],
        url = '/contractors/read-communication?id=' + communication_id,
        container = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            // Меняем в шапке сайта в иконке количество непрочитанных коммуникаций
            var blockCountUnreadCommunicationsFromContractors = $(body).find('.countUnreadCommunicationsFromContractors');
            var newQuantityAfterRead = response.countUnreadCommunications;
            $(blockCountUnreadCommunicationsFromContractors).html(newQuantityAfterRead);
            if (newQuantityAfterRead < 1) $(blockCountUnreadCommunicationsFromContractors).removeClass('active');
            // Меняем кол-во непрочитанных коммуникаций по проекту
            var blockCountUnreadCommunicationsByProject = $('#hypothesis-' + response.project_id).find('.countUnreadCommunicationsByProject');
            var countUnreadCommunicationsByProject = response.countUnreadCommunicationsByProject;
            $(blockCountUnreadCommunicationsByProject).html(countUnreadCommunicationsByProject);
            if (countUnreadCommunicationsByProject < 1) $(blockCountUnreadCommunicationsByProject).hide();

            $(container).html('<div class="text-success">Прочитано</div>');
        }
    });

    e.preventDefault();
    return false;
});