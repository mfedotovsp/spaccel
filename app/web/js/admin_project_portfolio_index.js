//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));


var body = $('body');
var module = (window.location.pathname).split('/')[1];


var id = (window.location.search).split('?id=')[1];
if (typeof id === 'undefined') {
    id = 'all_projects';
}


$(document).ready(function() {

    var count_projects = $('body').find('#field_count_projects').val();

    //Загружаем сводные таблицы проектов на страницу
    $.ajax({

        url: '/' + module + '/projects/get-result-projects?id=' + id + '&page=1&per_page=' + count_projects,
        method: 'POST',
        cache: false,
        success: function(response){

            $('.allContainersDataOfTableResultProject').html(response.renderAjax);
        }
    });

});


//Указание кол-ва проектов на странице
$(body).change('#field_count_projects', function(){

    var count_projects = $('body').find('#field_count_projects').val();

    $.ajax({

        url: '/' + module + '/projects/get-result-projects?id=' + id + '&page=1&per_page=' + count_projects,
        method: 'POST',
        cache: false,
        success: function(response){

            $('.allContainersDataOfTableResultProject').html(response.renderAjax);
        }
    });
});


//Поиск проектов по назаванию и полному названию проекта
$(body).on('input', '#search_project_name', function(){

    var search = $('body').find('#search_project_name').val();
    var count_projects = $('body').find('#field_count_projects').val();

    $.ajax({

        url: '/' + module + '/projects/get-result-projects?id=' + id + '&page=1&per_page=' + count_projects + '&search=' + search,
        method: 'POST',
        cache: false,
        success: function(response){

            $('.allContainersDataOfTableResultProject').html(response.renderAjax);
        }
    });
});


//Постраничная навигация
$(body).on('click', '.pagination-admin-projects-result .admin-projects-result-pagin-list li a', function(e){

    var url = $(this).attr('href');

    $.ajax({

        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            $('.allContainersDataOfTableResultProject').html(response.renderAjax);
            simpleBar.getScrollElement().scrollBy({top: $('.select_count_projects').offset().top, behavior: 'smooth'});
        }
    });

    e.preventDefault();
    return false;
});
