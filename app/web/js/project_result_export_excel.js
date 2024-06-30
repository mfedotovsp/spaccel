var body = $('body');

$(document).ready(function () {

    var button = $(body).find('button.dropdown-toggle');
    var link = $(body).find('a.export-full-xlsx');
    var timer_1 = setInterval(function () {
        if ($(button).length) {
            $(button).trigger('click');
            clearInterval(timer_1);

            var timer_2 = setInterval(function () {
                if ($(link).length) {
                    $(link).trigger('click');
                    clearInterval(timer_2);
                }
            }, 1000);
        }
    }, 1000);

});
