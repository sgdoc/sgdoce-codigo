var PessoaJuridica = {
	method : 'update',
	withoutValue: '---',
    iterator : 0,
	send : function(form, ignore) {
		var isNew = !$('#sqPessoa').val() 
			? true
			: false;
		
		encodeURIComponent($('input[name="noPessoa"]').val());
		encodeURIComponent($('input[name="noPessoa_autocomplete"]').val());
		encodeURIComponent($('input[name="noFantasia"]').val());
		var serialization = form.serialize();
		$('[disabled]', '#form-dados-basicos').each(function(index, element) {
			serialization += '&' + $(element).attr('id') + '=' + $(element).val();
		});
		
		
	    $.ajax({
            url : '/auxiliar/pessoa-juridica/save',
            type : 'POST',
            data : serialization,
            success : function(response) {
            	if(
        			!PessoaJuridica.iterator
        			&& !$('#sqPessoa').val()
        			&& response.sqPessoa
    			) {
            		PessoaJuridica.method = 'create';
            	}
                localStorage.setItem('campoPessoa',response.campoPessoa);
                localStorage.setItem('campoCnpj',response.campoCnpj);
                localStorage.setItem('valorPessoa',response.sqPessoa);
                localStorage.setItem('valorCnpj',response.nuCnpj);
                localStorage.setItem('form',response.form);
                localStorage.setItem('noPessoa',response.noPessoa);
            	PessoaJuridica.iterator++;
            	
                if(!response['return']) {
                    $('#body-error').html('<button type="button" class="close" data-dismiss="alert">&times;</button>' + response.message)
                        .removeClass('hide')
                        .addClass('show');
                    
                    Message.show('Erro', '<div >'+response.message+'</div>');
                    
                    $('[href=#tab-dados-basicos]').trigger('click');
                } else {
                	// Mensagens
                    if(!ignore) {
                        $('.alert-success').removeClass('hide');
                        
                        if($('.alert-success').length > 1 && PessoaJuridica.iterator != 1) {
                        	$($('.alert-success')[0]).addClass('hide');
                        }
                        
                    	if(
                			$('.alert-success').length > 1 
                			&& $($('.alert-success')[0]).is(':visible')
            			) {
                    		$('.msgSalvar').addClass('hide');
                    	}
                    }
                    
                    if(isNew) {
                    	var append = '<input type="hidden" name="new" id="new" value="1" />';
                    	
                        $('#form-endereco').append(append);
                    }
                    
                    $('[name=sqPessoa]').val(response.sqPessoa);
                    $('[name=sqPessoaSgdoce]').val(response.sqPessoaSgdoce);
                    $('[name=sqDocumento]').val(response.sqDocumento);

                    $('#btnModalMatrizFilial').removeClass('hide');
                    $('#nuCnpj').attr('readonly', true);
                    $('#noPessoa').attr('readonly', true);
                    
                    Endereco.init();
                    Email.init();
                    Telefone.init();
                    $(document).trigger('app.saved');
                }
                
            }
        });
	},
	
	init : function() {
		$('.form-actions .btn-concluir').off('click').on('click', function() {
		    if($('#form-dados-basicos').valid()) {
		    	var valido = ValidarFormPessoaJuridica.dadosBasicos(false);
		    	
		    	if(valido) {
		    		$('.alert').addClass('hide').removeClass('show');
		    		
	    		    PessoaJuridica.send($('#form-dados-basicos'), true);
	                
	                $(document).off('app.saved').on('app.saved', function() {
	                    $('#modalConcluir').modal('show');
	                    
	                    if($('#new').length && $('#new').val() == 1) {
	                    	$('#modalConcluir fieldset p').text('Operação realizada com sucesso.');
	                    } else {
	                    	if(PessoaJuridica.method == 'update') {
	                    		$('#modalConcluir fieldset p').text('Alteração realizada com sucesso.');
	                    	}
	                    }
	                    
	                    $(document).off('app.saved');
	                });
		    	}
		    }
            $('#modalConcluir .btn-fechar-concluir').click(function() {
                window.close();
                $('#modalConcluir').hide();
            });
		    return false;
		});

        $('#modalConcluir .btn-fechar-concluir').click(function() {
            window.close();
            $('#modalConcluir').hide();
        });
        $('.btn-fechar-janela').click(function(){
            window.close();
        });
        
        $('#btnIntegrationInfoconv').off('click').on('click', function() {
            if ($('#nuCnpj').val() != undefined && $('#nuCnpj').val() != '') {
            	PessoaJuridica.confirmUpdateInfoconv($('#nuCnpj'));
            } else {
                Message.showAlert('Informar um CNPJ.');
            }
        });
	},
    
    confirmUpdateInfoconv: function(element) {
        var yesCallback = function() {
            PessoaJuridica.getInformationInfoconv(element.val());
            
            if (localStorage.getItem('success') == 'true') 
            {
                if (localStorage.getItem('noPessoa') != '') {
                    $('#noPessoa').val(localStorage.getItem('noPessoa')).attr('readonly', true);
                    $('#noPessoa_hidden').val(localStorage.getItem('noPessoa'));
                }
                
                $('#noFantasia')        .val( localStorage.getItem('noFantasia') )        .attr('readonly', true);
                $('#sqNaturezaJuridica').val( localStorage.getItem('sqNaturezaJuridica') ).attr('readonly', true).prop('disabled', true);
                
                if ($('#sqPessoa').val() != undefined && $('#sqPessoa').val() != '') {
                    PessoaJuridica.modalAlteracoes();
                } else {
                    $('.btn-concluir').trigger('click');
                }
                
            } else {
                if ($('#sqPessoa').val() == '') {
                    $('#nuCnpj').val('');
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
    
    getInformationInfoconv: function( nuCnpj )
    {
        var config = 
        {
            url     :'/auxiliar/infoconv/service-infoconv',
            type    :'post',
            data    : {'nuCnpj' : nuCnpj },
            dataType:'json',
            async   :false,
            success : function( result ) {
                PessoaJuridica.setElementIntegrationInfoconv( result );
                localStorage.setItem('nuCnpj'   , nuCnpj);
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
                localStorage.setItem('noPessoaOld', $('#noPessoa').val());
            }
            if (result.noFantasia != '') {
                localStorage.setItem('noFantasiaOld', $('#noFantasia').val());
            }
            if(result.sqNaturezaJuridica != '') {
                localStorage.setItem('sqNaturezaJuridicaOld', $('#sqNaturezaJuridica option:selected').text());
            }
        }
        localStorage.setItem('noPessoa'              , result.noPessoa);
        localStorage.setItem('noFantasia'            , result.noFantasia);
        localStorage.setItem('inTipoEstabelecimento' , result.inTipoEstabelecimento);
        localStorage.setItem('sqNaturezaJuridicaPai' , result.sqNaturezaJuridicaPai);
        localStorage.setItem('sqNaturezaJuridica'    , result.sqNaturezaJuridica);
    },
    
    modalAlteracoes: function() {
    	var arrAlteracoes = [];
        if (localStorage.getItem('noPessoa') != '') {
            arrAlteracoes.push(PessoaJuridica.createItemDeParaInfoconv(
                                    'Nome',
                                    (localStorage.getItem('noPessoaOld')) ? localStorage.getItem('noPessoaOld') : PessoaJuridica.withoutValue,
                                    localStorage.getItem('noPessoa')
                            ));
        }
        if (localStorage.getItem('noFantasiaOld') != '') {
            arrAlteracoes.push(PessoaJuridica.createItemDeParaInfoconv(
                                    'Nome Fantasia',
                                    ((localStorage.getItem('noFantasiaOld') != '') ? localStorage.getItem('noFantasiaOld') : PessoaJuridica.withoutValue),
                                    localStorage.getItem('noFantasia')
                            ));
        }
        if (localStorage.getItem('sqNaturezaJuridica') != '') {
            arrAlteracoes.push(PessoaJuridica.createItemDeParaInfoconv(
                                    'Tipo de Sociedade',
                                    (localStorage.getItem('sqNaturezaJuridicaOld')) ? localStorage.getItem('sqNaturezaJuridicaOld') : PessoaJuridica.withoutValue,
                                    $('#sqNaturezaJuridica option:selected').text()
                            ));
        }

        Message.show('Alterações realizadas', arrAlteracoes.join('<br />'), function () {
            $('.btn-concluir').trigger('click');
        });
        $('.modal-header:visible > a.close').hide();
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
    }
};

$(document).ready(function() {
	PessoaJuridica.init();
});
