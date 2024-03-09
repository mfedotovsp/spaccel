//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));
var body = $('body');


//Поиск исполнителей
$(body).on('beforeSubmit', '#searchContractorsForm', function(e){

    var data = $(this).serialize();
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        cache: false,
        success: function(response){
            if (response.success) {
                if ($('.headers-contractor-ajax-list').css('display') === 'none' && $(window).width() > '990') {
                    $('.headers-contractor-ajax-list').toggle('display');
                }
                $('.block_all_contractors').html(response.renderAjax);
            }
        }
    });

    e.preventDefault();
    return false;
});


// Показать информацию о исполнителе
$(body).on('click', '.openContractorInfo', function (e) {

    if ($(window).width() > '990') {
        var parent = $(this).parents('.column-user-fio');
        var id_contractor = $(parent).attr('id').split('linkContractorInfo-')[1];
        $('.blockContractorInfo.containerContractorInfo-' + id_contractor).toggle('display');
        var preParent = parent.parents('.container-one_user')
    } else {
        var parent = $(this).parents('.action-buttons');
        var id_contractor = $(parent).attr('id').split('linkContractorInfo-')[1];
        $('.blockContractorInfo.containerContractorInfo-' + id_contractor).toggle('display');
        var preParent = parent.parents('.container-one_user_mobile')
    }

    if (preParent.hasClass('active')) {
        preParent.removeClass('active')
    } else {
        preParent.addClass('active')
    }

    e.preventDefault();
    return false;
});


// Отправка коммуникации
$(body).on('click', '.send-communication', function (e) {

    var url = $(this).attr('href');
    if ($(window).width() > '990') {
        var container = $(this).parent();
    } else {
        var container = $('.button-send-communication');
    }

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function (response) {
            if (response.success) {
                if (response.type === 1100) {
                    $(container).html('<div class="text-success">Запрос сделан</div>');
                }
            }
        }
    });

    e.preventDefault();
    return false;
});