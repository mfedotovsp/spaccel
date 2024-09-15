//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));
var body = $('body');
var id_page = window.location.search.split('=')[1];
var exist_desc = window.location.search.split('=')[2] ? window.location.search.split('=')[2] : false;
var selectedSourceOptions = $('.select-confirm-source').val();


//Добавление файлов
$(body).on('change', 'input[type=file]',function() {

    for (var i = 0; i < this.files.length; i++) {
        console.log(this.files[i].name);
    }

    //Количество добавленных файлов
    var add_count = this.files.length;

    if(add_count > 5) {
        //Сделать кнопку отправки формы не активной
        $(body).find('#save_create_form').attr('disabled', true);
        $(body).find('.error_files_count').show();
    }else {
        //Сделать кнопку отправки формы активной
        $(body).find('#save_create_form').attr('disabled', false);
        $(body).find('.error_files_count').hide();
    }
});


//Форма создания модели подтверждения сегмента
$('#new_confirm_segment').on('beforeSubmit', function(e){

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
                if (!exist_desc) {
                    window.location.href = '/confirm-segment/add-questions?id=' + response.id;
                } else {
                    $.ajax({
                        url: '/confirm-segment/exist-confirm?id=' + response.id,
                        method: 'POST',
                    });
                }
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


//Если задано, что count_respond < count_positive, то count_respond = count_positive
$("input#confirm_count_respond").change(function () {
    var value1 = $("input#confirm_count_positive").val();
    var value2 = $(this).val();
    var valueMax = 100;
    var valueMin = 1;

    if (parseInt(value2) < parseInt(value1)){
        value2 = value1;
        $(this).val(value2);
    }
    if (parseInt(value2) > parseInt(valueMax)){
        value2 = valueMax;
        $(this).val(value2);
    }
    if (parseInt(value2) < parseInt(valueMin)){
        value2 = valueMin;
        $(this).val(value2);
    }
});

//Если задано, что count_positive > count_respond, то count_positive = count_respond
$("input#confirm_count_positive").change(function () {
    var value1 = $(this).val();
    var value2 = $("input#confirm_count_respond").val();
    var valueMax = 100;
    var valueMin = 1;

    if (parseInt(value1) > parseInt(value2)){
        value1 = value2;
        $(this).val(value1);
    }
    if (parseInt(value1) > parseInt(valueMax)){
        value1 = valueMax;
        $(this).val(value1);
    }
    if (parseInt(value1) < parseInt(valueMin)){
        value1 = valueMin;
        $(this).val(value1);
    }
});

// Показать инструкцию для стадии разработки
$(body).on('click', '.open_modal_instruction_page', function (e) {

    var url = $(this).attr('href');
    var modal = $('.modal_instruction_page');
    $(body).append($(modal).first());

    $.ajax({
        url: url,
        method: 'POST',
        cache: false,
        success: function(response){

            if ($(modal).find('.modal-header').find('.modal-header-text-append').length === 0) {
                $(modal).find('.modal-header').append('<div class="modal-header-text-append">Этап 2. Подтверждение гипотез целевых сегментов</div>');
            }
            $(modal).find('.modal-body').html(response);
            $(modal).modal('show');
        },
        error: function(){
            alert('Ошибка');
        }
    });

    e.preventDefault();
    return false;
});


//Отслеживаем изменения в форме создания подтверждения и записываем их в кэш
$(body).on('change', 'form#new_confirm_segment', function(){

    var url = '/confirm-segment/save-cache-creation-form?id=' + id_page + '&existDesc=' + exist_desc;
    var data = $(this).serialize();
    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        cache: false,
        error: function(){
            alert('Ошибка');
        }
    });
});


//Показываем модальное окно - запрет перехода на следующий шаг
var modal_next_step_error = $('#next_step_error');

$(body).on('click', '.show_modal_next_step_error', function (e) {

    $(body).append($(modal_next_step_error).first());
    $(modal_next_step_error).modal('show');

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


//Добавление формы проблемы при создании
$(body).on('click', '.add_problem_variant_form', function(){

    var numberName = $('.item-variants').children('.row-variant').last();
    numberName = $(numberName).children('.form-group').last();
    numberName = $(numberName).children('div').last();
    numberName = $(numberName).find('textarea');
    numberName = $(numberName).attr('id');
    var lastNumberItem = numberName.toString().slice(-1);
    lastNumberItem = Number.parseInt(lastNumberItem);
    var id = lastNumberItem + 1;

    var problemId = 'problemVariants-' + id;
    var problemDesc = $('#problemVariants-');
    $(problemDesc).attr('name', 'FormCreateConfirmDescription[problemVariants]['+id+'][description]');
    $(problemDesc).attr('id', problemId);

    var buttonRemoveId = 'remove-problem-variant-form-' + id;
    var remove_problem_variant = $('#remove-problem-variant-');
    $(remove_problem_variant).addClass('remove_problem_variant_for_create');
    $(remove_problem_variant).attr('id', buttonRemoveId);

    var form_problem_variants = $('#form_problem_variants');

    $(form_problem_variants).find('.form_problem_variants_inputs').find('.row-variant').toggleClass('row-variant-').toggleClass('row-variant-form-create-' + id);
    var str = $(form_problem_variants).find('.form_problem_variants_inputs').html();
    $(str).find('.row-variant').toggleClass('row-variant-').toggleClass('row-variant-form-create-' + id);
    $(body).find('.item-variants').append(str);

    $(form_problem_variants).find('.form_problem_variants_inputs').find('.row-variant').toggleClass('row-variant-form-create-' + id).toggleClass('row-variant-');
    $(form_problem_variants).find('#problemVariants-' + id).attr('name', 'ProblemVariants[0][description]');

    $(form_problem_variants).find('#problemVariants-' + id).attr('id', 'problemVariants-');
    $(form_problem_variants).find('#remove-problem-variant-form-' + id).removeClass('remove_problem_variant_for_create');
    $(form_problem_variants).find('#remove-problem-variant-form-' + id).attr('id', 'remove-problem-variant-');

});


//Удаление формы проблемы при создании
$(body).on('click', '.remove_problem_variant_for_create', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[4];

    $(body).find('.row-variant-form-create-' + numberId).remove();
    $('form#new_confirm_segment').trigger('change');
});
