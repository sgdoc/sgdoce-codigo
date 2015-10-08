var Interessado = {
    keyInteressado: 0,
    _msgDuplicidade: 'Item já incluído na lista.',
    interessadoAutoComplete: function () {
        //autocomplete para as unidade organizacionais do ICMBio
        $('#unidIcmbio').simpleAutoComplete("/artefato/pessoa/autocomplete/extraParam/4", {
            extraParamFromInput: '4',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        // autocomplete para os funcionario do ICMBio
        $('#funcIcmbio').simpleAutoComplete("/artefato/pessoa/autocomplete/extraParam/1", {
            extraParamFromInput: '1',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    },
    zeraComando: function () {
        $('#sqPessoaInteressado').val('');
        $('#sqPessoaInteressado').removeAttr('autocomplete');
        $('#sqPessoaInteressado').removeAttr('name');
        $('#sqPessoaInteressado').attr('name', 'sqPessoaInteressado');
        $('#sqPessoaInteressado_hidden').remove();
    },
    setaAutocompletePessoa: function (vl) {
        Interessado.zeraComando();
        if (vl == 1) {
            $('#sqPessoaInteressado').simpleAutoComplete("/artefato/pessoa/search-pessoa/extraParam/1/save/true", {
                extraParamFromInput: "1",
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function (data) {
                Interessado.searchDadosPessoa();
            });
        }

        if (vl == 2) {
            $('#sqPessoaInteressado').simpleAutoComplete("/artefato/pessoa/search-pessoa/extraParam/0", {
                extraParamFromInput: "0",
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function (data) {
                Interessado.searchDadosPessoa();
            });
        }

        if (vl == 3) {
            $('#sqPessoaInteressado').simpleAutoComplete("/artefato/pessoa/search-pessoa/extraParam/2/save/true", {
                extraParamFromInput: "2",
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function (data) {
                Interessado.searchDadosPessoa();
            });
        }

        if (vl == 4) {
            $('#sqPessoaInteressado').simpleAutoComplete("/artefato/pessoa/search-pessoa/extraParam/4", {
                extraParamFromInput: "4",
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function (data) {
                Interessado.searchDadosPessoa();
            });
        }
        if (vl == 5) {
            $('#sqPessoaInteressado').simpleAutoComplete("/artefato/pessoa/search-pessoa/extraParam/5", {
                extraParamFromInput: "5",
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function (data) {
                Interessado.searchDadosPessoa();
            });
        }
        if (vl == 6) {
            $('#sqPessoaInteressado').simpleAutoComplete("/artefato/pessoa/search-pessoa/extraParam/3", {
                extraParamFromInput: "3",
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function (data) {
                Interessado.searchDadosPessoa();
            });
        }
    },
    interessadoModal: function () {
        $('#btnAdicionarInteressado').click(function () {
            $(".modal-backdrop, #modal-interessado").show();
            $("#modal-interessado").load('/artefato/artefato/modal-interessado/sqArtefato/' + $('#sqArtefato').val()).modal();
        });
    },
    obrigatoriedade: function () {
        $('#unidFuncionario').val('');
        $('#sqTipoPessoaInteressado').val('');
        if ($("input[name='tpInternoExterno']:checked").val() == 'interno') {
            $('#unidFuncionario').addClass('required');
            $('#sqTipoPessoaInteressado').removeClass('required');
        }

        if ($("input[name='tpInternoExterno']:checked").val() == 'externo') {
            $('#sqTipoPessoaInteressado').addClass('required');
            $('#unidFuncionario').removeClass('required');
            $('#unidIcmbio, #funcIcmbio').removeClass('required');
        }
    },
    interessadoFuncoes: function () {

        $('#nuPassaporte').setMask();

        $("input[name='tpInternoExterno']").addClass('required');

        $("input[name=tpInternoExterno]").click(function () {
            $('.noPessoa,.nuCnpj,.nuCpf,#divNacionalidade,.unidade,.funcionario').addClass('hidden');

            Interessado.obrigatoriedade();
            if ($(this).val() == 'interno') {
                $('.interno').removeClass('hidden');
                $('.externo,.div-externo').addClass('hidden');
                return true;
            } else {
                $("#unidIcmbio, #unidIcmbio_hidden").val('');
                $("#funcIcmbio, #funcIcmbio_hidden").val('');
            }

            $('.externo').removeClass('hidden');
            $('.interno').addClass('hidden');
        });

        // evento de tela ao selecionar a opçao de funcionario
        $("#unidFuncionario").change(function () {

            $('#unidIcmbio, #funcIcmbio').addClass('required');
            $("#unidIcmbio, #unidIcmbio_hidden").val('');
            $("#funcIcmbio, #funcIcmbio_hidden").val('');

            $('.funcionario').removeClass('hidden');
            $('.unidade').addClass('hidden');

            if ($(this).val() == '') {
                $('.funcionario').addClass('hidden');
                $('.unidade').addClass('hidden');
            }
            if ($(this).val() == 'unidade') {
                $('#unidIcmbio').addClass('required');
                $("#funcIcmbio, #funcIcmbio_hidden").val('');
                $('.unidade').removeClass('hidden');
                $('.funcionario').addClass('hidden');
                return true;
            }

            if ($(this).val() == 'funcionario') {
                $('#funcIcmbio').addClass('required');
                return true;
            }
        });

        //acao ao clicar no botao de cancelar na modal
        $(".btnCancelarInteressado").click(function () {
            $("input[name=tpInternoExterno]").removeAttr('checked');
            $('.externo, .interno, .funcionario, .unidade').addClass('hidden');
        });

        $("input[name=tpNacionalidadeInteressado]").click(function () {
            $("#extraParam").val($("input[name=tpNacionalidadeInteressado]:checked").val());
            $('#sqPessoaInteressado').val('');
            $('#nuCpf').val('');

            if ($("input[name='tpNacionalidadeInteressado']:checked").val() == '0') {
                $("#extraParam").val(Pessoa.Estrangeiro);
                $('#nuPassaporte').addClass('required');
                $('.div-externo').addClass('hidden');
                $('.noPessoa').removeClass('hidden');
                $('.nuPassaporte').removeClass('hidden');
                Interessado.setaAutocompletePessoa(6);
            } else {
                $("#extraParam").val($("input[name=tpNacionalidadeInteressado]:checked").val());
                $('#divNacionalidade').removeClass('hidden');
                $('.div-externo').addClass('hidden');
                $('.noPessoa').removeClass('hidden');
                $('.nuCpf').removeClass('hidden');
                Interessado.setaAutocompletePessoa(1);
            }
        });

        $("#sqTipoPessoaInteressado").change(function () {

            $('#divNacionalidade').addClass('hidden');
            $('#sqPessoaInteressado').addClass('required');

            switch ($(this).val()) {
                case '1' :
                case '3' :
                    if ($("input[name='tpNacionalidadeInteressado']:checked").val() == '0') {
                        $("#extraParam").val(Pessoa.Estrangeiro);
                        $('#nuPassaporte').addClass('required');
                        $('.div-externo').addClass('hidden');
                        $('.noPessoa').removeClass('hidden');
                        $('.nuPassaporte').removeClass('hidden');
                        Interessado.setaAutocompletePessoa(2);
                    } else {
                        $("#extraParam").val($("input[name=tpNacionalidadeInteressado]:checked").val());
                        $('#divNacionalidade').removeClass('hidden');
                        $('.div-externo').addClass('hidden');
                        $('.noPessoa').removeClass('hidden');
                        $('.nuCpf').removeClass('hidden');
                        Interessado.setaAutocompletePessoa(1);
                    }

                    $('#nuCnpj').val('');

                    $('.btnCadastraPessoa,.liPessoaFisica').show();
                    $('#divNacionalidade').show().removeClass('hidden');
                    $('.liPessoaJuridica').hide();
                    $('#dvLabelNomeInteressado').html('<span class="required">* </span>Nome');
                    break;
                case '2' :
                    $("#extraParam").val(Pessoa.PessoaJuridica);
                    $('#nuCnpj').addClass('required');
                    $('.div-externo').addClass('hidden');
                    $('.noPessoa').removeClass('hidden');
                    $('.nuCnpj').removeClass('hidden');
                    $('#dvLabelNomeInteressado').html('<span class="required">* </span>Razão Social');
                    Interessado.setaAutocompletePessoa(3);

                    $('.btnCadastraPessoa,.liPessoaJuridica').show();
                    $('.liPessoaFisica').hide();

                    $('#nuCpf,#nuPassaporte').val('');

                    $('#divNacionalidade').hide();

                    break;
                case '5' :
                    $("#extraParam").val(Pessoa.UnidadeOrganizacionalExterna);
                    $('#nuPassaporte').addClass('required');
                    $('.div-externo').addClass('hidden');
                    $('.noPessoa').removeClass('hidden');
                    $('#dvLabelNomeInteressado').html('<span class="required">* </span>Nome');
                    Interessado.setaAutocompletePessoa(5);

                    $('.btnCadastraPessoa').hide();
                    break;
                default :
                    $('.noPessoa,.nuCpf, .nuCnpj, .nuPassaporte').addClass('hidden');
                    break;
            }
        });

        $(".btnConcluirInteressado").click(function (e) {

            var nuDocumento = '';

            if ($("input[name=tpInternoExterno]:checked").val() == 'externo') {
                switch ($("#sqTipoPessoaInteressado").val()) {
                    case Pessoa.PessoaFisica :
                        if ($("input[name='tpNacionalidadeInteressado']:checked").val() == '1') {
                            nuDocumento = $('#nuCpf').val();
                        }
                        if ($("input[name='tpNacionalidadeInteressado']:checked").val() == '0') {
                            nuDocumento = $('#nuPassaporte').val();
                        }
                        break;
                    case Pessoa.PessoaJuridica :
                        nuDocumento = $('#nuCnpj').val();
                        break;
                    case Pessoa.Estrangeiro :
                        nuDocumento = $('#nuPassaporte').val();
                        break;
                }
            }

            if (!$('#form-interessado-modal').valid()) {
                $('.campos-obrigatorios').remove();
                e.preventDefault();
            }else{
                var noPessoa = '';
                var sqPessoa = 0;
                if ($('#sqPessoaInteressado').val() == '' && $('#unidIcmbio').val() != '' && $('#funcIcmbio').val() == '') {
                    noPessoa = $('#unidIcmbio').val();
                    sqPessoa = $('#unidIcmbio_hidden').val();
                } else if ($('#sqPessoaInteressado').val() == '' && $('#unidIcmbio').val() == '' && $('#funcIcmbio').val() != '') {
                    noPessoa = $('#funcIcmbio').val();
                    sqPessoa = $('#funcIcmbio_hidden').val();
                } else {
                    noPessoa = $('#sqPessoaInteressado').val();
                    sqPessoa = $('#sqPessoaInteressado_hidden').val();
                }

                var dataPost = {
                    sqArtefato: (parseInt($('#sqArtefato').val())) ? parseInt($('#sqArtefato').val()) : null,
                    sqPessoaFuncao: $('#sqPessoaFuncao').val(),
                    unidFuncionario: $('#unidFuncionario').val(),
                    noPessoa: noPessoa,
                    sqPessoaCorporativo: sqPessoa,
                    sqTipoPessoa: $('#sqTipoPessoaInteressado').val()
                };
                if (dataPost.sqArtefato) {
                    $.post('/artefato/pessoa/add-interessado', dataPost, function (data) {
                        if (data.sucess == 'true') {
                            Message.showSuccess(UI_MSG['MN013']);
                            Interessado.reloadGrid();
                        } else if (data.sucess == 'false') {
                            Message.showAlert(Interessado._msgDuplicidade);
                            $(".bootbox .btn").click(function () {
                                $("#modal-interessado").show();
                            });
                            return false;
                        }
                    });
                } else {
                    dataPost.nuDocumento = nuDocumento;
                    if (!Interessado.populateTable(dataPost)) {
                        return false;
                    }else{
                        Message.showSuccess(UI_MSG.MN013);
                    }
                }

                $("input[name=tpInternoExterno]").removeAttr('checked');
                $('.externo, .interno, .funcionario, .unidade').addClass('hidden');

                $("#modal-interessado").modal('hide');
            }
        });

        $('#nuCpf, #nuCnpj, #nuPassaporte').blur(function () {
            var elem = $(this);
            var clearName = function () {
                $('#sqPessoaInteressado,#sqPessoaInteressado_hidden').val('');
            };
            clearName();

            if (elem.val() == '') {
                return false;
            }
            var go = false;
            switch (true) {
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
                var label = $(this).parent().parent().find('.control-label').text().trim().replace('* ', '');
                var callback = function (label) {
                    if ($('#sqPessoaInteressado').val() == '') {
                        Message.showAlert(sprintf(UI_MSG.MN128, label));
                    }
                };

                Interessado.searchDadosPessoa(function () {
                    callback(label)
                });
            } else {
                return false;
            }

        });
        $('#sqPessoaInteressado').keyup(function () {
            $('#nuCpf, #nuCnpj, #nuPassaporte').val('');
        });
    },
    populateTable: function (data) {
        var table = $('.tableInteressado')
                , tbody = table.find('tbody')
                , go = true;

        /*verifica duplicidade na grid */

        tbody.find('.hdn_sqPessoaCorporativo').each(function (i) {
            if ($(this).val() == data.sqPessoaCorporativo) {
                go = false;
                Message.showAlert(Interessado._msgDuplicidade);
                return false;
            }
        });

        if (!go) {
            return false;
        }

        var newTr = $('<tr />')
                , tdName = $('<td />', {text: data.noPessoa}).appendTo(newTr)
                , tdDoc = $('<td />', {text: data.nuDocumento}).appendTo(newTr)
                , tdAction = $('<td />').appendTo(newTr)
                , btnAction = $('<button />', {type: 'button', class: 'btn btn-mini btnExcluirInteressado', title: 'Excluir'});

        btnAction.click(function () {
            var btn = $(this);
            Message.showConfirmation({
                body: UI_MSG.MN018,
                yesCallback: function () {
                    btn.parents('tr').remove();
                    if (tbody.find('tr').length === 1) {
                        tbody.find('tr.mensagemInteressado').show();
                    }
                }
            });
        });
        $('<i />', {class: 'icon-trash'}).appendTo(btnAction);

        btnAction.appendTo(tdAction);

        Interessado.keyInteressado++;

        var configPessoaCorporativo = {
            type: 'hidden',
            name: 'dataInteressado[' + Interessado.keyInteressado + '][sqPessoaCorporativo]',
            value: data.sqPessoaCorporativo,
            class: 'hdn_sqPessoaCorporativo'
        };

        $('<input />', configPessoaCorporativo).appendTo(tdDoc);
        $('<input />', {type: 'hidden', name: 'dataInteressado[' + Interessado.keyInteressado + '][sqTipoPessoa]', value: data.sqTipoPessoa}).appendTo(tdDoc);
        $('<input />', {type: 'hidden', name: 'dataInteressado[' + Interessado.keyInteressado + '][noPessoa]', value: data.noPessoa}).appendTo(tdName);
        $('<input />', {type: 'hidden', name: 'dataInteressado[' + Interessado.keyInteressado + '][sqPessoaFuncao]', value: data.sqPessoaFuncao}).appendTo(tdName);
        $('<input />', {type: 'hidden', name: 'dataInteressado[' + Interessado.keyInteressado + '][unidFuncionario]', value: data.unidFuncionario}).appendTo(tdName);

        tbody.find('tr.mensagemInteressado').hide();
        tbody.append(newTr);
        return true;
    },
    searchDadosPessoa: function (callback) {
        callback = callback || function () {
        };
        var tipoPessoa = $("#sqTipoPessoaInteressado").val();
        var nacionalidade = $("input[name=tpNacionalidadeInteressado]:checked").val();
        var nuCpfCnpjPassaporte = $('#nuCnpj').val();
        if (tipoPessoa == '1') {
            nuCpfCnpjPassaporte = nacionalidade == 1 ? $('#nuCpf').val() : $('#nuPassaporte').val();
        }

        if ($('#sqPessoaInteressado_hidden').val() || nuCpfCnpjPassaporte != '') {
            var params = {
                    sqPessoaCorporativo: $('#sqPessoaInteressado_hidden').val(),
                    sqTipoPessoa: tipoPessoa,
                    sqNacionalidade: nacionalidade,
                    nuCpfCnpjPassaporte: nuCpfCnpjPassaporte
            };
            $.post('/artefato/pessoa/get-dados-pessoa', params, function (data) {
                Interessado.setaValorDocumento(data, tipoPessoa, callback);
            });
        }

        if ($('#nuCnpj').val()) {
            $.post('/artefato/pessoa/recupera-pessoa-juridica-por-cnpj', {
                nuCnpj: $('#nuCnpj').val(),
                tipoPessoa: tipoPessoa,
                sqNacionalidade: tipoPessoa
            }, function (data) {
                $('#sqPessoaInteressado_hidden').val(data.sqPessoa);
                $('#sqPessoaInteressado').val(data.noPessoa);
            });
        }
    },
    setaValorDocumento: function (doc, tipoPessoa, callback) {
        callback = callback || function () {
        };
        if ($('#sqPessoaInteressado').val() != '') {
            switch (tipoPessoa) {
                case '1':
                    if ($("input[name=tpNacionalidadeInteressado]:checked").val() == '0') {
                        $('#nuPassaporte').val(doc.nuPassaporte);
                    } else {
                        $('#nuCpf').val(Interessado.cpfCnpj(doc.nuCpf));
                    }
                    break;
                case '2':
                    $('#nuCnpj').val(Interessado.cpfCnpj(doc.nuCnpj));
                    break;
            }
        }

        if ($('#sqPessoaInteressado').val() == '') {
            $('#sqPessoaInteressado').val(doc.noPessoa);
            $('#sqPessoaInteressado_hidden').val(doc.sqPessoa);
        }
        callback();
    },
    grid: function () {
        var url = '/artefato/processo-eletronico/list-interessados/sqArtefato/';
        Grid.load(url + $('#sqArtefato').val(), $('#table-interessado'));
    },
    deletar: function (sqArtefato, sqPessoaSgdoce) {
        var callBack = function () {
            $.get('/artefato/pessoa/delete-interessado/sqArtefato/' + sqArtefato + '/sqPessoaSgdoce/' + sqPessoaSgdoce, function () {
                Message.showSuccess(UI_MSG['MN013']);
            }).done(function () {
                Interessado.reloadGrid();
            });
        }
        Message.showConfirmation({
            'body': UI_MSG['MN018'],
            'yesCallback': callBack
        });
    },
    reloadGrid: function () {
        $('#table-interessado').dataTable().fnDraw(false);
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
