GerarEtiqueta = {
    SQ_TIPO_ETIQUETA_ELETRONICA: null,
    _formId: 'form_gerar_etiqueta',
    init: function() {
        GerarEtiqueta.events();
    },
    events: function() {
        $('#btn_gerar'     ).on('click' , GerarEtiqueta.handleFormSubmit);
        $('#sqTipoEtiqueta').on('change', GerarEtiqueta.handleChangeDigitalType);
        setTimeout(function(){
            $('#sqTipoEtiqueta').trigger('change');
        },100);

        $('#sqUnidadeOrg'  ).simpleAutoComplete("etiqueta/gerar-etiqueta/search-unidade-org/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#sqQuantidadeEtiqueta').val(localStorage.getItem('sqQuantidadeEtiqueta'));
        $('#sqTipoEtiqueta').val(localStorage.getItem('sqTipoEtiqueta'));
        $('#sqUnidadeOrg').val(localStorage.getItem('sqUnidadeOrg'));
        $('#sqUnidadeOrg_hidden').val(localStorage.getItem('sqUnidadeOrg_hidden'));
        $('#inLoteComNupSiorg').val(localStorage.getItem('inLoteComNupSiorg'));

        return GerarEtiqueta;
    },
    handleChangeDigitalType: function() {
        var objInLoteComNupSiorg = $('#inLoteComNupSiorg');
        var objRequiredLote      = $('.lote');

        objInLoteComNupSiorg.parents('.control-group').removeClass('error');
        objInLoteComNupSiorg.siblings('p.help-block').remove();

        if ($(this).val() == GerarEtiqueta.SQ_TIPO_ETIQUETA_ELETRONICA) {
            objInLoteComNupSiorg.val(1).prop('disabled', 'disabled');
            objRequiredLote.hide();
        } else {
            objInLoteComNupSiorg.val('').removeProp('disabled');
            objRequiredLote.show();
        }
    },
    handleFormSubmit: function() {

        localStorage.setItem('sqQuantidadeEtiqueta' ,$('#sqQuantidadeEtiqueta').val());
        localStorage.setItem('sqTipoEtiqueta'       ,$('#sqTipoEtiqueta').val());
        localStorage.setItem('sqUnidadeOrg'         ,$('#sqUnidadeOrg').val());
        localStorage.setItem('sqUnidadeOrg_hidden'  ,$('#sqUnidadeOrg_hidden').val());
        localStorage.setItem('inLoteComNupSiorg'    ,$('#inLoteComNupSiorg').val());


        if ($('form').valid()) {
            $('.campos-obrigatorios').addClass('hidden');
            Message.showConfirmation({
                'body': UI_MSG.MN001,
                'subject': 'Atenção',
                'yesCallback': function() {
                    $('#' + GerarEtiqueta._formId).submit();
                }
            });
        }
        return false;
    }
};

$(GerarEtiqueta.init);

