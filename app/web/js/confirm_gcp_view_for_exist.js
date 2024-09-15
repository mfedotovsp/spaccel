var body = $('body');
var selectedSourceOptions = $('.select-confirm-source').val();


//Добавление файлов при редактировании
$(body).on('change', 'input[type=file]',function(){

    for (var i = 0; i < this.files.length; i++) {
        console.log(this.files[i].name);
    }

    var sourceType = $(this).attr('id').split('-')[1];

    //Количество файлов уже загруженных
    var count_exist_files = $('.block_all_files-' + sourceType).children('div').length;
    //Общее количество файлов
    var countAllFiles = this.files.length + count_exist_files;

    if(countAllFiles > 5) {
        //Сделать кнопку отправки формы не активной
        $('#save_update_form').attr('disabled', true);
        $('.error_files_count').show();
    }else {
        //Сделать кнопку отправки формы активной
        $('#save_update_form').attr('disabled', false);
        $('.error_files_count').hide();
    }

});


//Удаление файла
$(body).on('click', '.delete_file', function(e){

    var deleteFileId = $(this).attr('id');
    deleteFileId = deleteFileId.split('-');
    deleteFileId = deleteFileId[1];
    var url = $(this).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){
            if (response.success) {
                //Удаляем блок с файлом
                $('.one_block_file-' + deleteFileId).remove();
            }
        }
    });

    e.preventDefault();
    return false;
});


// Отслеживаем выбор источников информации для описания подтверждения
$(body).on('change', '.select-confirm-source', function () {
    var action = 'add';
    var diff, values;

    values = Array.from(this.selectedOptions).map(({ value }) => value);
    if (values.length < selectedSourceOptions.length) action = 'delete';
    if (action === 'add') diff = values.filter(element => !selectedSourceOptions.includes(element));
    else diff = selectedSourceOptions.filter(element => !values.includes(element));
    diff.forEach(val => $('.select-confirm-source-option-' + val).toggle('display'));
    selectedSourceOptions = values;
});


//Форма редактирования модели подтверждения проблемы
$('#update_confirm_gcp').on('beforeSubmit', function(e){

    var form = $(this);
    var formData = new FormData(form[0]);
    var url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response){

            if (response.success) {
                $(body).find('.errors').html('');
            } else {
                var errStr = 'Обратите внимание!<br/>';
                response.errors.forEach((val, index) => {
                    errStr += (index+1) + '. ' + val + ' <br/>';
                });
                $(body).find('.errors').html(errStr);
            }
        }
    });

    e.preventDefault();
    return false;
});
