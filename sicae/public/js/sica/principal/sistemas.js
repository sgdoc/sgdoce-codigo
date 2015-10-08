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
        };

        $("#usersList option").remove();
        $("#usersList").append($("<option></option>").text('Selecione uma opção').val(''));

        $.each(data, function(index, item) {
            if(!$('#usersList option[value='+ item.sqPessoa + ']').size()){
                $("#usersList").append(
                        $("<option></option>")
                        .html($("<\div class='word-wrap' >").text(item.sgUnidadeOrg+' - '+item.noPessoa))
                        .val(item.sqPessoa)
                );
            }
        });

        $("#usersList option").css('width', '400px');
        $('#usersList option:nth-child(2n+1)').css('background-color', '#EEEEEE');

        if ($("#usersList option").size() > 11) {
            $("#search-container").show();
        } else {
            $("#search-container").hide();
        }

        $("#systemId").val(system);
        $('#modal-usuario-perfil').modal(config);

        UsuarioPerfil.comboUnidadePerfil();
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
        if (!$("#form-user-profile").valid()) {
            return false;
        }
        $('#modal-usuario-perfil').modal('hide');

        $.ajax({
            url : Sistemas.base + '/usuario-perfil/user-profile',
            dataType : 'jsonp',
            contentType: "application/json",
            data : {
                sqPerfil: $('#feijoadaProfile').val(),
                sqUnidadeOrg: $('#usersList').val(),
                systemId: $('#systemId').val()
            }
        });

        return false;
    },

    drawOptions : function()
    {
        $("#usersList").val("");
        if ($('[name="search-text"]').val()) {
            $("#usersList option").each(function () {

                $(this).hide();
                var pattern = Sistemas.accentsStrip($('[name="search-text"]').val().trim());
                var text    = Sistemas.accentsStrip($(this).text().trim());
                var regExp = new RegExp(pattern, "g");
                if (regExp.test(text)) {
                    $(this).show();
                }

            });
          } else {
            $("#usersList option").show()
          }
    },

    accentsStrip : function(text){
        var normalized = text.toLowerCase();
        var nonASCIIs = {
            'a': '[àáâãäå]',
            'c': 'ç',
            'e': '[èéêë]',
            'i': '[ìíîï]',
            'n': 'ñ',
            'o': '[òóôõö]',
            'u': '[ùúûűü]',
            'y': '[ýÿ]',
            'ae': 'æ',
            'oe': 'œ',
            '...': '…'
        };
        for (i in nonASCIIs) {
            normalized = normalized.replace(new RegExp(nonASCIIs[i], 'img'), i);
        }
        normalized = normalized.replace(/\s+/g,' ');

        return normalized;
    },

    backToSystemLogged : function ()
    {
        $.ajax({
            url : Sistemas.base + '/principal/sistema/system-logged',
            dataType : 'jsonp',
            contentType: "application/json"
        }).fail(function () {
            window.location.reload();
        }).always(function (data) {
            data = jQuery.parseJSON(data.responseText);
            window.location = data.txUrl;
        });
    },
};

$(document).ready(function(){

    $(document).on('click', '#btn-access', Sistemas.setPerfil);

    $('#btnCancelar, .modal-header > .close').click(function(){
        $('#form-user-profile').validate().resetForm();
        $('#form-user-profile').validate().elements().each(function(elment){
            $(this).parents('.error').removeClass('error');
        });

        Sistemas.backToSystemLogged();
    });

    $(document).on('click', '#btn-search', Sistemas.drawOptions);
});