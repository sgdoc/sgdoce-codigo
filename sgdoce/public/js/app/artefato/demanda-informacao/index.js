var DemandaInformacaoMain = {
    Index : {        
        Init: function(){
            //limpa dados das grids sessionStorage
            sessionStorage.clear();
            
            DemandaInformacaoMain.Index.InitGrid();
        },
        
        InitGrid: function(){
            Grid.load($('#received_box-form'), $('#table-grid-di-received'));
            Grid.load($('#generated_box-form'), $('#table-grid-di-generated'));
        },
                
        Aba : {
            setCurrent : function( sqPessoa, _url ){
                $(window.document.location).attr('href', sprintf(_url, sqPessoa));
            },
        }
    },
    Acoes : {
        Resposta : function( sqPrazo ){
            DemandaInformacaoMain.Modal(sprintf(DemandaInformacaoMain.Vars.urlResposta, sqPrazo));
            return;
        },
        Visualizar : function( sqPrazo ){
            DemandaInformacaoMain.Modal(sprintf(DemandaInformacaoMain.Vars.urlVisualizar, sqPrazo));
            return;
        },
        Gerar : function( sqPrazo ) {
            DemandaInformacaoMain.Modal(sprintf(DemandaInformacaoMain.Vars.urlGerar, sqPrazo));
            return;
        }
    },
    Vars : {
        urlResposta : "/artefato/demanda-informacao/resposta/id/%d",
        urlVisualizar : "/artefato/demanda-informacao/visualizar/id/%d",
        urlGerar : "/artefato/demanda-informacao/gerar/idPai/%d",
    },
    Modal : function( url ) {
        var modalContainer = $("#modal_container");
            modalContainer.empty();
            modalContainer.load(url, function (responseText, textStatus) {
                if (textStatus === 'success') {
                    modalContainer.modal();
                } else {
                    Message.showError(responseText);
                }
            });
    }
}