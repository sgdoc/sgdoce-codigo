PesquisarCaixaArquivo = {
    init: function() {
        console.log('<< CaixaArquivo::init >>');

        PesquisarCaixaArquivo.events().grid();
        sessionStorage.clear();
    },
    events: function() {
        $('#btn_clear').on('click', PesquisarCaixaArquivo.handleClickClear);
        $('#btn_pesquisar').on('click', PesquisarCaixaArquivo.handleFormSubmit);
        $('#btn_filtros').on('click', PesquisarCaixaArquivo.handleClickFiltros);
        $('#sqUnidadeOrg').simpleAutoComplete("arquivo/caixa/search-unidade-org/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
        $('#sqClassificacao').simpleAutoComplete("arquivo/caixa/search-classificacao-caixa/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        return PesquisarCaixaArquivo;
    },
    handleClickClear: function() {
        $('#sqUnidadeOrg_hidden,#sqClassificacao_hidden').val('');
        $('#sqTipoEtiqueta,#sqUnidadeOrg,#nuCaixa,#nuAno,#stFechamento').val('');
        return false;
    },
    handleModalListaArtefatoArquivado: function(sqCaixa) {
        $("#modal_lista_artefato_arquivado").load( '/arquivo/caixa/modal-list-artefato-arquivado/id/'+sqCaixa ).modal();
    },
    handleModalImagemArtefato: function(sqArtefato) {
        $("#modal_imagem_artefato").load( '/artefato/imagem/view/id/'+sqArtefato ).modal();
    },
    handleClickFiltros: function() {
        $('html,body').animate({scrollTop: 0}, 500);
    },
    handleFormSubmit: function() {
        var sqClassificacao = $('#sqClassificacao_hidden').val();
        var sqUnidadeOrg = $('#sqUnidadeOrg_hidden').val();

        if ($('#nuCaixa').val() == '' &&
              $('#nuAno').val() == '' &&
                (sqClassificacao == '' || sqClassificacao == '0') &&
                  (sqUnidadeOrg == '' || sqUnidadeOrg == '0')&&
                    $('#stFechamento').val() == '') {
            var div = '<button class="close" data-dismiss="alert">×</button>' + UI_MSG.MN087;
            $('.campos-obrigatorios').html(div).removeClass('hidden').show();
            $('html,body').animate({scrollTop: 0}, 500);

            return false;
        } else {
            $('html,body').animate({scrollTop: 300}, 500);
        }
    },
    reloadGrid: function () {
        $('#grid_caixa_arquivo').dataTable().fnDraw(false);
        return PesquisarCaixaArquivo;
    },
    grid: function() {
        Grid.load($('#form_caixa_arquivo'), $('#grid_caixa_arquivo'));
        return PesquisarCaixaArquivo;
    },
    edit: function(id) {
        $(window.document.location).attr('href', sprintf('/arquivo/caixa/edit/id/%d', id));
    },
    delete: function(id) {
        Message.showConfirmation({
            'body': sprintf(UI_MSG.MN121, 'a caixa'),
            'yesCallback': function(){
                $(window.document.location).attr('href', sprintf('/arquivo/caixa/delete/id/%d', id));
            }
        });
    },
    openBox: function(id) {
        Message.showConfirmation({
            'body': sprintf(UI_MSG.MN122, 'abrir'),
            'yesCallback': function(){
                try{
                    $.ajax({
                         url: "/arquivo/caixa/open-box",
                        type: "POST",
                        data: {id: id},
                        dataType:'json'
                    }).success(function (result) {
                            if (result.error) {
                                Message.showError(result.msg);
                            }else{
                                Message.showSuccess(result.msg);
                                PesquisarCaixaArquivo.reloadGrid();
                            }
                      })
                      .error(function (err) {
                          Message.showError("Ocorreu um erro inesperado na execução");
                      });

                } catch (e) {
                    Message.showError(e.message);
                }
            }
        });
    },
    closeBox: function(id) {
        Message.showConfirmation({
            'body': sprintf(UI_MSG.MN122, 'fechar'),
            'yesCallback': function(){
                try{
                    $.ajax({
                         url: "/arquivo/caixa/close-box",
                        type: "POST",
                        data: {id: id},
                        dataType:'json'
                    }).success(function (result) {
                            if (result.error) {
                                Message.showError(result.msg);
                            }else{
                                Message.showSuccess(result.msg);
                                PesquisarCaixaArquivo.reloadGrid();
                            }
                      })
                      .error(function (err) {
                          Message.showError("Ocorreu um erro inesperado na execução");
                      });

                } catch (e) {
                    Message.showError(e.message);
                }
            }
        });
    }
};

$(PesquisarCaixaArquivo.init);

