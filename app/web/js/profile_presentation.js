//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var user_id = window.location.search.split('=')[1];

$(document).ready(function() {

    // Получаем данные по проектам пользователя
    $.ajax({
        url: '/profile/get-data-projects?id='+user_id,
        method: 'POST',
        cache: false,
        success: function(response){

            $.each(response.projects, function(){
                var project_id = this.id;
                //Загружаем данные проектов на страницу
                $.ajax({

                    url: '/profile/get-presentation-project?id='+project_id,
                    method: 'POST',
                    cache: false,
                    success: function(response){

                        $('#result-'+project_id).find('.hereAddDataOfProject').html(response.renderAjax);
                    },
                    error: function(){
                        alert('Ошибка');
                    }
                });
            });
        },
        error: function(){
            alert('Ошибка');
        }
    });
});

// При клике по строке с названием проекта
// Показываем и скрываем данные проекта
$(body).on('click', '.container-one_hypothesis', function () {

    var block_data_project = $(this).parent().find('.hereAddDataOfProject');

    if ($(block_data_project).is(':hidden')){
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#7F9FC5',
            'border-radius' : '12px 12px 0px 0px',
        });
        $(this).find('.informationAboutAction').html('Закрыть презентацию проекта');
    }
    if ($(block_data_project).is(':visible')) {
        $(this).parent().find('.container-one_hypothesis').css({
            'background' : '#707F99',
            'border-radius' : '12px',
        });
        $(this).find('.informationAboutAction').html('Посмотреть презентацию проекта');
    }

    $(block_data_project).toggle('display');
});