//Установка Simple ScrollBar
const simpleBar = new SimpleBar(document.getElementById('simplebar-shared-container'));

var body = $('body');

var module = (window.location.pathname).split('/')[1];

//Добавление причины запроса в форме создания запроса b2b компании
$(body).on('click', '.add_reason_create_form', function(){

    var containerRequirementReasons = $('.container-requirementReasons');
    var numberName = $('.item-requirementReasons').children('.rowRequirementReasons').last();
    numberName = $(numberName).children('div.field-EXR').last();
    numberName = $(numberName).children('.form-group').last();
    numberName = $(numberName).find('textarea');
    numberName = $(numberName).attr('id');
    var lastNumberItem = numberName.toString().slice(-1);
    lastNumberItem = Number.parseInt(lastNumberItem);
    var id = lastNumberItem + 1;

    var reason_id = '_reasons_reason_create-' + id;
    var reasonsFieldReason = $('#_reasons_reason-');
    $(reasonsFieldReason).attr('name', 'RequirementWishList[reasons]['+id+'][reason]');
    $(reasonsFieldReason).attr('id', reason_id);

    var buttonRemoveId = 'remove-requirementReasons-form-create-' + id;
    var remove_EXR = $('#remove-requirementReasons-');
    $(remove_EXR).addClass('remove_requirementReasons_for_create');
    $(remove_EXR).attr('id', buttonRemoveId);

    var formRequirementReasons = $('#formRequirementReasons');
    $(formRequirementReasons).find('#' + reason_id).html('');

    $(formRequirementReasons).find('.formRequirementReasons_inputs').find('.rowRequirementReasons').toggleClass('rowRequirementReasons-').toggleClass('row-requirementReasons-form-create-' + id);
    var str = $(formRequirementReasons).find('.formRequirementReasons_inputs').html();
    $(str).find('.rowRequirementReasons').toggleClass('rowRequirementReasons-').toggleClass('row-requirementReasons-form-create-' + id);
    $(containerRequirementReasons).find('.item-requirementReasons').append(str);

    $(formRequirementReasons).find('.formRequirementReasons_inputs').find('.rowRequirementReasons').toggleClass('row-requirementReasons-form-create-' + id).toggleClass('rowRequirementReasons-');
    $(formRequirementReasons).find('#_reasons_reason_create-' + id).attr('name', 'RequirementWishList[reasons][0][reason]');

    $(formRequirementReasons).find('#_reasons_reason_create-' + id).attr('id', '_reasons_reason-');

    $(formRequirementReasons).find('#remove-requirementReasons-form-create-' + id).removeClass('remove_requirementReasons_for_create');
    $(formRequirementReasons).find('#remove-requirementReasons-form-create-' + id).attr('id', 'remove-requirementReasons-');

});

//Удаление причины запроса в форме создания запроса b2b компании
$(body).on('click', '.remove_requirementReasons_for_create', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[4];

    var containerRequirementReasons = $('.container-requirementReasons');
    $(containerRequirementReasons).find('.row-requirementReasons-form-create-' + numberId).remove();
    $('form#hypothesisCreateForm').trigger('change');
});

//Добавление причины запроса в форме редактирования запроса b2b компании
$(body).on('click', '.add_requirementReasons', function(){

    var containerRequirementReasons = $('.container-requirementReasons');
    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[1];

    var item_requirementReasons = $('.item-requirementReasons-' + numberId);
    var numberName = $(item_requirementReasons).children('.rowRequirementReasons').last();
    numberName = $(numberName).children('div.field-EXR').last();
    numberName = $(numberName).children('.form-group').last();
    numberName = $(numberName).find('textarea');
    numberName = $(numberName).attr('id');
    var lastNumberItem = numberName.toString().slice(-1);
    lastNumberItem = Number.parseInt(lastNumberItem);
    var id = lastNumberItem + 1;

    var reason_id = '_reasons_reason-' + id;
    var requirementReasons_reason = $('#_reasons_reason-');
    $(requirementReasons_reason).attr('name', 'RequirementWishList[reasons]['+id+'][reason]');
    $(requirementReasons_reason).attr('id', reason_id);
    $(requirementReasons_reason).html('');

    var buttonRemoveId = 'remove-requirementReasons-' + numberId + '_' + id;
    $('#remove-requirementReasons-').attr('id', buttonRemoveId);

    var formRequirementReasons = $('#formRequirementReasons');
    $(formRequirementReasons).find('.rowRequirementReasons').toggleClass('rowRequirementReasons-').toggleClass('row-requirementReasons-' + numberId + '_' + id);
    var str = $(formRequirementReasons).find('.formRequirementReasons_inputs').html();
    $(containerRequirementReasons).find('.item-requirementReasons-' + numberId).append(str);

    $(formRequirementReasons).find('#_reasons_reason-' + id).attr('name', 'RequirementWishList[reasons][0][reason]');
    $(formRequirementReasons).find('#_reasons_reason-' + id).attr('id', '_reasons_reason-');

    $(formRequirementReasons).find('#remove-requirementReasons-' + numberId + '_' + id).attr('id', 'remove-requirementReasons-');
    $(formRequirementReasons).find('.rowRequirementReasons').toggleClass('row-requirementReasons-' + numberId + '_' + id).toggleClass('rowRequirementReasons-');
});

//Удаление причины запроса в форме редактирования запроса b2b компании
$(body).on('click', '#requirementUpdateForm .remove-requirementReasons', function(){

    var clickId = $(this).attr('id');
    var arrId = clickId.split('-');
    var numberId = arrId[2];

    if(arrId[3] || arrId[4]) {

        var requirementReasonId;
        arrId[4] ? requirementReasonId = arrId[4] : requirementReasonId = arrId[3];
        var url = '/' + module + '/wish-list/reason-delete?id=' + requirementReasonId;

        $.ajax({
            url: url,
            method: 'POST',
            cache: false,
            error: function(){
                alert('Ошибка');
            }
        });
    }

    var containerRequirementReasons = $('.container-requirementReasons');
    $(containerRequirementReasons).find('.row-requirementReasons-' + numberId).remove();
});
