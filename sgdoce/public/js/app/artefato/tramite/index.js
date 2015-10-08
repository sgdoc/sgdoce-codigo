var Tramitar = {
    _formId            : 'form_tramite_artefato',
    _sqUnidadeLogada   : 0,
    _artefatoSigiloso  : 0, //boolean vindo da view

    _sqPessoaTramiteExternoCurrent : '',
    _goAddress : false,
    /**
     *
     * o setInterval é utilizado para ficar escutando o sqPessoaOrigem_hidden
     * para tramite externo devido as configurações estarem setadas no script origem.js
     */
    _interval : null,

    init: function() {
        Tramitar.events();
    },
    events: function() {

        $('#btn_save').on('click', Tramitar.handleFormSubmit);

        $('#stImprimeGuia').on('change',function(){
            if ($(this).is(':checked')) {
                $('#reqEndereco, .endereco').show();
            }else{
                $('#reqEndereco, .endereco').hide();
            }
            $('#sqEndereco').toggleClass('required');
        });

        $('input[name="tipo_tramite"]').on('change',function(){
            var selectedValue = $(this).val();
            $('.tramiteControle').hide();
            if (selectedValue == 2) {
                $('#stImprimeGuia').prop('checked','checked');
                Tramitar.setInterval();
            }else{
                Tramitar.clearInterval();
                $('#stImprimeGuia').removeProp('checked');
            }
            $('#tramite-'+selectedValue).show();
            Tramitar.clearInputTramite($(this).val());
        });

        $('#sqUnidadeOrg').simpleAutoComplete("artefato/tramite/search-unidade-org/", {
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        }, function(arrID, element) {
            if ((arrID[1] == Tramitar._sqUnidadeLogada) || Tramitar._artefatoSigiloso ) {
                $('#sqPessoaDestinoInterno').removeProp('readonly');
                Tramitar.viewInputPerson(true);
            } else {
                Tramitar.viewInputPerson(false);
                $('#sqPessoaDestinoInterno').prop('readonly','readonly');
            }
        });

        $('#sqPessoaDestinoInterno').simpleAutoComplete("/artefato/tramite/funcionario-unidade-setor", {
            extraParamFromInput  : '#sqUnidadeOrg_hidden',
            autoCompleteClassName: 'autocomplete',
            selectedClassName    : 'sel'
        });


        return Tramitar;
    },

    setInterval: function(){
        Tramitar._interval = setInterval(function(){
            var objSqPessoa   = $('#sqPessoaOrigem_hidden');
            var objSqEndereco = $('#sqEndereco');

            /**
             * se mudou o sqPessoa recupera os endereço da nova pessoa
             */
            if (objSqPessoa.val() != Tramitar._sqPessoaTramiteExternoCurrent) {
                Tramitar._sqPessoaTramiteExternoCurrent = objSqPessoa.val();
                Tramitar._goAddress = true;
            }else{
                Tramitar._goAddress = false;
            }

            if (Tramitar._goAddress) {
                if (objSqPessoa.val() == '') {
                    $('#sqEndereco').html('<option value="">Selecione uma opção</option>');
                }else{
                    $.post('/artefato/tramite/get-enderecos-by-pessoa', {
                            sqPessoa: objSqPessoa.val()
                        },
                        function(data){
                            $('#sqEndereco').html('');
                            Tramitar.loadCombo(data, objSqEndereco);
                        }
                    );
                }
            }
        },300);
    },
    clearInterval: function(){
        clearInterval(Tramitar._interval);
    },

    loadCombo: function(data, combo, selectedValue){
        selectedValue = selectedValue || false;
        var html = '';
        $.each(data, function(index, value) {
            html += '<option value="' + index + '" title="' + value + '">' + value + '</option>';
        });
        combo.html(html);
        if(selectedValue){
            combo.val(selectedValue).change();
        }
    },

    handleFormSubmit: function(){
        $('.campos-obrigatorios').addClass('hidden');
        if ($('form').valid()) {
            Message.showConfirmation({
                body: UI_MSG.MN113,
                yesCallback: function () {
                    $('#' + Tramitar._formId).submit();
                }
            });
        }
        return false;
    },

    viewInputPerson: function(view) {
        if (view) {
            $('.destinoInterno').show();
        }else{
            $('.destinoInterno').hide();
            $('#sqPessoaDestinoInterno_hidden').val(0);
            $('#sqPessoaDestinoInterno').val('');
        }
    },
    clearInputTramite: function(type) {
        switch (type){
            case 2:
                $('#sqUnidadeOrg_hidden,#sqPessoaDestinoInterno_hidden').val(0);
                $('#sqUnidadeOrg,#sqPessoaDestinoInterno').val('');
                break;
            default:
                break;
        }
        return Tramitar;
    }
};

$(Tramitar.init);

