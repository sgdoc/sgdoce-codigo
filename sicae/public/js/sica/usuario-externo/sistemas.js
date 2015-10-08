var Sistemas = {

    base: $('base').attr('data-url').replace(/\s*$/g, ''),

    verifica : function(id, callback) {
        $.ajax({
            url : Sistemas.base + '/principal/usuario-perfil/user-unit/sqSistema/' + id,
            dataType : 'jsonp',
            contentType: "application/json"
        }).fail(function (){
            window.location.reload();
        }).always(function () {
            if(typeof callback === 'function'){
                callback.apply();
            }
            $('[rel=popover]').popover('hide');
        });
    },

    populateUsersList : function (data,system)
    {
        var config = {
            backdrop: 'static',
            keyboard: false
        }

        $("#sqPerfilUsuarioExterno option").remove();
        $("#sqPerfilUsuarioExterno")
        .append($("<option></option>").text('Selecione uma opção').val(''))
        .removeAttr('disabled');

        $.each(data, function(index, item) {
            $("#sqPerfilUsuarioExterno").append($("<option></option>").text(item.noPerfil).val(item.sqPerfil));
        });

        $("#systemId").val(system);
        $('#modal-usuario-perfil').modal(config);
    },

    redirectSystem : function(id)
    {
        $.ajax({
            url : Sistemas.base + '/principal/sistema/user-system-menu/sqSistema/' + id,
            dataType : 'jsonp',
            contentType: "application/json"
        });
    },

    setPerfil : function(){
        if (!$(this).valid()) {
            return false;
        }

        $('#modal-usuario-perfil').modal('hide');
        $.ajax({
            url : Sistemas.base + $(this).attr('action'),
            dataType : 'jsonp',
            contentType: "application/json",
            data : {
                sqPerfil: $('#sqPerfilUsuarioExterno').val(),
                systemId: $('#systemId').val()
            }
        });

        return false;
    }
};
$(document).ready(function(){
    $('.modal-header > .close, .modal-footer > a.btn').click(function(){
        $(".campos-obrigatorios > .close").click();

        $("form[name=form-user-profile]").validate().resetForm();
        $("form[name=form-user-profile]").validate().elements().each(function(elment){
            $(this).parents('.error').removeClass('error');
            $(this).val('');
        });
    });
    $('#form-user-profile').submit(Sistemas.setPerfil);
});
