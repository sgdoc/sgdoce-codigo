var UsuarioPerfil = {

    comboUnidadePerfil : function(){
        $("#usersList").unbind('change').change(function(){
            if($(this).val() !== '') {
                $('#feijoadaProfile option:first').html('Carregando ...');

                $.ajax({
                    url : Sistemas.base + '/usuario-perfil/perfil-unidade',
                    dataType : 'jsonp',
                    contentType: "application/json",
                    data:{
                        sqUnidadeOrgPessoa : $(this).val(),
                        sqSistema    : $('#systemId').val()
                    }
                });
            } else {
                $("#feijoadaProfile option").remove();
                $("#feijoadaProfile").attr('disabled', 'disabled');
                $("#feijoadaProfile").parent('div').parent('div').hide();
            }
        });

        $("#feijoadaProfile").parent('div').parent('div').hide();
    }

};