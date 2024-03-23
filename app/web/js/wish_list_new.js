//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

$(body).on('click', '.delete-wish-list', function (e){

    var url = $(this).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if (response.success) {
                $('.block_all_wish_lists_new').html(response.renderAjax);
            } else {
                console.log(response.messageError);
            }
        }
    });

    e.preventDefault();
    return false;
});

$(body).on('click', '.wish-list-complete', function (e) {

    var url = $(this).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if (response.success) {
                $('.block_all_wish_lists_new').html(response.renderAjax);
            }
        }
    });

    e.preventDefault();
    return false;
});

$(body).on('click', '.one-wish_list_new .hide-show-ready-data', function (){

    var oneWishList = $(this).parent('.one-wish_list_new');
    $(oneWishList).parent('.parent-wish_list_new').find('.one-wish_list_ready-data').toggle('display');
    if ($(oneWishList).css('border-radius') === '12px') {
        $(oneWishList).css({
            'border-radius': '12px 12px 0 0',
            'margin-bottom': '0',
            'background': '#7F9FC5'
        });
    } else {
        $(oneWishList).css({
            'border-radius': '12px',
            'margin-bottom': '5px',
            'background': '#707F99'
        });
    }
});
