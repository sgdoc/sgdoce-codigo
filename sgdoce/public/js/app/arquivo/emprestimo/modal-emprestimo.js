ModalEmprestimo = {
    _objForm: $('#form_artefato_emprestimo'),
    init: function() {
        ModalEmprestimo.events().ajustaCamposDestino();
    },
    events: function() {
        $('#btnSave').on('click', ModalEmprestimo.save);
        return ModalEmprestimo;
    },
    ajustaCamposDestino: function() {
        $('#sqPessoaEncaminhado,#cb_noCargoEncaminhado,#sqPessoaEncaminhadoExterno').parents('.control-group').remove();
        $('#labelTipoPessoaDestinoInterno').text('Tipo de Destino');
    },
    save: function() {
        $('.campos-obrigatorios > .close').trigger('click');
        if (ModalEmprestimo._objForm.valid()) {
            try {
                $.ajax({
                    type: "POST",
                    url: "/arquivo/emprestimo/save-emprestimo",
                    data: ModalEmprestimo._objForm.serialize()
                }).success(function(result) {
                    if (result.error) {
                        Message.showError(result.msg);
                    } else {
                        Message.showSuccess(result.msg, function() {
                            $('div.modal-header:visible').find('a.close:visible').trigger('click');
                            //é chamado metodo da area de trabalho
                            AreaTrabalho.Grid.reloadGridArquivo();
                        });
                    }

                }).error(function(err) {
                    Message.showError("Ocorreu um erro inesperado na execução");
                });

            } catch (e) {
                Message.showError(e.message);
                $('div.modal-footer:visible').find('button.btn-primary:visible').focus();
            }
        }
        return false;
    }
};

$(ModalEmprestimo.init);

