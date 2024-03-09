//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var module = (window.location.pathname).split('/')[1];
var page = window.location.search.split('=')[1];
if (typeof(page) === 'undefined') page = 1;

$(body).on('click', '.showDataResultTaskExpertise', function () {
    var projectId = $(this).attr('id').split('-')[1];
    var url = '/' + module + '/expertise/get-result-task?id=' + projectId;
    var dataResultTaskExpertise = $('#dataResultTaskExpertise-' + projectId);

    if ($(this).hasClass('active')) {
        $(this).css('border-bottom', '1px solid #cccccc');
        $(this).removeClass('active');
        $(dataResultTaskExpertise).toggle('display');
    }
    else {
        $(this).css('border-bottom', '0');
        $(this).addClass('active');
        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            success: function(response){
                $(dataResultTaskExpertise).html(response.renderAjax);
                $(dataResultTaskExpertise).toggle('display');
            }
        });
    }
});