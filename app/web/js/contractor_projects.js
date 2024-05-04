//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');


// При клике по строке с названием проекта
// Показываем коммуникации
$(body).on('click', '.container-one_project', function () {

    var contractorId = window.location.href.split('id=')[1];
    var project_id = $(this).parent().attr('id').split('-')[1];
    var block_data = $(this).parent().find('.hereAddProjectData');

    if ($(block_data).is(':hidden')){

        var url = '/contractor/projects/get-tasks?contractorId=' + contractorId + '&projectId=' + project_id;

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function(response){
                // Добавляем задания по проекту в блок контента
                $(block_data).html(response.renderAjax);
            }
        });

        // Делаем активный блок неактиным
        $(body).find('.container-one_project.active').trigger('click');
        // Делаем выбранный блок активным
        $(this).addClass('active');
        $(this).parent().find('.container-one_project').css({
            'background' : '#7F9FC5',
            'border-radius' : '12px 12px 0px 0px',
        });
    }
    if ($(block_data).is(':visible')) {
        // Делаем выбранный блок неактивным
        $(this).removeClass('active');
        $(this).parent().find('.container-one_project').css({
            'background' : '#707F99',
            'border-radius' : '12px',
        });
    }

    // Меняем видимость блока
    $(block_data).toggle('display');
});
