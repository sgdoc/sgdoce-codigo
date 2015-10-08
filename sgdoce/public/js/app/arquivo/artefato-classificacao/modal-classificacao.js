ModalClassificacaoArtefato = {
    _objFormGrid: $('#form_artefato_arquivar'),
    _objGridCaixa: $('#grid_caixa'),

    init: function() {
        ModalClassificacaoArtefato
                .events() //seta os eventos e configurações necessárias para a tela
                .gridCaixa(); //monta grid de caixa para arquivamento

        //em caso de atualização de classificação
        //transfere para o form da grid de caixas o valor da classificação
        if ($('#sqClassificacao_hidden').val() != '') {
            ModalClassificacaoArtefato.updateClassificationForGrid($('#sqClassificacao_hidden').val());
        }
    },
    events: function() {
        $('#btnSave'              ).on('click', ModalClassificacaoArtefato.handleFormSubmit);
        $('input[name="arquivar"]').on('change', ModalClassificacaoArtefato.handleChangeArchivingQuestion);
        var inputClassificacao = $('.sqClassificacao');

        inputClassificacao.simpleAutoComplete("arquivo/artefato-classificacao/search-classificacao-artefato/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        },function(data){
            //transfere para o form da grid de caixas o valor da classificação selecionada
            ModalClassificacaoArtefato.updateClassificationForGrid(data[1]);

            //se alterar a classificação e é pra arquivar, atualiza a grid
            if ($('#arquivar-1').is(':checked')) {
                ModalClassificacaoArtefato.reloadGridCaixa();
            }
        });

        inputClassificacao.on('focusout',ModalClassificacaoArtefato.handlerBlurClassification);

        return ModalClassificacaoArtefato;
    },
    gridCaixa: function() {
        Grid.load(ModalClassificacaoArtefato._objFormGrid, ModalClassificacaoArtefato._objGridCaixa);
        return ModalClassificacaoArtefato;
    },
    reloadGridCaixa: function(){
        ModalClassificacaoArtefato._objFormGrid.submit();
        return ModalClassificacaoArtefato;
    },
    updateClassificationForGrid:function(sqClassificacao) {
        ModalClassificacaoArtefato._objFormGrid.find('#sqClassificacao').val(sqClassificacao);
    },
    handlerBlurClassification: function(){
        setTimeout(function(){
            if ($(this).val() == '' || $('#sqClassificacao_hidden').val() == '') {
                ModalClassificacaoArtefato._objFormGrid.find('#sqClassificacao').val('');
                $('#arquivar-0').trigger('click');
                ModalClassificacaoArtefato.reloadGridCaixa();
            }
        },200);
    },
    handleChangeArchivingQuestion: function(){
        var yes = parseInt($(this).val());
        var containerGrid = $('.archivingGrid');
        if (yes) {
            if ($('#sqClassificacao_hidden').val() == '' || $('.sqClassificacao').val() == '') {
                Message.showAlert('Selecione a classificação');
                $('#arquivar-0').trigger('click');
                return false;
            }else{
                containerGrid.show();
                ModalClassificacaoArtefato.reloadGridCaixa();
            }
        } else {
            containerGrid.hide();
        }
    },
    handleFormSubmit: function(){
        var form = $('#form_artefato_classificacao')
        if (form.valid()) {
            var sqCaixa = ModalClassificacaoArtefato._objFormGrid.find('input[name="sqCaixa"]:checked').val();

            if ($('#arquivar-1').is(':checked') && !sqCaixa) {
                Message.showAlert('Selecione a Caixa. <br />Caso nenhuma caixa estiver disponível, é necessario o cadastro da caixa antes do arquivamento');
                return false;
            }
            $('<input />',{type:'hidden',name:'sqCaixa',value:sqCaixa}).appendTo(form);
            form.submit();
        }
        return false;
    }

};

$(ModalClassificacaoArtefato.init);

