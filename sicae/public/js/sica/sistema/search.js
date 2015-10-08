$(document).ready(function() {
    $('button[type=reset]').click(function() {
        $(':input').attr('disabled', false)
                .attr('readonly', false);
        $('#sqSistema').val('');

        $("#form-pesquisa-sistema").validate().resetForm();
        $("#form-pesquisa-sistema").validate().elements().each(function(elment) {
            $(this).parents('.error').removeClass('error');
            $(this).val('');
        });

    });

    Grid.load($('#form-pesquisa-sistema'), $('#table-sistema'));

    $('#grid').removeClass('hidden');

    $('#form-pesquisa-sistema').submit(function() {
        $('#pesquisa-pdf').val($(this).find(':not(#pesquisa-pdf)').serialize());
    });

});
if (typeof Sistema === 'undefined') {
    var Sistema = {};
}

Sistema.edit = function(id) {
    window.location = BASE + "/sistema/edit/id/" + id;
}

Sistema.view = function(id) {
    $.get('/principal/sistema/view/id/' + id, function(data) {
        Message.show('Visualizar', data);
    });
}

$(document).ajaxComplete(function(e, xhr, settings) {
    if (settings.url.indexOf('sistema/list') != -1) {
        var response = $.parseJSON(xhr.responseText) || {};
        if ('iTotalRecords' in response) {
            $('.gerar-pdf').removeClass('hide');
            if (parseInt(response.iTotalRecords) < 1) {
                $('.gerar-pdf').addClass('hide');
            }
        }
    }
});