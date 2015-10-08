ModalArquivar = {
    _objForm: $('#form_artefato_arquivar'),
    _objGridCaixa: $('#grid_caixa'),

    init: function() {
        $(document).ajaxStop(function() {
            ModalArquivar.checkResultGrid();
        });
        ModalArquivar.events().grid();
    },
    events: function() {
        $('#btnSave').on('click', ModalArquivar.save);
        return ModalArquivar;
    },
    grid: function() {
        Grid.load(ModalArquivar._objForm, ModalArquivar._objGridCaixa);
        return ModalArquivar;
    },

    checkResultGrid: function() {
        if (ModalArquivar._objGridCaixa.find('tbody tr td.dataTables_empty').length !== 0) {
            Message.showConfirmation({
                'body':'Não há nenhuma caixa aberta com a classificação adequada para este artefato. Deseja cadastrar um nova caixa?',
                'yesCallback': function() {
                    console.log('vai para cadastro');
                    $(window.document.location).attr('href', '/arquivo/caixa/index');
                }
            });
        }
        return ModalArquivar;
    },

    save: function(){
        if (ModalArquivar._objForm.find('input:radio:checked').length !== 0) {
            //# fazer join na view de area de trabalho para saber da imagem

            try {
                $.ajax({
                    type: "POST",
                     url: "/arquivo/arquivamento/arquivar",
                    data: ModalArquivar._objForm.serialize()
                }).success(function (result) {
                    if (result.error) {
                        Message.showError(result.message);
                    }else{
                        Message.showSuccess(result.message, function(){
                            window.document.location.reload();
                        });
                    }

                }).error(function (err) {
                    Message.showError("Ocorreu um erro inesperado na execução");
                });

            } catch (e) {
                Message.showError(e.message);
                $('div.modal-footer:visible').find('a.btn-primary:visible').focus();
            }
        }else{
            Message.showAlert('Nenhuma caixa selecionada');
            return false;
        }
        return false;
    }

};

$(ModalArquivar.init);

