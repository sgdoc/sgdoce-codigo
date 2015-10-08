ModalInteressado = {
    initResponsavelDestinatario: function() {
        $('#sqPessoaInteressado,#sqPessoaInteressadoPf,#sqPessoaInteressadoInterna').click(function() {

            $('#sqPessoaDestinatario').val('');
            $('#sqPessoaInteressado_hidden').val('');
            $('#sqPessoaInteressadoPf').val('');
            $('#sqPessoaInteressadoPf_hidden').val('');
            $('#sqPessoaInteressadoInterna_hidden').val('');
            $('#sqPessoaInteressadoInterna').val('');
            $('#nuCPFInteressado').val('');
            $('#nuCNPJInteressado').val('');
        });

        $('input[id=sqPessoaInteressado],input[id=sqPessoaInteressadoPf],input[id=nuCPFInteressado],input[id=sqPessoaInteressadoInterna],input[id=nuCNPJInteressado]').blur(
                function() {
                    var value = '';
                    var doc = '';
                    if ($('#sqPessoaInteressadoPf_hidden').val()) {
                        value = $('#sqPessoaInteressadoPf_hidden').val();
                    }
                    if ($('#sqPessoaInteressado_hidden').val()) {
                        value = $('#sqPessoaInteressado_hidden').val();
                    }
                    if ($('#sqPessoaInteressadoInterna_hidden').val()) {
                        value = $('#sqPessoaInteressadoInterna_hidden').val();
                    }
                    if ($('#nuCNPJInteressado').val()) {
                        doc = $('#nuCNPJInteressado').val();
                    }
                    if ($('#nuCPFInteressado').val()) {
                        doc = $('#nuCPFInteressado').val();
                    }
                    ModalInteressado.getDados($('#sqTipoPessoaInteressado option:selected').val(), value, doc, $("input[name=TipoNacionalidade]:checked").val());
                });
    },
    getDados: function(sqTipoPessoa, sqPessoa, nuCPF, sqNacionalidade) {

        if (sqPessoa || nuCPF) {
            var result;
            var sqArtefato = $('#sqArtefato').val();
            var params = {
                    sqPessoaCorporativo: sqPessoa,
                    sqTipoPessoa: sqTipoPessoa,
                    sqNacionalidade: sqTipoPessoa,
                    nuCpfCnpjPassaporte: nuCPF
            };

            $.post('/artefato/pessoa/get-dados-pessoa', params, function(data) {
                if ($('#sqTipoPessoaInteressado').val() == '1') {
                    $('#sqPessoaInteressadoPf_hidden').val(data.sqPessoa);
                    $('#sqPessoaInteressadoPf').val(data.noPessoa);
                    $('#sqPessoaInteressadoInterna_hidden').val(data.sqPessoa);
                    $('#sqPessoaInteressadoInterna').val(data.noPessoa);
                    if ($("#pessoaBrasilieira").is(":checked")) {
                        $('#nuCPFInteressado').val(ModalInteressado.cpfCnpj(data.nuCpf));
                    } else {
                        $('#nuPassaporteInteressado').val(data.nuPassaporte);
                    }
                } else {
                    $('#nuCPFInteressado').val(ModalInteressado.cpfCnpj(data.nuCpf));
                    $('#nuCNPJInteressado').val(ModalInteressado.cpfCnpj(data.nuCnpj));
                    $('#sqPessoaInteressado_hidden').val(data.sqPessoa);
                    $('#sqPessoaInteressado').val(data.noPessoa);
                }
            });
            return true;
        }
        return false;
    },
    initVisualizaPessoa: function() {
        $('#alterarPFInter,#alterarPJInter,#visualizarPFInter,#visualizarPJInter').parent().addClass('disabled');
        $('#sqPessoaInteressadoPf,#sqPessoaInteressado,#nuCPF').blur(function() {
            if ($('#sqPessoaInteressadoPf').val() || $('#nuCPF').val())
            {
                $('#alterarPFInter').attr('href', '/auxiliar/pessoa-fisica/edit/id/' + $('#sqPessoaInteressadoPf_hidden').val() + '/form/form-interessado-modal/campoPessoa/sqPessoaInteressadoPf/campoCpf/nuCPFInteressado');
                $('#alterarPFInter,#visualizarPFInter').parent().removeClass('disabled');

            } else {
                $('#alterarPFInter,#visualizarPFInter').parent().addClass('disabled');
            }
            if ($('#sqPessoaInteressado').val() || $('#nuCPF').val())
            {
                $('#alterarPJInter').attr('href', '/auxiliar/pessoa-juridica/edit/id/' + $('#sqPessoaInteressado_hidden').val() + '/form/form-interessado-modal/campoPessoa/sqPessoaInteressado/campoCnpj/nuCNPJInteressado');
                $('#alterarPJInter,#visualizarPJInter').parent().removeClass('disabled');
            } else {
                $('#alterarPJInter,#visualizarPJInter').parent().addClass('disabled');

            }
        });

        $('#sqPessoaInteressadoPf,#sqPessoaInteressado').click(function() {
            $(this).closest('.input-append').removeClass('open');
        });


        $('#visualizarPJInter').on('click', function() {
            $("#visualizarPJ").load('/auxiliar/pessoa-juridica/visualizar-matriz-filial/visualizar/true/sqPessoa/' + $('#sqPessoaInteressado_hidden').val());
        });

        $('#visualizarPFInter').on('click', function() {
            $("#visualizarPF").load('/auxiliar/pessoa-fisica/visualizar-pessoa-fisica/sqPessoa/' + $('#sqPessoaInteressadoPf_hidden').val());
        });

        $('.divMultiplaPF, .divMultiplaPJ, .divMultiplaPJInter').click(function() {
            if ($('#sqPessoaInteressado').val() || $('#nuCPF').val())
            {
                $('#alterarPJInter').attr('href', '/auxiliar/pessoa-juridica/edit/id/' + $('#sqPessoaInteressado_hidden').val() + '/form/form-interessado-modal/campoPessoa/sqPessoaInteressado/campoCnpj/nuCNPJInteressado');
                $('#alterarPJInter,#visualizarPJInter').parent().removeClass('disabled');
            } else {
                $('#alterarPJInter,#visualizarPJInter').parent().addClass('disabled');

            }
            if ($('#sqPessoaInteressadoPf').val() || $('#nuCPF').val())
            {
                $('#alterarPFInter').attr('href', '/auxiliar/pessoa-fisica/edit/id/' + $('#sqPessoaInteressadoPf_hidden').val() + '/form/form-interessado-modal/campoPessoa/sqPessoaInteressadoPf/campoCpf/nuCPFInteressado');
                $('#alterarPFInter,#visualizarPFInter').parent().removeClass('disabled');

            } else {
                $('#alterarPFInter,#visualizarPFInter').parent().addClass('disabled');
            }
        });

        $('#alterarPFInter,#alterarPJInter').on('click', function(e) {
            $('#alterarPFInter,#alterarPJInter').closest('.input-append').removeClass('open');
            $('#alterarPJInter,#visualizarPJInter').parent().addClass('disabled');
            $('#alterarPFInter,#visualizarPFInter').parent().addClass('disabled');
            $('#btnLimpar').trigger('click');
        });
    },
    tipoInteressado: function() {


        $('#sqPessoaInteressado').val('');
        $('#nuCPFInteressado').val('');
        $('#nuCNPJInteressado').val('');
        $('#nuPassaporteInteressado').val('');

        var all = $('.divGeralNomeInteressado,.divGeralNomeInteressadoPF,.divGeralNomeInteressadoInterna, .divGeralCPFInteressado , .tpInteressado, .divGeralCNPJInteressado, .divGeralPassaporteInteressado, .divGeralEstrangeiro');
        $(all).hide();

        $("input[name=tpInterno]").click(function() {
            $('#sqPessoaDestinatario').val('');
            $('#sqPessoaInteressado_hidden').val('');
            $('#sqPessoaInteressadoPf').val('');
            $('#sqPessoaInteressadoPf_hidden').val('');
            $('#sqPessoaInteressadoInterna_hidden').val('');
            $('#sqPessoaInteressadoInterna').val('');
            $('#nuCPFInteressado').val('');
            $('#nuCNPJInteressado').val('');

            $('#anexo').attr('checked', true);
            $('#anexo').parent('div').parent('div').removeClass('error');
            $('#anexo').parent('div').find('.help-block').remove();

            $('#sqPessoaInteressado').val('');
            $('#nuCPFInteressado').val('');
            if ($("#chekInterno").is(":checked"))
            {
                $('#sqTipoPessoaInteressado').val('');
                $(all).hide();
                $('#dvLabelNomeInteressadoInterna').text('Nome');
                $('#dvLabelCPFInteressado').text('CPF');
                $('#sqTipoPessoaInteressado').val('1');
                $('.divGeralNomeInteressadoInterna').show();
                $('.divGeralCPFInteressado').show();
            } else {
                $('#nuCPFInteressado').parent('div').parent('div').removeClass('error');
                $('#nuCPFInteressado').parent('div').find('.help-block').remove();
                $(all).hide();
                $('.tpInteressado').show();
                $('#sqTipoPessoaInteressado').val('0');

            }
        });

        switch ($('#sqTipoPessoaInteressado').val()) {
            case '1':
                $('#pessoaBrasilieira').trigger('click');
                $('.tpInteressado').show();
                $('.divGeralEstrangeiro').show();
                $('.divGeralNomeInteressadoPF').show();
                $('#divGeralNomeInteressadoPf').text('Nome');
                $('#dvLabelCPFInteressado').text('CPF');
                $('.divGeralCPFInteressado').show();
                $('.divGeralPassaporteInteressado').hide();
                $('.divGeralNomeInteressado').hide();
                $('.divMultiplaPF').show();
                break;
            case '2':
                $('.tpInteressado').show();
                $('.divGeralNomeInteressado').show();
                $('#dvLabelNomeInteressado').text('Razão Social');
                $('.divGeralCNPJInteressado').show();
                $('.divMultiplaPJInter').show();
                break;
            case '4':
                $('.tpInteressado').show();
                $('.divGeralNomeInteressado').show();
                $('.divGeralCPFInteressado').hide();
                $('#dvLabelNomeInteressado').html('<span class="required">* </span>Nome');
                $('.divMultiplaPJInter').hide();

                break;
            case '5':
                $('.tpInteressado').show();
                $('.divGeralNomeInteressado').show();
                $('.divGeralCPFInteressado').hide();
                $('#dvLabelNomeInteressado').html('<span class="required">* </span>Nome');
                $('.divMultiplaPJInter').hide();
                break;
        }

    },
    initModalInteressado: function() {

        ModalInteressado.tipoInteressado();
        $('#sqTipoPessoaInteressado').change(function() {
            $('#nuCPFInteressado').parent('div').parent('div').removeClass('error');
            $('#nuCPFInteressado').parent('div').find('.help-block').remove();
            $('#nuCNPJInteressado').parent('div').parent('div').removeClass('error');
            $('#nuCNPJInteressado').parent('div').find('.help-block').remove();
            if ($('#sqTipoPessoaInteressado').val() != '') {
                ModalInteressado.tipoInteressado();
            } else {
                $('.divGeralNomeInteressado').hide();
//                $('.divGeralNomeInteressadoPF').hide();
                $('.divGeralCPFInteressado').hide();
                $('.divGeralPassaporteInteressado').hide();
                $('.divGeralCNPJInteressado').hide();
            }
        });

        $("input[name=TipoNacionalidade]").click(function() {
            $('#sqPessoaInteressado_hidden').val('');
            $('#sqPessoaInteressado').val('');
            $('#nuCPFInteressado').val('');
            $('.divGeralNomeInteressado').hide();
            $('#dvLabelNomeInteressadoPf').text('Nome');
            $('.divGeralNomeInteressadoPF').show();
            if ($("#pessoaBrasilieira").is(":checked"))
            {
                $('#dvLabelCPFInteressado').text('CPF');
                $('.divGeralCPFInteressado').show();
                $('.divGeralPassaporteInteressado').hide();
            } else {
                $('.tpInteressado').show();
                $('#dvLabelCPFInteressado').text('Nº do Passaporte');
                $('.divGeralPassaporteInteressado').show();
                $('.divGeralCPFInteressado').hide();
            }

        });
        $('#sqPessoaInteressadoPf').simpleAutoComplete("/artefato/pessoa/search-pessoa/save/true/tp/" + $("input[name=TipoNacionalidade]:checked").val(), {
            extraParamFromInput: 'input[name=TipoNacionalidade]:checked',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
        $('#sqPessoaInteressadoInterna').simpleAutoComplete("/artefato/pessoa/search-pessoa-interna/", {
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
        $('#sqPessoaInteressado').simpleAutoComplete("/artefato/pessoa/search-pessoa/save/true", {
            extraParamFromInput: '#sqTipoPessoaInteressado',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    },
    //destinatario interno
    formInteressado: function(nuCpfCnpjPassaporte) {
        var sqPessoaInteressado = '';
        var noPessoa = '';

        if ($('#sqPessoaInteressado').val()) {
            sqPessoaInteressado = $('#sqPessoaInteressado_hidden').val();
            noPessoa = $('#sqPessoaInteressado').val()
        }
        if ($('#sqPessoaInteressadoPf').val()) {
            sqPessoaInteressado = $('#sqPessoaInteressadoPf_hidden').val();
            noPessoa = $('#sqPessoaInteressadoPf').val()
        }
        if ($('#sqPessoaInteressadoInterna').val()) {
            sqPessoaInteressado = $('#sqPessoaInteressadoInterna_hidden').val();
            noPessoa = $('#sqPessoaInteressadoInterna').val()
        }
        if ($("#chekInterno").is(":checked"))
        {
            $('#sqTipoPessoaInteressado').val('');
        }
        var arrDados = Interessado.getDados(sqPessoaInteressado,
                $('#sqPessoaFuncao').val(),
                nuCpfCnpjPassaporte,
                $('#sqTipoPessoaInteressado').val());

        if (arrDados == 'true') {
            Message.showAlert('O usuário informado já foi incluido.');
            return false;
        }

        if (arrDados == '[]') {
            Message.showAlert('Nenhum resultado encontrado!');
            return false;
        }
        if ($("#chekInterno").is(":checked"))
        {
            $('#sqTipoPessoaInteressado').val('1');
        }
        var j = $.parseJSON(arrDados);
        if (j.length > 0) {
            var numeroCpfCnpjPassaporte = '';
            if ($('#nuCPFInteressado').val()) {
                numeroCpfCnpjPassaporte = $('#nuCPFInteressado').val();
            }
            if ($('#nuCNPJInteressado').val()) {
                numeroCpfCnpjPassaporte = $('#nuCNPJInteressado').val();
            }
            if ($('#nuPassaporteInteressado').val()) {
                numeroCpfCnpjPassaporte = $('#nuPassaporteInteressado').val();
            }
            $.each(j, function(i) {

            });

            $.post(
                    '/artefato/minuta-eletronica/add-destinatario-artefato',
                    {
                        sqArtefato: $('#sqArtefato').val()
                        , sqPessoaFuncao: $('#sqPessoaFuncao').val()
                        , tipoPessoaAba: '2'
                        , noPessoa: noPessoa
                        , checkCorporativo: '1'
                        , sqTratamentoVocativo: '0'
                        , sqPessoaCorporativo: sqPessoaInteressado
                        , sqTipoPessoa: $('#sqTipoPessoaInteressado').val()
                        , nuCpfCnpjPassaporte: numeroCpfCnpjPassaporte
                        , sqNacionalidade: $("input[name=TipoNacionalidade]:checked").val(),
                        sqEnderecoSgdoce: ''
                    },
            function() {
                Message.showAlert('Operação realizada com sucesso.');
                Interessado.clearFields();
                Interessado.reloadGrid();
            }
            );

            return true;
        }
    },
    concluir: function() {
        $('.btnConcluirInteressado').click(function() {
            var numeroCpfCnpjPassaporte = '';
            if ($('#sqTipoPessoaInteressado').val() == 1) {
                numeroCpfCnpjPassaporte = $('#nuCPFInteressado').val();
            } else if ($('#sqTipoPessoaInteressado').val() == 2) {
                numeroCpfCnpjPassaporte = $('#nuCNPJInteressado').val();
            } else if ($('#sqTipoPessoaInteressado').val() == 3) {
                numeroCpfCnpjPassaporte = $('#nuPassaporteInteressado').val();
            }
            if (($('#sqPessoaInteressado').val() != '' || $('#sqPessoaInteressado').val() == '') && (numeroCpfCnpjPassaporte == '')) {
                $('#nuCPFInteressado').removeClass('cpf');
                $('#nuCNPJInteressado').removeClass('cnpj');
            }
            if ($('#form-interessado-modal').valid()) {
                var div = '<button class="close" data-dismiss="alert">×</button>' +
                        'Informe pelo menos um campo para realizar a pesquisa.';
                $('.campos-obrigatorios-modal').html(div).addClass('hidden').hide();
                var sqPessoaInteressado = '';
                if ($('#sqPessoaInteressadoPf').val()) {
                    sqPessoaInteressado = $('#sqPessoaInteressadoPf').val();
                }
                if ($('#sqPessoaInteressado').val()) {
                    sqPessoaInteressado = $('#sqPessoaInteressado').val();
                }
                if ($('#sqPessoaInteressadoInterna').val()) {
                    sqPessoaInteressado = $('#sqPessoaInteressadoInterna').val();
                }
                if ((sqPessoaInteressado == '') && (numeroCpfCnpjPassaporte == '')) {
                    Message.showAlert('Informe pelo menos um campo para realizar a pesquisa.');
                    return false;
                }
                return ModalInteressado.formInteressado(numeroCpfCnpjPassaporte);
            } else {
                $('.campos-obrigatorios').remove();
                return false;
            }

        });
    },
    init: function() {
        Interessado.clearFields();
        ModalInteressado.initModalInteressado();
        ModalInteressado.concluir();
        ModalInteressado.initResponsavelDestinatario();
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
}

Interessado = {
    deletar: function(sqArtefato, sqPessoaSgdoce, sqPessoaFuncao) {
        $.post('/artefato/pessoa/delete-interessado', {
            sqArtefato: sqArtefato,
            sqPessoaSgdoce: sqPessoaSgdoce,
            sqPessoaFuncao: sqPessoaFuncao
        }).done(function() {
            Interessado.reloadGrid();
        });

    },
    reloadGrid: function() {
        $('#table-interessado').dataTable().fnDraw(false);
    },
    clearFields: function() {
        $('#sqPessoaInteressado').val('');
        $('#nuCPFInteressado').val('');
        $('#sqTipoPessoaInteressado').val('');
        $("#chekInterno").attr("checked", false);
        $("#chekExterno").attr("checked", false);
        var all = $('.divGeralNomeInteressado,divGeralNomeInteressadoPF, .divGeralCPFInteressado , .tpInteressado, .divGeralCNPJInteressado, .divGeralPassaporteInteressado');
        $(all).hide();
    },
    getDados: function(pessoaCorporativo, pessoaFuncao, nuCPFInteressado) {
        var result = $.ajax({
            type: 'post',
            url: '/artefato/pessoa/valida-dados',
            data: 'sqTipoPessoaInteressado=' + $('#sqTipoPessoaInteressado').val() + '&sqPessoaCorporativo=' + pessoaCorporativo + '&sqPessoaFuncao=' + pessoaFuncao + '&nuCPFInteressado=' + nuCPFInteressado + '&sqArtefato=' + $('#sqArtefato').val(),
            async: false,
            global: false
        }).responseText;
        return result;
    }
}

$(document).ready(function() {
    $('#nuCPFInteressado').setMask('999.999.999-99');
    $('#nuCNPJInteressado').setMask('99.999.999/9999-99');

    ModalInteressado.init();
    ModalInteressado.initVisualizaPessoa();
    loadJs('js/components/modal.js', function() {
        Menu.init();
    }); // load cdn
});