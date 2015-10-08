var Origem = {
    getGroup: function (vl) {
        switch (vl) {
            case 1:
                return $('.divOrigemTipoPessoaIcmbio, .divOrigemSqPessoaIcmbio, .divOrigemTipoPessoa, .divNacionalidade, .divOrigemSqPessoa, .divOrigemCPF, .divOrigemCNPJ, .divOrigemPassaporte');
                break;
            case 2:
                return $('.divNacionalidade, .divOrigemSqPessoa, .divOrigemCPF, .divOrigemCNPJ, .divOrigemPassaporte, .divOrigemTipoPessoaIcmbio, .divOrigemTipoPessoa, .divOrigemSqPessoaIcmbio');
                break;
            case 3:
                return $('.divNacionalidade, .divOrigemSqPessoa, .divOrigemCPF, .divOrigemCNPJ, .divOrigemPassaporte, .divOrigemSqPessoaIcmbio');
                break;
        }
        return true;
    },
    getElement: function (vl) {
        switch (vl) {
            case 1:
                return $('#sqTipoPessoaOrigemIcmbio, #sqTipoPessoaOrigem, #sqPessoaOrigem, #nuCPFOrigem, #nuCNPJOrigem, #nuPassaporteOrigem, #sqPessoaIcmbio');
                break;
            case 2:
                return $('#nuCPFOrigem, #nuCNPJOrigem, #nuPassaporteOrigem, #sqPessoaIcmbio');
                break;
        }
        return true;
    },
    limpaCampo: function (vl) {
        var obj = Origem.getElement(vl);
        obj.val('');
        return true;
    },
    hideCampo: function (vl) {
        var obj = Origem.getGroup(vl);
        obj.hide();
        return true;
    },
    removeObrigatoriedade: function (vl) {
        var obj = Origem.getElement(vl);
        obj.not('#nuCPFOrigem')//excluindo cpf da não obrigatoridade
                .removeClass('required');
        return true;
    },
    tipoPessoaOrigem: function () {
        if ($("input[name='procedenciaInterno']:checked").val() == 'interno') {
            $('.divOrigemTipoPessoaIcmbio').show();
        }

        if ($("input[name='procedenciaInterno']:checked").val() == 'externo') {
            $('.divOrigemTipoPessoa').show();
        }
        return true;
    },
    tipoOrigemIcmbio: function () {
        Origem.removeObrigatoriedade(2);
        if ($('#sqTipoPessoaOrigemIcmbio').val() != '') {
            $('.divOrigemSqPessoaIcmbio').show();
            $('#sqPessoaIcmbio').addClass('required');
        } else {
            $('.divOrigemSqPessoaIcmbio').hide();
            $('#sqPessoaIcmbio').removeClass('required');
        }
        return true;
    },
    tipoOrigem: function () {
        Origem.removeObrigatoriedade(2);
        Origem.hideCampo(3);
        switch ($('#sqTipoPessoaOrigem').val()) {
            case '1':
            case '3':
                if ($("input[name='tpNacionalidade']:checked").val() == '0') {
                    Origem.setaAutocompletePessoa(3);
                    $('.divNacionalidade, .divOrigemPassaporte,.divOrigemSqPessoa').show();
                    $('#sqPessoaOrigem,#nuPassaporteOrigem').addClass('required');
                } else {
                    Origem.setaAutocompletePessoa(1);
                    $('.divNacionalidade, .divOrigemCPF, .divOrigemSqPessoa').show();
                    $('#sqPessoaOrigem,#nuPassaporteOrigem').addClass('required');
                }
                $('#dvLabelSqPessoa').html('<span class="required">* </span>Nome');

                $('.divCadastraPessoa').show();
                $('#origem .liPessoaFisica').show();
                $('#origem .liPessoaJuridica').hide();
                break;
            case '2':
                Origem.setaAutocompletePessoa(2);
                $('.divOrigemTipoPessoa,.divOrigemCNPJ, .divOrigemSqPessoa').show();
                $('#sqPessoaOrigem, #nuCNPJOrigem').addClass('required');
                $('#dvLabelSqPessoa').html('<span class="required">* </span>Razão Social');

                $('.divCadastraPessoa').show();
                $('#origem .liPessoaFisica').hide();
                $('#origem .liPessoaJuridica').show();
                break;
            case '4':
            case '5':
                Origem.setaAutocompletePessoa(5);
                $('#dvLabelSqPessoa').html('<span class="required">* </span>Nome');
                $('.divOrigemTipoPessoa,.divOrigemSqPessoa').show();
                $('#sqPessoaOrigem').addClass('required');

                $('.divCadastraPessoa').hide();
                break;
            default:
                // faz nada ainda
        }
        return true;
    },
    camposInternosExternos: function () {
        if ($("input[name='procedenciaInterno']:checked").val() == 'interno') {
            $('.divOrigemTipoPessoaIcmbio').show();
            $('#sqTipoPessoaOrigemIcmbio').addClass('required');

            Origem.tipoOrigemIcmbio();
        }

        if ($("input[name='procedenciaInterno']:checked").val() == 'externo') {
            $('.divOrigemTipoPessoa').show();
            $('#sqTipoPessoaOrigem').addClass('required');
            Origem.tipoOrigem();
        }
        return true;
    },
    autocomplete: function () {
        $('#sqPessoaIcmbio').simpleAutoComplete("/artefato/pessoa/autocomplete", {
            extraParamFromInput: '#sqTipoPessoaOrigemIcmbio',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
        return true;
    },
    zeraComando: function () {
        $('#sqPessoaOrigem').val('');
        $('#sqPessoaOrigem').removeAttr('autocomplete');
        $('#sqPessoaOrigem').removeAttr('name');
        $('#sqPessoaOrigem').attr('name', 'sqPessoaOrigem');
        $('#sqPessoaOrigem_hidden').remove();
        return true;
    },

    setaAutocompletePessoa: function(vl) {
        Origem.zeraComando();
        $('#sqPessoaOrigem').simpleAutoComplete("/artefato/pessoa/autocomplete/extraParam/" + vl, {
            extraParamFromInput: vl.toString(),
            attrCallBack: 'id',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        },function(){            
            $('#nuCPFOrigem').val('');
            $('#nuCNPJOrigem').val('');
            $('#nuPassaporteOrigem').val('');            
            Origem.searchDadosPessoa();
        });
        return true;
    },

    searchDadosPessoa: function (callback) {
        callback = callback||function(){};
        var tipoPessoa = $('#sqTipoPessoaOrigem').val();
        if ($('#sqTipoPessoaOrigem').val() == '1') {
            var nacionalidade = $("input[name='tpNacionalidade']:checked").val();
            if ($("input[name='tpNacionalidade']:checked").val() == 1) {
                var nuCpfCnpjPassaporte = $('#nuCPFOrigem').val();
            } else {
                var nuCpfCnpjPassaporte = $('#nuPassaporteOrigem').val();
            }
        } else if ($('#sqTipoPessoaOrigem').val() == '2') {
            var nuCpfCnpjPassaporte = $('#nuCNPJOrigem').val();
        }

        if (!/Unidade/.test($('#sqTipoPessoaDestino option:selected').text()) && !/Unidade/.test($('#sqTipoPessoaOrigem option:selected').text())) {
            if ($('#sqPessoa_hidden').val() || $('#sqPessoaOrigem_hidden').val() || $('#nuCNPJDestino').val() || $('#nuCPFDestino').val() || $('#nuCNPJOrigem').val() || $('#nuCPFOrigem').val()) {
                var params = {
                        sqPessoaCorporativo: $('#sqPessoaOrigem_hidden').val(),
                        sqTipoPessoa: $('#sqTipoPessoaOrigem').val(),
                        sqNacionalidade: nacionalidade,
                        nuCpfCnpjPassaporte: nuCpfCnpjPassaporte
                };
                $.post('/artefato/pessoa/get-dados-pessoa', params, function (data) {
                    Origem.setaValorDocumento(data, tipoPessoa, nacionalidade, callback);
                });
            }
        }
    },

    setaValorDocumento: function (doc, tipoPessoa, nacionalidade, callback) {
        callback = callback || function(){};
        
        if (doc != '') {
            switch (tipoPessoa) {
                case '1':
                    if (nacionalidade == '1') {
                        $('#nuCPFOrigem').val(Origem.cpfCnpj(doc.nuCpf));
                    } else {
                        $('#nuPassaporteOrigem').val(doc.nuPassaporte);
                    }
                    break;
                case '2':
                    $('#nuCNPJOrigem').val(Origem.cpfCnpj(doc.nuCnpj));
                    break;
            }
            $('#sqPessoaOrigem').val(doc.noPessoa);
            $('#sqPessoaOrigem_hidden').val(doc.sqPessoa);
        } 
        callback();
    },
    inicializaFormulario: function () {
        Origem.hideCampo(1);
        Origem.autocomplete();
        Origem.camposInternosExternos();
    },
    initCampos: function () {
        Origem.removeObrigatoriedade(1); // remove a obrigatoriedade de todos os campos de destinatario
        Origem.inicializaFormulario();   // inicializando o formulario, setando acoes, exibindo grupos e etc.

        $("input[name='procedenciaInterno']")
                .addClass('required')
                .click(function () {

                    var div_recebidoPor = $('#div_recebidoPor');
                    if ($(this).val() == 'externo') {
                        div_recebidoPor.show().find('#noPessoa,#noPessoa_hidden,#dtEntrada').addClass('required');
                    } else {
                        div_recebidoPor.hide().find('#noPessoa,#noPessoa_hidden,#dtEntrada').removeClass('required');
                    }

                    Origem.limpaCampo(1);
                    Origem.hideCampo(2);
                    Origem.camposInternosExternos();

                    $('#sqPessoaOrigem_hidden').val('');
                });

        $('#sqTipoPessoaOrigem').change(function () {
            Origem.limpaCampo(2);
            Origem.hideCampo(3);
            Origem.tipoOrigem();
        });

        $('#sqTipoPessoaOrigemIcmbio').change(function () {
            Origem.limpaCampo(2);
            Origem.tipoOrigemIcmbio();
        });

        $('input[name=tpNacionalidade]').click(function () {
            Origem.limpaCampo(2);
            Origem.tipoOrigem();
        });

        $('#nuCPFOrigem, #nuCNPJOrigem, #nuPassaporteOrigem').blur(function () {
            var elem = $(this);
            var clearName = function(){
                $('#sqPessoaOrigem,#sqPessoaOrigem_hidden').val('');
            };
            if (elem.val() == '') {
                clearName();
                return false;
            }
            var go = false;
            switch (true){
                case elem.hasClass('cpf') && isCPFValid(elem.val()):
                    go = true;
                    break;
                case elem.hasClass('cnpj') && isCNPJValid(elem.val()):
                    go = true;
                    break;
                case elem.hasClass('passaporte'):
                    go = true;
                    break;
                default:
                    go = false;
                    break;
            }

            if (go) {
                var label = $(this).parent().parent().find('.control-label').text().trim().replace('* ','');
                var callback = function(label){
                    if ($('#sqPessoaOrigem').val() == ''){
                        Message.showAlert(sprintf(UI_MSG.MN128, label));
                    }
                };

                Origem.searchDadosPessoa(function(){callback(label)});
            }else{
                clearName();
                return false;
            }
        });

        $('#sqTipoPessoaOrigemIcmbio').change(function () {
            Origem.tipoOrigemIcmbio();
        });

        $('#sqPessoaOrigem').keydown(function () {
            if(!$.trim($(this).val())){
                $('#nuCPFOrigem, #nuCNPJOrigem, #nuPassaporteOrigem').val('');
            }
        });
    },

    cpfCnpj: function(v)
    {
        if( v ) {
            v=v.replace(/\D/g,"")
            if (v.length <= 14) { //CPF
                v=v.replace(/(\d{3})(\d)/,"$1.$2")
                v=v.replace(/(\d{3})(\d)/,"$1.$2")
                v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
            } else { //CNPJ
                v=v.replace(/^(\d{2})(\d)/,"$1.$2")
                v=v.replace(/^(\d{2})\.(\d{3})(\d)/,"$1.$2.$3")
                v=v.replace(/\.(\d{3})(\d)/,".$1/$2")
                v=v.replace(/(\d{4})(\d)/,"$1-$2")
            }
        }
        return v;
    }
};
