var Pesquisa = {
    vars : {
        _urlVisualizar : "artefato/solicitacao/visualizar/id/%d",
    },
    init : function(){
        sessionStorage.clear();
        Pesquisa.grid('#form-pesquisa-demandas', '#table-grid-demandas');
        Pesquisa.events();

    },
    events : function(){
        $("#btnPesquisar").click(function(){
            $("#form-pesquisa-demandas").submit();
        });

        $('#btnClear').on('click',function(){
            $('#dtSolicitacao').val('');
            $('#sqTipoAssuntoSolicitacao,#sqTipoAssuntoSolicitacao_hidden').val('');
            $('#nuArtefato').val('').removeProp('disabled');
            $('#sqTipoArtefatoProcesso').trigger('click');
        });

        $('#sqTipoAssuntoSolicitacao').simpleAutoComplete("/artefato/solicitacao/search-tipo-assunto-solicitacao", {
            extraParamFromInput: 'input[name="sqTipoArtefato"]:checked',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('input[name="sqTipoArtefato"]').on('change',function(){
            if ($(this).val() == 0) {
                $('#nuArtefato').val('').prop('disabled','disabled');
                $('#sqTipoAssuntoSolicitacao,#sqTipoAssuntoSolicitacao_hidden').val('');
            }else{
                $('#nuArtefato').removeProp('disabled');
            }
        });
    },
    acoes : {
        visualizar : function( sqSolicitacao ){
            Pesquisa.initModal(sprintf(Pesquisa.vars._urlVisualizar, sqSolicitacao));
        },
    },
    grid : function(form, tablegrid){
        Grid.load($(form), $(tablegrid));
    },
    initModal: function(_url, container) {
        var modalContainer = container || $("#modal_container");
        modalContainer.empty();
        modalContainer.load(_url, function(responseText, textStatus) {
            if (textStatus === 'success') {
                modalContainer.modal();
            } else {
                Message.showError(responseText);
            }
        });
    }
}