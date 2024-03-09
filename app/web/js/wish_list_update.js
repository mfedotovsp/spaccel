//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

$(body).on('click', '.delete-requirement-wish-list', function (e){

    var url = $(this).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if (response.success) {
                $('.blockRequirementsTable').html(response.renderAjax);
            } else {
                console.log(response.messageErrorr)
            }
        }
    });

    e.preventDefault();
    return false;
});
