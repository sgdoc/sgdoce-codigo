ModalFirstPiece = {
    _objForm: $('#form_artefato_primeira_peca'),

    init: function() {
        ModalFirstPiece.events();
    },
    events: function() {
        $('#btnSave').on('click', ModalFirstPiece.save);
        $('#child').simpleAutoComplete("artefato/vinculo/autocomplete-documents-first-piece/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            extraParamFromInput: '#parent',
        });
        $('.infolink').tooltip();
        return ModalFirstPiece;
    },

    save: function(){
        if (ModalFirstPiece._objForm.valid()) {
            //# fazer join na view de area de trabalho para saber da imagem
            try {
                Message.showConfirmation({
                    body: UI_MSG.MN125,
                    yesCallback: function() {
                        $.ajax({
                            type: "POST",
                             url: "/artefato/vinculo/save-first-piece",
                            data: ModalFirstPiece._objForm.serialize()
                        }).success(function (result) {
                               if (result.status) {
                                   $('.modal:visible a.close').click();
                                   //atualiza a minha grid
                                   $('#form-visualizar-area-trabalho-minha').submit();
                                   Message.showSuccess(UI_MSG[result.message]);
                               }else{
                                   Message.showError(UI_MSG[result.message] + '<br />' + result.errorCompl);
                               }
                        }).error(function (request, status, error) {
                            Message.showError("Ocorreu um erro inesperado na execução. <br />" + request.responseText);
                            console.debug(request);
                            console.debug(status);
                            console.debug(error);
                        });
                    }
                });
            } catch (e) {
                Message.showError(e.message);
            }
            return true;
        }
        return false;
    }
};

$(ModalFirstPiece.init);

