//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var contractor_id = window.location.search.split('=')[1];

$(document).ready(function() {

    // Получаем задания для исполнителя по проектам
    var allHypothesis = $(body).find('.allHypothesis').children('.hypothesis');
    if (allHypothesis.length > 0) {
        $.each(allHypothesis, function(){
            var projectId = $(this).attr('id').split('hypothesis-')[1];
            $.ajax({
                url: '/tasks/get-tasks-by-params?contractorId='+contractor_id+'&projectId='+projectId,
                method: 'POST',
                cache: false,
                success: function(response){
                    $('#hypothesis-' + projectId).find('.hereAddProjectTasks').html(response.renderAjax);
                }
            });
        });
    }
});

// При клике по строке с названием проекта
// Показываем и скрываем коммуникации по проекту
$(body).on('click', '.container-one_hypothesis', function () {
    var block_data_project = $(this).parent().find('.hereAddProjectTasks');

    if ($(block_data_project).is(':hidden')){
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#7F9FC5',
            'border-radius' : '12px 12px 0px 0px',
        });
        $(this).find('.informationAboutAction').html('Закрыть задания по проекту');
    }
    if ($(block_data_project).is(':visible')) {
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#707F99',
            'border-radius' : '12px',
        });
        $(this).find('.informationAboutAction').html('Посмотреть задания по проекту');
    }

    $(block_data_project).toggle('display');
});

// При клике по кнопке "Подробнее"
// Показываем и скрываем историю статусов задачи
$(body).on('click', '.openTaskHistory', function () {
    var taskId = $(this).attr('id').split('-')[1];
    var blockHistory = $('.hereAddProjectTasks').find('.blockTaskHistory-' + taskId);
    if ($(blockHistory).is(':hidden')) {
        $(body).find('.blockTaskHistory').hide();
    }
    $(blockHistory).toggle('display');
});

// Изменение статуса задания в зависимости от выбранного статуса кнопки
$(body).on('click', '.changeStatusSubmit', function (e) {

    var status = $(this).attr('id').split('-')[2];
    var blockForm = $(this).parents('.blockChangeTaskStatusCustomForm');
    var form = $(blockForm).find('form#changeTaskStatusCustomForm');
    var data = $(form).serialize();
    var url = $(form).attr('action') + status;

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            if (response.success) {
                var projectId = response.projectId;
                $.ajax({
                    url: '/tasks/get-tasks-by-params?contractorId='+contractor_id+'&projectId='+projectId,
                    method: 'POST',
                    cache: false,
                    success: function(response){
                        $('#hypothesis-' + projectId).find('.hereAddProjectTasks').html(response.renderAjax);
                    }
                });
            }
        }
    });

    e.preventDefault();
    return false;
});
