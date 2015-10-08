var PessoaFisica = {
    method: 'update',
    withoutValue: '---',
    iterator: 0,
    salvar: function(form) {
        form.off('submit');
        form.submit();
    },
    send: function(form, ignore) {
        var isNew = !$('#sqPessoa').val() ? true : false;

        var serialization = form.serialize();
        $('[readonly], input[type=hidden]', '#form-dados-basicos').each(function(index, element) {
            serialization += '&' + $(element).attr('name') + '=' + $(element).val();
        });

        serialization += '&noPessoaFisica=' + $('#noPessoaFisica').val();

        $.ajax({
            url: '/auxiliar/pessoa-fisica/save',
            type: 'POST',
            data: serialization,
            success: function(response) {
                if (!PessoaFisica.iterator && !$('#sqPessoa').val() && response.sqPessoa) {
                    PessoaFisica.method = 'create';
                }

                localStorage.setItem('campoPessoa'  , response.campoPessoa);
                localStorage.setItem('campoCpf'     , response.campoCpf);
                localStorage.setItem('valorPessoa'  , response.sqPessoa);
                localStorage.setItem('valorCpf'     , response.nuCpf);
                localStorage.setItem('form'         , response.form);
                localStorage.setItem('noPessoa'     , response.noPessoa);
                PessoaFisica.iterator++;

                if (!response['return']) {
                    $('#body-error').html(response.message).removeClass('hide').addClass('show');
                    $('[href=#tab-dados-basicos]').trigger('click');
                } else {
                    // Mensagens
                    if (!ignore) {
                        $('.alert-success').removeClass('hide');

                        if ($('.alert-success').length > 1 && PessoaFisica.iterator != 1) {
                            $($('.alert-success')[0]).addClass('hide');
                        }

                        if ($('.alert-success').length > 1 && $($('.alert-success')[0]).is(':visible')) {
                            $('.msgSalvar').addClass('hide');
                        }
                    }

                    if (isNew) {
                        var append = '<input type="hidden" name="new" id="new" value="1" />';

                        $('#form-documento').append(append);
                        $('#form-endereco').append(append);
                    }

                    $('[name=sqPessoaSgdoce]').val(response.sqPessoaSgdoce);
                    $('[name=sqPessoa]').val(response.sqPessoa);
                    $('[name=sqPessoaFisica]').val(response.sqPessoa);

                    Endereco.init();
                    Email.init();
                    Telefone.init();
                    Documento.init();
                    if ($('#nuCpf').val()) {
                        $('.btn-visualizar-justificativa').remove();
                        $('#nuCpf').attr('readonly', true);
                    }
                }

                $(document).trigger('app.saved');
            }
        });
    },
    init: function() {
        var sqNacionalidade = $('input[name=sqNacionalidadeBrasileira]'),
                cpfNomePessoa = $('#nuCpf, #noPessoa');

        sqNacionalidade.off('click').on('click', function() {
            $(this).val() ?
                    $('.campoEstrangeiro').addClass('hide').removeClass('show') :
                    $('.campoEstrangeiro').addClass('show').removeClass('hide');
            $(this).val() ?
                    $('#sqPais').removeClass('required') :
                    $('#sqPais').addClass('required');
        });

        // Triggers necessários para funcionar/validar
        $('input[name=sqNacionalidadeBrasileira]:checked').trigger('click');

        $('#btnConcluir').off('click').on('click', function() {
            if ($('#form-dados-basicos').valid()) {
                var valido = ValidarFormPessoaFisica.dadosBasicos(false);

                if (valido) {
                    $('.alert').addClass('hide').removeClass('show');
                    PessoaFisica.send($('#form-dados-basicos'), true);
                    $(document).off('app.saved').on('app.saved', function() {
                        $('#modalConcluir').modal('show');
                        if ($('#new').length && $('#new').val() == 1) {
                            $('#modalConcluir fieldset p').text('Operação realizada com sucesso.');
                        } else {
                            if (PessoaFisica.method == 'update') {
                                $('#modalConcluir fieldset p').text('Alteração realizada com sucesso.');
                            }
                        }
                        $(document).off('app.saved');
                    });
                }
                return false;
            }
            return false;
        });
        
        $('#btnIntegrationInfoconv').off('click').on('click', function() {
            if ($('#nuCpf').val() != undefined && $('#nuCpf').val() != '') {
                PessoaFisica.confirmUpdateInfoconv($('#nuCpf'));
            } else {
                Message.showAlert('Informar um CPF.');
            }
        });

        $('#modalConcluir .btn-fechar-concluir').click(function() {
            window.close();
            $('#modalConcluir').hide();
        });
        $('.btn-fechar-janela').click(function() {
            window.close();
        });
    },
    
    confirmUpdateInfoconv: function(element) {
        var yesCallback = function() {
            PessoaFisica.getInformationInfoconv(element.val());
            
            if (localStorage.getItem('success') == 'true') 
            {
                if (localStorage.getItem('noPessoa') != '') {
                    $('#noPessoaFisica').val(localStorage.getItem('noPessoa')).attr('readonly', true);
                    $('#noPessoaFisica_hidden').val(localStorage.getItem('noPessoa'));
                }
                if (localStorage.getItem('dtNascimento') != '') {
                    $('#dtNascimento').val(localStorage.getItem('dtNascimento')).attr('readonly', true);
                }
                if (localStorage.getItem('noMae') != '') {
                    $('#noMae').val(localStorage.getItem('noMae')).attr('readonly', true);
                } else {
                    $('#noMae').attr('readonly', false);
                }
                if (localStorage.getItem('nacionalidade') != undefined && localStorage.getItem('nacionalidade') != '') {
                    
                    if (localStorage.getItem('nacionalidade') == 2) {
                        $("#sqNacionalidade-nao").attr('checked', 'checked').attr('readonly', true);
                        $('#sqPais').val(localStorage.getItem('sqPaisNaturalidade')).attr('disabled', true).attr('readonly', true);
                    } else {
                        $("#sqNacionalidade-sim").attr('checked', 'checked').attr('readonly', true);
                    }
                    $('input[name="sqNacionalidadeBrasileira"]:checked').trigger('click');
                    $('input[name="sqNacionalidadeBrasileira"]').attr('onclick', 'return false').off('click');
                } else {
                    $('input[name="sqNacionalidadeBrasileira"]').attr('readonly', false);
                }
                
                if ($('#sqPessoa').val() != undefined && $('#sqPessoa').val() != '') {
                    PessoaFisica.modalAlteracoes();
                } else {
                    $('#btnConcluir').trigger('click');
                }
                
            } else {
                if ($('#sqPessoa').val() == '') {
                    $('#nuCpf').val('');
                }
                Message.showAlert(localStorage.getItem('response'));
            }
        }
        var noCallback = function() {
            if (!$('#sqPessoa').val()) {
                element.val('');
            }
        }

        Message.showConfirmation({
            'body'       : 'O sistema atualizará os dados básicos referentes ao CPF informado (Nome e Data de Nascimento) conforme o cadastro da Receita Federal. Deseja continuar?',
            'yesCallback': yesCallback,
            'noCallback' : noCallback
        });
    },
    
    getInformationInfoconv: function( nuCpf )
    {
        var config = 
        {
            url     :'/auxiliar/infoconv/service-infoconv',
            type    :'post',
            data    : {'nuCpf' : nuCpf },
            dataType:'json',
            async   :false,
            success : function( result ) {
                PessoaFisica.setElementIntegrationInfoconv( result );
                localStorage.setItem('nuCpf'   , nuCpf);
                localStorage.setItem('response', result.response);
                localStorage.setItem('success' , result.success);
                localStorage.setItem('code'    , result.code);
                localStorage.setItem('personId', result.personId);
                $('.alert-success').html(result.response).removeClass('hide').addClass('show');
            },
            error: function() {
                $('#body-error').html(result.response).removeClass('hide').addClass('show');
            }
        };
        $.ajax(config);
    },
    
    setElementIntegrationInfoconv: function( result ) 
    {
        if ($('#sqPessoa').val() != undefined && $('#sqPessoa').val() != '') {
            if (result.noPessoa != '') {
                localStorage.setItem('noPessoaOld', $('#noPessoaFisica').val());
            }
            if (result.dtNascimento != '') {
                localStorage.setItem('dtNascimentoOld', $('#dtNascimento').val());
            }
            if (result.noMae != '') {
                localStorage.setItem('noMaeOld', $('#noMae').val());
            }
            if (result.nacionalidade != '') {
                localStorage.setItem('nacionalidadeOld', $('input[name=sqNacionalidadeBrasileira]:checked').parent().text().trim());
                if (result.sqPaisNaturalidade != '' && result.nacionalidade == 2) {
                    localStorage.setItem('noPaisNaturalidadeOld', 'Brasil');
                    localStorage.setItem('noPaisNaturalidade', result.noPaisNaturalidade);
                } else {
                	localStorage.setItem('noPaisNaturalidadeOld', $("#sqPais option[selected='selected']").text());
                    localStorage.setItem('noPaisNaturalidade', 'Brasil');
                }
            }
        }
        localStorage.setItem('noPessoa'            , result.noPessoa);
        localStorage.setItem('dtNascimento'        , result.dtNascimento);
        localStorage.setItem('noMae'               , result.noMae);
        localStorage.setItem('nacionalidade'       , result.nacionalidade);
        localStorage.setItem('sqPaisNaturalidade'  , result.sqPaisNaturalidade);
    },

    createItemDeParaInfoconv:function(title, oldValue, newValue){
        var xhtml = '<fieldset>';
            xhtml += '   <legend>' + title + '</legend>';
            xhtml += '   <div>';
            xhtml += '       <span class="span1">&rsaquo; Anterior:</span>';
            xhtml += '       <span class="span4">' + oldValue + '</span>';
            xhtml += '   </div>';
            xhtml += '   <div>';
            xhtml += '       <span class="span1">&rsaquo; Atual:</span>';
            xhtml += '       <span class="span4"><b>' + newValue + '</b></span>';
            xhtml += '   </div>';
            xhtml += '</fieldset>';
        return xhtml;
    },
    
    modalAlteracoes: function() {
        var arrAlteracoes = [];
        if (localStorage.getItem('noPessoa') != '') {
            arrAlteracoes.push(PessoaFisica.createItemDeParaInfoconv(
                                    'Nome',
                                    (localStorage.getItem('noPessoaOld')) ? localStorage.getItem('noPessoaOld') : PessoaFisica.withoutValue,
                                    localStorage.getItem('noPessoa')
                            ));
        }
        if (localStorage.getItem('dtNascimento') != '') {
            arrAlteracoes.push(PessoaFisica.createItemDeParaInfoconv(
                                    'Data de Nascimento',
                                    ((localStorage.getItem('dtNascimentoOld') != '') ? localStorage.getItem('dtNascimentoOld') : PessoaFisica.withoutValue),
                                    localStorage.getItem('dtNascimento')
                            ));
        }
        if (localStorage.getItem('nacionalidade') != undefined) {
            arrAlteracoes.push(PessoaFisica.createItemDeParaInfoconv(
                                    'Nacionalidade',
                                    (localStorage.getItem('nacionalidadeOld')) ? localStorage.getItem('nacionalidadeOld') : PessoaFisica.withoutValue,
                                    ((localStorage.getItem('nacionalidade') == 2) ? 'Não' : 'Sim')
                            ));
            arrAlteracoes.push(PessoaFisica.createItemDeParaInfoconv(
                                    'País de Origem',
                                    (localStorage.getItem('noPaisNaturalidadeOld')) ? localStorage.getItem('noPaisNaturalidadeOld'): PessoaFisica.withoutValue,
                                    localStorage.getItem('noPaisNaturalidade')
                            ));
        }
        if (localStorage.getItem('noMae') != '') {
            arrAlteracoes.push(PessoaFisica.createItemDeParaInfoconv(
                                    'Nome da Mãe',
                                    (localStorage.getItem('noMaeOld')) ? localStorage.getItem('noMaeOld') : PessoaFisica.withoutValue,
                                    localStorage.getItem('noMae')
                            ));
        }

        Message.show('Alterações realizadas', arrAlteracoes.join('<br />'), function () {
            $('#btnConcluir').trigger('click');
        });
        $('.modal-header:visible > a.close').hide();
	}
};

$(document).ready(function() {
    PessoaFisica.init();
});