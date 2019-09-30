// Data Tables
var cnTable = $(".cn-data-tables").dataTable();
$('.dataTables_paginate > a').wrapInner('<span />');
$('.dataTables_paginate > a span').addClass('btn-paginate');

function getUsers() {
    $.ajax({
        type: 'POST',
        url: 'userAction.php',
        data: 'action_type=view',
        success:function(html){
            $('#userData').html(html);
        }
    });
}

// Mengirim permintaan CRUD ke server
function userAction( type, id ) {
    id = ( typeof id == "undefined" ) ? '' : id;
    var userData = '', frmElement = '';

    if( type == 'add' ) {
        frmElement = $("#modalUserAddEdit");
        userData = frmElement.find('form').serialize() + '&action_type=' + type + '&id=' + id;
    } else if ( type == 'edit' ) {
        frmElement = $("#modalUserAddEdit");
        userData = frmElement.find('form').serialize() + '&action_type=' + type;
    } else {
        frmElement = $(".row");
        userData = 'action_type=' + type + '&id=' + id;
    }
    frmElement.find('.statusMsg').html('');
    $.ajax({
        type: 'POST',
        url: 'userAction.php',
        dataType: 'JSON',
        data: userData,
        beforeSend: function() {
            frmElement.find('form').css("opacity", "0.5");
        },
        success:function( resp ) {
            frmElement.find('.statusMsg').html(resp.msg);
            if(resp.status == 1){
                if(type == 'add'){
                    frmElement.find('form')[0].reset();
                }
                getUsers();
            }
            frmElement.find('form').css("opacity", "");
        }
    });
}

function editUser( id ) {
    $.ajax({
        type: 'POST',
        url: 'userAction.php',
        dataType: 'JSON',
        data: 'action_type=data&id=' + id,
        success:function( data ) {
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
        }
    });
}

$(function() {
    $('#modalUserAddEdit').on('show.bs.modal', function( e ) {
        var type = $(e.relatedTarget).attr('data-type');
        var userFunc = "userAction('add');";
        if(type == 'edit') {
            userFunc = "userAction('edit');";
            var rowId = $(e.relatedTarget).attr('rowID');
            editUser(rowId);
        }
        $('#userSubmit').attr("onclick", userFunc);
    });
    
    $('#modalUserAddEdit').on('hidden.bs.modal', function() {
        $('#userSubmit').attr("onclick", "");
        $(this).find('form')[0].reset();
        $(this).find('.statusMsg').html('');
        setTimeout(function() {
            location.reload();
       }, 0001);
    });
});