PesquisarEtiqueta = {
    init: function() {
        $('#form_print').remove();
        $('#sqUnidadeOrg_hidden').val(sessionStorage.getItem('sqUnidadeOrg_hidden'));
        PesquisarEtiqueta.events().grid();

        //limpa o storage do cadastro
        localStorage.clear();
    },
    events: function() {
        $('#btn_clear').on('click', PesquisarEtiqueta.handleClickClear);
        $('#btn_pesquisar').on('click', PesquisarEtiqueta.handleFormSubmit);
        $('#btn_filtros').on('click', PesquisarEtiqueta.handleClickFiltros);
        $('#sqUnidadeOrg').simpleAutoComplete("etiqueta/gerar-etiqueta/search-unidade-org/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        return PesquisarEtiqueta;
    },
    handleClickClear: function() {
        $('#sqUnidadeOrg_hidden').val(0);
        $('#sqTipoEtiqueta,#sqUnidadeOrg,#inLoteComNupSiorg').val('');
        sessionStorage.clear();
        return false;
    },
    handleClickFiltros: function() {
        $('html,body').animate({scrollTop: 0}, 500);
    },
    handleFormSubmit: function() {

        var objSqUnidadeOrg_hidden = $('#sqUnidadeOrg_hidden');
        var objSqTipoEtiqueta      = $('#sqTipoEtiqueta');
        var objInLoteComNupSiorg   = $('#inLoteComNupSiorg');
        var objCamposObrigatorios  = $('.campos-obrigatorios');

        sessionStorage.setItem('sqUnidadeOrg_hidden', objSqUnidadeOrg_hidden.val());
        sessionStorage.setItem('sqTipoEtiqueta', objSqTipoEtiqueta.val());
        sessionStorage.setItem('inLoteComNupSiorg', objInLoteComNupSiorg.val());

        if (objSqUnidadeOrg_hidden.val() == '0' && objSqTipoEtiqueta.val() == '' && objInLoteComNupSiorg.val() == '') {
            if (objCamposObrigatorios.length == 0) {
                $('body .row-fluid .span9 > h1').after($('<div />', {class: 'alert alert-error campos-obrigatorios hidden'}));
            }
            objCamposObrigatorios.html(UI_MSG.MN087).removeClass('hidden').show();
            setTimeout(function() {
                objCamposObrigatorios.fadeOut();
            }, 5000);
            $('html,body').animate({scrollTop: 0}, 500);

            return false;
        } else {
            $('html,body').animate({scrollTop: 300}, 500);
        }
    },
    grid: function() {
        Grid.load($('#form_pesquisa_etiqueta'), $('#grid_etiqueta'));
        return PesquisarEtiqueta;
    },
    imprimir: function(id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/etiqueta/gerar-etiqueta/validate-print',
            data: {sqLoteEtiqueta: id},
            success: function(result) {

                var callback = function() {
                    var form = $('<form />', {
                        action: '/etiqueta/gerar-etiqueta/print',
                        id: 'form_print',
                        method: 'post',
                        class: 'hidden',
                        target: '_black'
                    });
                    $('body').append(form.append($('<input />', {value: id, name: 'lote'})));
                    form.submit();
                };

                if (result.user) {
                    Message.showConfirmation({
                        body: sprintf(UI_MSG.MN042, result.user, result.date),
                        yesCallback: callback
                    });
                } else {
                    callback();
                }
            }
        });
    }
};

$(PesquisarEtiqueta.init);

