// Открыть модальное окно с выбором экспертизы по типу деятельности
// для выполнения экспертизы экспертом
// и просмотром результатов экспертизы для других ролей
$(body).on('click', '.link-get-list-expertise', function (e) {

    $.ajax({
        url: $(this).attr('href'),
        method: 'POST',
        cache: false,
        success: function(response){

            $('#showListExpertise').find('.modal-header > a > span.text-link').html(response.headerContent);
            $('#showListExpertise').find('.modal-body').html(response.renderList);
            $('#showListExpertise').modal('show');
        }
    });

    e.preventDefault();
    return false;
});


// Получить форму экспертизы после выбора типа деятельности экспертом
$(body).on('click', '.link-get-form-expertise', function (e) {

    $.ajax({
        url: $(this).attr('href'),
        method: 'POST',
        cache: false,
        success: function(response){

            $('#showListExpertise').find('.modal-body').html(response.renderAjax);
        }
    });

    e.preventDefault();
    return false;
});


// Отслеживаем клик выбора ответа в форме экспертизы для гипотез
$(body).on('change', '.checkbox-expertise', function (e) {

    // Флаг на установку и снятие
    // 'checked' с текущего элемента
    var checked;

    if($(this).prop('checked')) {
        checked = true;
    } else {
        checked = false;
    }

    // Убираем 'checked' у всех элементов
    $('input.checkbox-expertise:checked').prop('checked', false);

    if(checked) {
        // Устанавливаем 'checked' текущему элементу
        $(this).prop('checked', 'checked');
    }

    e.preventDefault();
    return false;
});


// Отслеживаем клик выбора ответа в форме экспертизы для подтверждения гипотез
$(body).on('change', '.checkbox-expertise-many-answer', function (e) {

    // Флаг на установку и снятие
    // 'checked' с текущего элемента
    var checked;

    if($(this).prop('checked')) {
        checked = true;
    } else {
        checked = false;
    }

    // Убираем 'checked' у всех элементов данного вопроса
    $(this).parents('.parent-checkbox-expertise-many-answer').find('input.checkbox-expertise-many-answer:checked').prop('checked', false);

    if(checked) {
        // Устанавливаем 'checked' текущему элементу
        $(this).prop('checked', 'checked');
    }

    e.preventDefault();
    return false;
});


// Сохранение экспертизы
$(body).on('click', '.submit-expertise', function (e) {

    var submitId = $(this).attr('id');
    var form = $(this).parents('form');
    var url = $(form).attr('action');
    var data = $(form).serialize();
    if (submitId === 'completed_expertise') {
        url = url + '&completed=true';
    }

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        success: function(response){

            $('#showListExpertise').find('.modal-body').html(response.renderList);
        }
    });

    e.preventDefault();
    return false;
});