PessoaJuridica = {
    initAbas: function() {
        $('.buttons').click(function() {
            if ($(this).attr('href') == '#documento') {
                $('li a[href=#pessoa-vinculo]').tab('show');
            } else {
                $('li a[href=' + $(this).attr('href') + ']').tab('show');
            }
            return false;
        });

        $('ul.nav-tabs li a').click(function() {
            $('.alert-success').hide();
        });

        $('ul.nav-tabs li').click(function() {
            if ($('ul.nav-tabs li:first').hasClass('active')) {
                if ($('form').valid()) {
                    $('#aba').val($($(this)).index() + 1);
                    $('#form-pessoa').submit();
                }
                return false;
            }
        });

        if (!$('#sqPessoa').val()) {
            $('.tab').click(function() {
                return false;
            });
        }

        if ($('#aba').val()) {
            $('ul.nav-tabs li:nth-child(' + $('#aba').val() + ') a').tab('show');
        }

        $('.btn-mini, .close').click(function() {
            $('.campos-obrigatorios').addClass('hidden');
        });

        $('#btnProximo, #btnSalvar, #btnConcluir').click(function() {
            $('.alert').hide();

            if ($(this).attr('id') == 'btnProximo') {
                $('#aba').val('2');
            }

            if ($(this).attr('id') == 'btnSalvar') {
                $('#aba').val('1');
            }

            if ($(this).attr('id') == 'btnConcluir') {
                $('#aba').val('');
            }

            if ($('#form-pessoa').valid()) {
                $('#form-pessoa').submit();
            }
        });
    },
    addValidationRequired: function() {
        loadJs('/assets/js/components/validation.js', function() {
            jQuery.validator.addMethod("requiredEdit", function(value, element) {

                if (value.length > 0) {
                    return true;
                } else {
                    return false;
                }

            }, "A informação do campo pode ser alterada, porém não pode ser eliminada.");
        });

    },
    initCnpj: function() {
        if ($('#nuCnpj').val() && $('#sqPessoa').val()) {
            $('#nuCnpj').attr('readonly', true).setMask('cnpj');
            PessoaForm.searchCpfCnpj = function() {
            };
        }

        if ($('#noPessoa').val() && $('#sqPessoa').val()) {
            $('#noPessoa').attr('readonly', true);
        }

        $('#nuCnpj').setMask('cnpj');

        $('#btn-add-endereco, #btn-add-email, #btn-add-telefone').click(function() {
            $(document).ajaxStop(function() {
                // endereco
                $('#sqTipoEndereco option[value=1]').remove();
                PessoaForm.validateType('#modal-endereco', '#sqTipoEndereco', 'endereço');

                // telefone
                $('#sqTipoTelefone option[value=1]').remove();
                PessoaForm.validateType('#modal-telefone', '#sqTipoTelefone', 'telefone');

                // email
                $('#sqTipoEmail option[value=1]').remove();
                PessoaForm.validateType('#modal-email', '#sqTipoEmail', 'e-mail');
            });
        });

        if ($('#inTipoEstabelecimento').val() && $('#sqPessoa').val()) {
            $('#inTipoEstabelecimento').addClass('requiredEdit');
        }

        if ($('#noFantasia').val() && $('#sqPessoa').val()) {
            $('#noFantasia').addClass('requiredEdit');
        }

        if ($('#dtIntegracaoInfoconv').val() != undefined && $('#dtIntegracaoInfoconv').val() != '') {
            if($('#noPessoa').val() != '' ) {
                $('#noPessoa').attr('readonly', 'readonly');
            }
            if($('#noFantasia').val() != '' ) {
                $('#noFantasia').attr('readonly', 'readonly');
            }
        }
    },
    initNaturezaJuridica: function() {
        $('#sqNaturezaJuridicaPai').change(function() {
            if (parseInt($(this).val()) > 0) {
                $.get("/principal/pessoa-juridica/find-natureza-juridica", {
                    sqNaturezaJuridicaPai: $('#sqNaturezaJuridicaPai').val()
                }, function(data) {
                    $('#sqNaturezaJuridica').html(data);
                });
            } else {
                $('#sqNaturezaJuridica').html('<option value="">Selecione uma opção</option>');
                $('#sqNaturezaJuridica').val('').change();
                $('#sqNaturezaJuridica option').not("#sqNaturezaJuridica option:first").remove();
            }
        });
    },
    integrationInfoconv: function() {
        if( $('#nuCnpj').val() != undefined && !!$('#nuCnpj').val()) {
            var yesCallback = function() {
                PessoaForm.getInformationInfoconv( $('#nuCnpj').val(), 'nuCnpj' );
                if( PessoaForm.elements.success == true ) {
                    $('#groupTxJustificativaInfoconv').addClass('hide');
                    $('#sqPessoaAutoraInfoconv'      ).attr('value', PessoaForm.elements.personId);
                    $('#dtIntegracaoInfoconv'        ).attr('value', PessoaForm.elements.dtIntegracaoInfoconv);
                    $('#txJustificativaInfoconv'     ).attr('value', '').attr('readonly', false);

                    if(PessoaForm.elements.noPessoa != '') {
                        $('#noPessoa'       ).val( PessoaForm.elements.noPessoa ).attr('readonly', false);
                        $('#noPessoa_hidden').val( PessoaForm.elements.noPessoa );
                    }

                    $('#noFantasia'           ).val( PessoaForm.elements.noFantasia ).attr('readonly', false);
                    $('#sqNaturezaJuridicaPai').val( PessoaForm.elements.sqNaturezaJuridicaPai );
                    $('#sqNaturezaJuridicaPai').trigger('change').attr('readonly', false);

                    var setNaturezaJuridica = setInterval(function() {
                        if( !$('#sqNaturezaJuridica').val()) {
                            $('#sqNaturezaJuridica').val( PessoaForm.elements.sqNaturezaJuridica ).attr('readonly', false);
                        } else {
                            clearInterval(setNaturezaJuridica);
                        };
                    }, 1000);

                    $('#inTipoEstabelecimento').val( PessoaForm.elements.inTipoEstabelecimento ).attr('readonly', false);

                    //só mostra modal de/para caso seja alteração
                    if ($('#sqPessoa').val() != undefined && !!$('#sqPessoa').val()) {
                        PessoaJuridica.modalDepara();
                    } else {
                        //fica esperando o retorno do ajax da natureza juridica para salvar
                        var setSaveNewPJ = setInterval(function() {
                            if( $('#sqNaturezaJuridica').val() ) {
                                clearInterval(setSaveNewPJ);
                                $('#btnSalvar').trigger('click');
                            };
                        }, 1000);
                    };
                } else {
                    if (!$('#sqPessoa').val()) {
                        $('#nuCnpj').val('');
                    }
                    Message.showAlert(PessoaForm.elements.response);
                };
            };
            var noCallback = function() {
                if (!$('#sqPessoa').val()) {
                    $('#nuCnpj').val('');
                };
            };

            if($('#sqPessoa').val()) {
                var msg = MessageUI.translate('MN192');
            } else {
                var msg = MessageUI.translate('MN193');
            }

            Message.showConfirmation({
                'body': msg,
                'yesCallback': yesCallback,
                'noCallback': noCallback
            });
        } else {
            Message.showAlert('Informar um CNPJ');
        };
    },
    setElementIntegrationInfoconv: function( result ) {

        if($('#sqPessoa').val() != undefined && !!$('#sqPessoa').val()) {
            if(result.noPessoa != '') {
                PessoaForm.elements.noPessoaOld = $('#noPessoa').val();
            }
            if(result.noFantasia != '') {
                PessoaForm.elements.noFantasiaOld = $('#noFantasia').val();
            }
            if(result.inTipoEstabelecimento != '') {
                PessoaForm.elements.inTipoEstabelecimentoOld = $("#inTipoEstabelecimento option:selected").text();
            }
            if(result.sqNaturezaJuridicaPai != '') {
                PessoaForm.elements.sqNaturezaJuridicaPaiOld = $('#sqNaturezaJuridicaPai option:selected').text();
            }
            if(result.sqNaturezaJuridica != '') {
                PessoaForm.elements.sqNaturezaJuridicaOld = $('#sqNaturezaJuridica option:selected').text();
            }
            $('#address_infoconv').val('');
            $('#phone_infoconv'  ).val('');
            $('#email_infoconv'  ).val('');
        } else {
            $('#address_infoconv').val(JSON.stringify(result.address));
            $('#phone_infoconv'  ).val(JSON.stringify(result.phone));
            $('#email_infoconv'  ).val(JSON.stringify(result.txEmail));
        }

        PessoaForm.elements.noPessoa              = result.noPessoa;
        PessoaForm.elements.noFantasia            = result.noFantasia;
        PessoaForm.elements.inTipoEstabelecimento = result.inTipoEstabelecimento;
        PessoaForm.elements.sqNaturezaJuridicaPai = result.sqNaturezaJuridicaPai;
        PessoaForm.elements.sqNaturezaJuridica    = result.sqNaturezaJuridica;
        PessoaForm.elements.dtIntegracaoInfoconv  = result.dtIntegracaoInfoconv;
        PessoaForm.elements.type                  = 'nuCnpj';
    },

    btnIntegrationInfoconv: function(){
        $('#btnIntegrationInfoconv').live('click', PessoaJuridica.integrationInfoconv);
    },

    modalDepara: function() {
        setTimeout(function() {
            var arrAlteracoes = [];
            if(PessoaForm.elements.noPessoa != '') {
                arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                    'Nome',
                                    ((PessoaForm.elements.noPessoaOld) ? PessoaForm.elements.noPessoaOld : PessoaForm.withoutValue),
                                    PessoaForm.elements.noPessoa));
            }
            if(PessoaForm.elements.noFantasia != '') {
                arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                    'Nome Fantasia',
                                    ((PessoaForm.elements.noFantasiaOld) ? PessoaForm.elements.noFantasiaOld : PessoaForm.withoutValue),
                                    PessoaForm.elements.noFantasia));
            }
            if(PessoaForm.elements.sqNaturezaJuridica) {
                arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                    'Natureza Jurídica',
                                    ((PessoaForm.elements.sqNaturezaJuridicaPaiOld != 'Selecione uma opção') ? PessoaForm.elements.sqNaturezaJuridicaPaiOld : PessoaForm.withoutValue),
                                    $('#sqNaturezaJuridicaPai option:selected').text()));
                arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                    'Classificação',
                                    ((PessoaForm.elements.sqNaturezaJuridicaOld != 'Selecione uma opção') ? PessoaForm.elements.sqNaturezaJuridicaOld : PessoaForm.withoutValue),
                                    $('#sqNaturezaJuridica option:selected').text()));

            }
            if(PessoaForm.elements.inTipoEstabelecimento) {
                arrAlteracoes.push(PessoaForm.createItemDeParaInfoconv(
                                    'Tipo de Estabelecimento',
                                    ((PessoaForm.elements.inTipoEstabelecimentoOld != 'Selecione uma opção') ? PessoaForm.elements.inTipoEstabelecimentoOld : PessoaForm.withoutValue),
                                    $('#inTipoEstabelecimento option:selected').text()));
            }

            Message.show( 'Alterações realizadas', arrAlteracoes.join('<br />'), function() {
                $('#btnSalvar').trigger('click');
            } );
            $('.modal-header:visible > a.close').hide();
        }, 1000);
    },

    init: function() {
        PessoaJuridica.addValidationRequired();
        PessoaJuridica.initNaturezaJuridica();
        PessoaJuridica.initAbas();
        PessoaJuridica.initCnpj();
        PessoaForm.searchCpfCnpj($('#nuCnpj'), true);
        PessoaForm.urlPJ = true;
        PessoaJuridica.btnIntegrationInfoconv();
    }
};

$(document).ready(function() {
    PessoaJuridica.init();
});