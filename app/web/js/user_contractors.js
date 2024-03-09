//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');
var module = (window.location.pathname).split('/')[1];

// Ссылка на профиль пользователя
$(body).on('click', '.column-user-fio', function () {
    var id = $(this).attr('id').split('-')[1];
    location.href = '/contractor/profile/index?id=' + id;
});