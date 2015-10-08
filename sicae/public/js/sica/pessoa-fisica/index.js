PessoaFisica = {
    initAbas: function () {
        $('.buttons').click(function () {
            $('li a[href=' + $(this).attr('href') + ']').tab('show');

            return false;
        });

        $('ul.nav-tabs li a').click(function () {
            $('.alert-success').hide();
        });

        $('ul.nav-tabs li').click(function () {
            if ($('ul.nav-tabs li:first').hasClass('active')) {
                if ($('form').valid()) {
                    $('#aba').val($($(this)).index() + 1);
                    if (!$('#nuCpf').val() && !$('#sqPessoa').val()) {
                        $('#modal-justificativa').modal({backdrop: 'static', keyboard: false});
                        $('#txJustificativa').parent().parent().removeClass('error');
                        $('#txJustificativa').parent().find('p.help-block').remove();
                        $('#txJustificativa').val('');
                    } else {
                        $('#form-pessoa').submit();
                    }
                }
                return false;
            }
        });
        if (!$('#sqPessoa').val()) {
            $('.tab').click(function () {
                return false;
            });
        }
        if ($('#aba').val()) {
            $('ul.nav-tabs li:nth-child(' + $('#aba').val() + ') a').tab('show');
        }
        $('.btn-mini, .close').click(function () {
            $('.campos-obrigatorios').addClass('hidden');
        });
    },
    initNacionalidade: function () {
        $('.nacSim').hide();
        $('.nacNao').hide();

        $('#sqTipoPessoaNascSim').click(function () {
            $('#sqEstado, #sqMunicipio').val('').change();
            $('.nacSim').show();
            $('.nacNao').hide();
        });

        $('#sqTipoPessoaNascNao').click(function () {
            $('#sqPais').val('').change();

            $('.nacSim').hide();
            $('.nacNao').show();
        });

        if ($('#sqTipoPessoaNascSim').is(':checked')) {
            $('.nacSim').show();
        }

        if ($('#sqTipoPessoaNascNao').is(':checked')) {
            $('.nacNao').show();
        }
    },
    addValidationRequired: function () {
        loadJs('/assets/js/components/validation.js', function () {
            jQuery.validator.addMethod("requiredEdit", function (value, element) {

                if (value.length > 0) {
                    return true;
                } else {
                    return false;
                }

            }, "A informação do campo pode ser alterada, porém não pode ser eliminada.");
        });

    },
    initCpf: function () {
        if ($('#nuCpf').val() && $('#sqPessoa').val()) {
            $('#nuCpf').attr('readonly', true).setMask('cpf');
            PessoaForm.searchCpfCnpj = function () {
            };
        }

        if ($('#noPessoa').val() && $('#sqPessoa').val()) {
            $('#noPessoa').attr('readonly', true);
        }

        if ($('#dtNascimento').val() && $('#sqPessoa').val()) {
            $('#dtNascimento').addClass('requiredEdit');
        }

        if ($('#sqEstadoCivil').val() && $('#sqPessoa').val()) {
            $('#sqEstadoCivil').addClass('requiredEdit');
        }

        if ($('#noPai').val() && $('#sqPessoa').val()) {
            $('#noPai').addClass('requiredEdit');
        }

        if ($('#noMae').val() && $('#sqPessoa').val()) {
            $('#noMae').addClass('requiredEdit');
        }

        if ($('#dtIntegracaoInfoconv').val() != undefined && $('#dtIntegracaoInfoconv').val() != '')
        {
            if ($('#noPessoa').val() != '') {
                $('#noPessoa').attr('readonly', 'readonly');
            }
            if ($('#dtNascimento').val() != '') {
                $('#dtNascimento').attr('readonly', 'readonly');
            }

            if ($("input[name='nacionalidade'][checked='checked']").val() != undefined
                    && $("input[name='nacionalidade'][checked='checked']").val() == 2) {
                $("input[name='nacionalidade']").attr('readonly', 'readonly');
                if ($("#sqPais option[selected='selected']").text() != '') {
                    $('#sqPais').attr('readonly', 'readonly');
                }
            }
            if ($('#noMae').val() != '') {
                $('#noMae').attr('readonly', 'readonly');
            }
        }
    },
    integrationInfoconv: function () {
        if ($('#nuCpf').val() != undefined && $('#nuCpf').val() != '') {
            var yesCallback = function () {
                PessoaForm.getInformationInfoconv($('#nuCpf').val(), 'nuCpf');

                if (PessoaForm.elements.success == true) {
                    $('#groupTxJustificativaInfoconv').addClass('hide');
                    $('#sqPessoaAutoraInfoconv').attr('value', PessoaForm.elements.personId);
                    $('#dtIntegracaoInfoconv').attr('value', PessoaForm.elements.dtIntegracaoInfoconv);
                    $('#txJustificativaInfoconv').attr('value', '').attr('readonly', false);
                    if (PessoaForm.elements.noPessoa != '') {
                        $('#noPessoa').val(PessoaForm.elements.noPessoa).attr('readonly', false);
                        $('#noPessoa_hidden').val(PessoaForm.elements.noPessoa);
                    }
                    if (PessoaForm.elements.sgSexo != '') {
                        $('#sgSexo').val(PessoaForm.elements.sgSexo).attr('readonly', false);
                        $('#sgSexo option[selected!=selected]').remove();
                    } else {
                        $('#sgSexo').attr('readonly', true);
                    }
                    if (PessoaForm.elements.dtNascimento != '') {
                        $('#dtNascimento').val(PessoaForm.elements.dtNascimento).attr('readonly', false);
                    }
                    if (PessoaForm.elements.noMae != '') {
                        $('#noMae').val(PessoaForm.elements.noMae).attr('readonly', false);
                    } else {
                        $('#noMae').attr('readonly', true);
                    }

                    if (PessoaForm.elements.nacionalidade != undefined && PessoaForm.elements.nacionalidade != '') {
                        $("input[name='nacionalidade'][value=" + PessoaForm.elements.nacionalidade + "]").attr('checked', 'checked');
                        if (PessoaForm.elements.nacionalidade == 1) {
                            $('#sqTipoPessoaNascSim').trigger('click');

                            $('#sqEstado').attr('readonly', 'readonly');
                            $('#sqMunicipio').attr('readonly', 'readonly');
                        } else if (PessoaForm.elements.nacionalidade == 2) {
                            $('#sqTipoPessoaNascNao').trigger('click');
                            $('#sqPais').val(PessoaForm.elements.sqPaisNaturalidade);
                        }
                    } else {
                        $("input[name='nacionalidade']").attr('readonly', 'readonly');
                        $('#sqEstado').attr('readonly', 'readonly');
                        $('#sqMunicipio').attr('readonly', 'readonly');
                    }

                    if ($('#sqPessoa').val() != undefined && !!$('#sqPessoa').val()) {
                        var arrAlteracoes = [];
                        if (PessoaForm.elements.noPessoa != '') {
                            arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                                    'Nome',
                                                    (PessoaForm.elements.noPessoaOld) ? PessoaForm.elements.noPessoaOld : PessoaForm.withoutValue,
                                                    PessoaForm.elements.noPessoa
                                            ));
                        }
                        if (PessoaForm.elements.dtNascimento != '') {
                            arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                                    'Data de Nascimento',
                                                    ((PessoaForm.elements.dtNascimentoOld != '') ? PessoaForm.elements.dtNascimentoOld : PessoaForm.withoutValue),
                                                    PessoaForm.elements.dtNascimento
                                            ));
                        }
                        if (PessoaForm.elements.nacionalidade != undefined && PessoaForm.elements.nacionalidade == 2) {
                            arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                                    'Nacionalidade',
                                                    (PessoaForm.elements.nacionalidadeOld) ? PessoaForm.elements.nacionalidadeOld : PessoaForm.withoutValue,
                                                    ((PessoaForm.elements.nacionalidade == 2) ? 'Não' : 'Sim')
                                            ));
                            arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                                    'País de Origem',
                                                    (PessoaForm.elements.noPaisNaturalidadeOld) ? PessoaForm.elements.noPaisNaturalidadeOld: PessoaForm.withoutValue,
                                                    PessoaForm.elements.noPaisNaturalidade
                                            ));
                        }
                        if (PessoaForm.elements.sgSexo == "M" || PessoaForm.elements.sgSexo == "F") {
                            arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                                    'Sexo',
                                                    (PessoaForm.elements.sgSexoOld) ? PessoaForm.elements.sgSexoOld : PessoaForm.withoutValue,
                                                    ((PessoaForm.elements.sgSexo == 'M') ? 'Masculino' : 'Feminino')
                                            ));
                        }
                        if (PessoaForm.elements.noMae != '') {
                            arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                                    'Nome da Mãe',
                                                    (PessoaForm.elements.noMaeOld) ? PessoaForm.elements.noMaeOld : PessoaForm.withoutValue,
                                                    PessoaForm.elements.noMae
                                            ));
                        }

                        Message.show('Alterações realizadas', arrAlteracoes.join('<br />'), function () {
                            $('#btnSalvar').trigger('click');
                        });
                        $('.modal-header:visible > a.close').hide();
                    } else {
                        $('#btnSalvar').trigger('click');
                    }
                } else {
                    if ($('#sqPessoa').val() == '') {
                        $('#nuCpf').val('');
                    }
                    Message.showAlert(PessoaForm.elements.response);
                }
            };
            var noCallback = function () {
                if (!$('#sqPessoa').val()) {
                    $('#nuCpf').val('');
                }
            };

            if($('#sqPessoa').val()){
                var msg = MessageUI.translate('MN194');
            }else{
                var msg = MessageUI.translate('MN195');
            }

            Message.showConfirmation({
                'body': msg,
                'yesCallback': yesCallback,
                'noCallback': noCallback
            });
        } else {
            Message.showAlert('Informar um CPF');
        }
        ;
    },
    setElementIntegrationInfoconv: function (result)
    {
        if ($('#sqPessoa').val() != undefined && $('#sqPessoa').val() != '') {
            if (result.noPessoa != '') {
                PessoaForm.elements.noPessoaOld = $('#noPessoa').val();
            }
            if (result.sgSexo != '') {
                PessoaForm.elements.sgSexoOld = $("#sgSexo option[selected='selected']").text();
            }
            if (result.dtNascimento != '') {
                PessoaForm.elements.dtNascimentoOld = $("#dtNascimento").val();
            }
            if (result.noMae != '') {
                PessoaForm.elements.noMaeOld = $('#noMae').val();
            }
            if (result.nacionalidade != '') {
                PessoaForm.elements.nacionalidadeOld = $("input[name='nacionalidade'][checked='checked']").parent().text().trim();
                if (result.sqPaisNaturalidade != '' && result.nacionalidade == 2) {
                    PessoaForm.elements.noPaisNaturalidadeOld = $("#sqPais option[selected='selected']").text();
                    PessoaForm.elements.noPaisNaturalidade = result.noPaisNaturalidade;
                }
            }
        }

        PessoaForm.elements.noPessoa = result.noPessoa;
        PessoaForm.elements.sgSexo = result.sgSexo;
        PessoaForm.elements.dtNascimento = result.dtNascimento;
        PessoaForm.elements.noMae = result.noMae;
        //
        PessoaForm.elements.nacionalidade = result.nacionalidade;
        PessoaForm.elements.sqPaisNaturalidade = result.sqPaisNaturalidade;
        //
        PessoaForm.elements.dtIntegracaoInfoconv = result.dtIntegracaoInfoconv;
        PessoaForm.elements.type = 'nuCpf';
    },
    btnIntegrationInfoconv: function ()
    {
        $('#btnIntegrationInfoconv').live('click', function () {
            PessoaFisica.integrationInfoconv();
        });
    },
    init: function () {
        PessoaFisica.addValidationRequired();
        PessoaFisica.initAbas();
        PessoaFisica.initNacionalidade();
        PessoaFisica.initCpf();
        PessoaForm.searchCpfCnpj($('#nuCpf'));
        PessoaFisica.btnIntegrationInfoconv();
    }
};

$(document).ready(function () {
    PessoaFisica.init();
});