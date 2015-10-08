ModalReturn = {
    _objForm: $('#form_artefato_return_sigiloso'),
    init: function() {
        ModalReturn.events();
    },
    events: function() {
        $('#btnSave').on('click', ModalReturn.save);

        $('#sqPessoaDestino')
            .blur(function(){
                    if (! $('#sqPessoaDestino_hidden').val()) {
                        $('#sqPessoaDestinoInterno').prop('disabled','disabled');
                    }
                })
            .simpleAutoComplete("artefato/tramite/search-unidade-org/", {
                attrCallBack: 'rel',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function(arrID, element) {
                if (arrID[1]) {
                    $('#sqPessoaDestinoInterno').removeProp('disabled');
                } else {
                    $('#sqPessoaDestinoInterno').prop('disabled','disabled');
                }
            });
            
        $('#sqPessoaDestinoInterno').simpleAutoComplete("/artefato/tramite/funcionario-unidade-setor", {
            extraParamFromInput  : '#sqPessoaDestino_hidden',
            autoCompleteClassName: 'autocomplete',
            selectedClassName    : 'sel'
        });

        return ModalReturn;
    },

    save: function(){
        $('.campos-obrigatorios:visible').remove();
        if (ModalReturn._objForm.valid()) {
            Message.showConfirmation({
                body: 'Tem certeza que deseja retornar o trâmite para pessoa informada?',
                yesCallback: function () {
                    try {
                        Message.wait();
                        $.ajax({
                            type: "POST",
                             url: "/artefato/tramite/return-sigiloso",
                            data: ModalReturn._objForm.serialize()
                        }).success(function (result) {
                            Message.waitClose();
                            if (result.error) {
                                Message.showError(result.msg);
                            }else{
                                $('#table-grid-area-trabalho-externa').dataTable().fnDraw(false);
                                $('.modal:visible').modal('hide')
                                Message.showSuccess(result.msg);
                            }
                        }).error(function (err) {
                            Message.waitClose();
                            Message.showError("Ocorreu um erro inesperado na execução");
                        });

                    } catch (e) {
                        Message.waitClose();
                        Message.showError(e.message);
                        $('div.modal-footer:visible').find('a.btn-primary:visible').focus();
                    }
                }
            });
        }
        return false;
    }
};

$(ModalReturn.init);

