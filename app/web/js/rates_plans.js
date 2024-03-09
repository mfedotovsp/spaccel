//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

// Показать форму создания нового тарифного плана
$(body).on('click', '#showRatesPlanToCreate', function (e) {

    var url = $(this).attr('href');
    var elem = $('.hi-line-page');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            if ($('.block-form-create-rates-plan').length) {
                // Если форма уже открыта, то закрыть её
                $('.block-form-create-rates-plan').remove();
            } else {
                // Вставить сразу после верхней строки страницы
                $(elem).after(response.renderAjax);
            }
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});
