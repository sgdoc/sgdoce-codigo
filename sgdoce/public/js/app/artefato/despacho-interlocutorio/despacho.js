NovoDespacho = {
    FORM_ID: '#form_novo_despacho',
    _urlIndex: '/artefato/despacho-interlocutorio/index/id/%d',
    init: function() {
        NovoDespacho.events();
        NovoDespacho.handleViewForm();
    },
    events: function() {

        $('#sqUnidadeDestino').simpleAutoComplete("etiqueta/gerar-etiqueta/search-unidade-org/", {
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#sqPessoaAssinatura').simpleAutoComplete("artefato/despacho-interlocutorio/search-pessoa-unidade/", {
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#btn_salvar').on('click', NovoDespacho.handleFormSubmit);

        $('#stCargoFuncao1').on('click', NovoDespacho.handleOnCargo);
        $('#stCargoFuncao2').on('click', NovoDespacho.handleOnFuncao);

        $('#cancelar').click(function(e){
            e.preventDefault();
            var _url = sprintf(
                    '/artefato/despacho-interlocutorio/index/id/%d/back/%s',
                    $('#sqArtefato').val(),
                    AreaTrabalho.getUrlBack()
                    );
            AreaTrabalho.initModal(_url);
            return false;
        });

    },
    handleFormSubmit: function() {
        try {
            if ($(NovoDespacho.FORM_ID).valid()) {
                $('#btn_salvar').attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "/artefato/despacho-interlocutorio/save",
                    data: $('#form_novo_despacho').serialize(),
                }).success(function(result) {
                    if (result == '') {
                        var _url = sprintf(
                                '/artefato/despacho-interlocutorio/index/id/%d/back/%s',
                                $('#sqArtefato').val(),
                                AreaTrabalho.getUrlBack()
                                );
                        AreaTrabalho.initModal(_url);
                    } else {
                        Message.showError(result.message);
                    }
                    $('#btn_salvar').attr('disabled', false);
                }).error(function(err) {
                    Message.showError("Ocorreu um erro inesperado na execução");
                    $('#btn_salvar').attr('disabled', false);
                });
            }
        } catch (e) {
            Message.showError(e.message);
            $('div.modal-footer:visible').find('a.btn-primary:visible').focus();
        }
    },
    handleViewForm: function() {
        if ($('#view').val() == 1) {
            $('h1').html('Visualizar Despacho');
            $('.top-bar').hide();
            $('#form_novo_despacho :input').prop('disabled', true);
            $('#btn_salvar').remove();
        }
    },
    handleOnCargo: function(){
        var isChecked = $(this).is(':checked');

        if( isChecked ) {
            $("#divCargo").removeClass('hidden').show();
            $("#sqCargoAssinatura").addClass('required');
            $("#divFuncao").addClass('hidden').hide();
        } else {
            $("#divFuncao").addClass('hidden').hide();
            $("#sqFuncaoAssinatura").removeClass('required');
            $("#divCargo").removeClass('hidden').show();

        }
    },
    handleOnFuncao: function(){
        var isChecked = $(this).is(':checked');

        if( isChecked ) {
            $("#divFuncao").removeClass('hidden').show();
            $("#sqFuncaoAssinatura").addClass('required');
            $("#divCargo").addClass('hidden').hide();
        } else {
            $("#divCargo").addClass('hidden').hide();
            $("#sqCargoAssinatura").removeClass('required');
            $("#divFuncao").removeClass('hidden').show();
        }
    }
};

$(NovoDespacho.init);

