//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

// Скрыть/показать подробную информацию по виш-листу
$(body).on('click', '.one-wish_list_ready', function (){
    $(this).parent('.parent-wish_list_ready').find('.one-wish_list_ready-data').toggle('display');
    if ($(this).css('border-radius') === '12px') {
        $(this).css({
            'border-radius': '12px 12px 0 0',
            'margin-bottom': '0',
            'background': '#7F9FC5'
        });
    } else {
        $(this).css({
            'border-radius': '12px',
            'margin-bottom': '5px',
            'background': '#707F99'
        });
    }
});

// Изменить актуальность запроса B2B компаний
$(body).on('click', '.change-requirement-actual', function (e){

    var url = $(this).attr('href');
    var parent = $(this).parent();

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if (response.success) {
                $(parent).find('.isActual').html(response.result);
            }
        }
    });

    e.preventDefault();
    return false;
});

// При клике на кнопку добавить фильтры
$(body).on('click', '#addFiltersForListRequirementsAdmin', function (){

    $('.addFiltersForListRequirements').toggle('display');
    $('.buttonsFiltersForListRequirements').toggle('display');
    $('.requirement-filters').toggle('display');
});

// Фильтрация списка запросов B2B компаний
$(body).on('beforeSubmit', 'form#adminFiltersRequirement', function (e){

    var url = $(this).attr('action');
    var data = $(this).serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            $('.block_all_wish_lists_new').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});

//Пагинация списка запросов B2B компаний
$(body).on('click', '.admin-projects-result-pagin-list li a', function (e){

    var url = $(this).attr('href');
    var data = $('form#adminFiltersRequirement').serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            $('.block_all_wish_lists_new').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});

// Отслеживаем выбор даты начала периода в фильтре даты создания виш-листов
$(body).on('change', '#FormFilterRequirement_startDate', function () {

    var startDate = $(this).val();
    var endDate = $('#FormFilterRequirement_endDate').val();

    if (endDate.toString().length > 0) {
        var startDateArr = startDate.toString().split('.');
        var endDateArr = endDate.toString().split('.');
        var startDateModify = new Date(startDateArr[2] + '-' + startDateArr[1] + '-' + startDateArr[0]);
        var endDateModify = new Date(endDateArr[2] + '-' + endDateArr[1] + '-' + endDateArr[0]);
        if (startDateModify >= endDateModify) {
            $('#FormFilterRequirement_endDate').val(null);
        }
    }
});

// Отслеживаем выбор даты конца периода в фильтре даты создания виш-листов
$(body).on('change', '#FormFilterRequirement_endDate', function () {

    var endDate = $(this).val();
    var startDate = $('#FormFilterRequirement_startDate').val();

    if (startDate.toString().length > 0) {
        var startDateArr = startDate.toString().split('.');
        var endDateArr = endDate.toString().split('.');
        var startDateModify = new Date(startDateArr[2] + '-' + startDateArr[1] + '-' + startDateArr[0]);
        var endDateModify = new Date(endDateArr[2] + '-' + endDateArr[1] + '-' + endDateArr[0]);
        if (startDateModify >= endDateModify) {
            $('#FormFilterRequirement_startDate').val(null);
        }
    }
});
