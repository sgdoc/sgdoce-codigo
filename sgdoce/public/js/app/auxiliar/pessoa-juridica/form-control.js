var tab       = null,
	tabActive = null,
	origin    = null
	tabIndex  = -1,
	tabTargetIndex = -1,
	iterator = 0;
	
$(document).off('ready');
$(document).ready(function() {
	origin = window.location.hash;
	tab    = $('#tabPessoaJuridica');
	
	function navigate() {
		$('.btn-nav').removeClass('disabled');
        $('.btn-nav.btn-concluir').removeClass('btn-primary');
			
		if(!tabIndex) {
			$('.btn-nav.btn-prev').addClass('disabled');
			$('#btnIntegrationInfoconv').show();
		} else {
			$('#btnIntegrationInfoconv').hide();
		}
		
		if(tabIndex >= (tab.find('li').length - 1)) {
			$('.btn-nav.btn-next').addClass('disabled');
            $('.btn-nav.btn-concluir').addClass('btn-primary');
		}
		
		$('html, body').animate({ scrollTop : 0 }, 'fast');
	};
	
	$('.btn-nav').off('click');
	$('.btn-nav').on('click', function(e) {
		tabActive = tab.find('.active');
		tabIndex  = $(this).hasClass('btn-prev') ?
			$('li', tab).index(tabActive) - 1 :
			$('li', tab).index(tabActive) + 1;
			
		$('li a', tab).eq(tabIndex).tab('show');
	});
	
	$('a', tab).off('show');
	$('a', tab).on('show', function(event) {
		if(iterator) {
			$('.alert').addClass('hide').removeClass('show');
		}
	    
		tabTarget      = $(event.target).parent();
		tabActive      = $(event.relatedTarget).parent();
		
		tabIndex       = $('li', tab).index(tabTarget);
		tabTargetIndex = $('li', tab).index(tabActive);
			
		var hash  = $('li a', tab).eq(tabIndex).attr('href').replace('tab-', '');
		var regex = new RegExp(hash);

		if(!regex.test(window.location.toString())) {
			window.location = window.location.toString().replace(/\#[A-Za-z0-9-_]*/ig, '') + hash;
		}

		if(tabIndex > tabTargetIndex) {
			if(!validarAba(tabIndex)) {
				return false;
			}
		}
		
        if(((tabIndex + 1) == $('li', tab).length)) {
            $('[href=#modalConcluir]').addClass('btn-primary')
                .find('i')
                .addClass('icon-white');
        } else {
            $('[href=#modalConcluir]').removeClass('btn-primary')
                .find('i')
                .removeClass('icon-white');
        }

		navigate();
		
		iterator++;
	});
	
	var originalValueCnpj = $('#nuCnpj', '#form-dados-basicos').val().replace(/[\.\/-]/g, '');
	$('#nuCnpj', '#form-dados-basicos').off('blur').on('blur', function() {
		var value = $(this).val().replace(/[\.\/-]/g, '');
		
		if(
			value 
			&& value.length === 14 
			&& value !== originalValueCnpj
			&& $(this).is(':not([readonly])')
		) {
			var searchMatrizFilial = function() {
				$.post('/auxiliar/pessoa-juridica/search-matriz-filial', {
					nuCnpj : value
				}, function(response) {
					if(response['return']) {
						$('#btnModalMatrizFilial').removeClass('hide');
					}
					PessoaJuridica.confirmUpdateInfoconv($('#nuCnpj'));
				});
			};
			
			$.post('/auxiliar/pessoa-juridica/search-cnpj', {
				nuCnpj : value,
			}, function(response) {
				$('#btnModalMatrizFilial').addClass('hide');
				
				if(response && response.sqPessoa) {
					$('#modalCnpj').modal('show');
					$('.btn-primary', '#modalCnpj').attr('href', '/auxiliar/pessoa-juridica/edit/id/' + response.sqPessoa);
					
					$('#modalCnpj').off('hide').on('hide', function() {
						$('#nuCnpj', '#form-dados-basicos').val('');
					});
				} else {
					searchMatrizFilial();
				}
			});
		}
	});
	
	var originalValue = $('#noPessoa', '#form-dados-basicos').val();
	$('#noPessoa', '#form-dados-basicos').off('blur').on('blur', function() {
		var value = $(this).val();
		
		if(
			value 
			&& value !== originalValue 
			&& !$('#sqPessoa').val()
			&& $(this).is(':not([readonly])')
		) {
			$.post('/auxiliar/pessoa-juridica/search-razao-social', {
				noPessoa : value,
			}, function(response) {
				if(response && response.sqPessoa) {
					$('#modalRazaoSocial').modal('show');
					$('.btn-primary', '#modalRazaoSocial').attr('href', '/auxiliar/pessoa-juridica/edit/id/' + response.sqPessoa);
					
					$('#modalRazaoSocial').off('hide').on('hide', function() {
						$('#noPessoa', '#form-dados-basicos').val('');
						$('#noPessoa_hidden', '#form-dados-basicos').val('');
					});
				}
			});
		}
	});

    $('#noPessoa').simpleAutoComplete('/auxiliar/pessoa-juridica/search-pessoa-juridica', {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel',
        clearInput: true
    });

    $('li.sel').live('click', function(element) {
    	var value = $(element.currentTarget).text().replace(/^([0-9]{2})\.([0-9]{3})\.([0-9]{3})\/([0-9]{4})\-([0-9]{2})\s-\s/, '');
    	
    	$('#noPessoa').val(value);
    });
	
	$('.btn-print', '#modalMatrizFilial').off('click').on('click', function() {
		window.location = '/auxiliar/pessoa-juridica/gerar-doc-matriz-filial/?sqPessoa=' + 
			$('#sqPessoa', '#form-dados-basicos').val() + '&nuCnpj=' + $('#nuCnpj', '#form-dados-basicos').val(); 
		
		return false;
	});
	
	function validarAba(index) {
		switch(index) {
			default:
				return ValidarFormPessoaJuridica.dadosBasicos(true);
				
				break;
				
			case -1:
				break;
		}
	}
    
    // Abre a aba correspondente à url de acordo com o hash 
    if(window.location.hash) {
        var hash = window.location.hash.replace('#', '');
        
        if($('#tabPessoaJuridica a[href=#tab-' + hash + ']').length) {
            setTimeout(function() {
                $('#tabPessoaJuridica a[href=#tab-' + hash + ']').trigger('click');
            }, 200);
        }
    }
});

var ValidarFormPessoaJuridica = {
	validated : false,
	
	dadosBasicos : function(send) {
		if(!$('#nuCnpj').val()) {
			$('#nuCnpj').attr('title', 'Campo de preenchimento obrigatório.');
		}
		
		if(
			$('#sqPessoa', '#form-dados-basicos').length && 
			(origin === '#dados-basicos' || !origin)
		) {
			this.validated = true;
		}
		
		var tab    = $('#tab-dados-basicos'),
			form   = $('form', tab),
			inputs = $('.obrigatorio:not(:hidden)'),
			error  = false;
		
		if(!$('[name=aba]', form).length) {
			form.append($('<input type="hidden" name="aba" value="enderecos">'));
		}
		
		$('[name=aba]', form).val((window.location.hash).replace('#', ''));
		
		if(inputs.length) {
		    if(!$('#form-dados-basicos').valid()) {
		        error = true;
		    }
		    
		    $('#nuCnpj').attr('title', 'CNPJ inválido.');
		}
		
		if(!error) {
			if(
				(tabTargetIndex === 0 && !$('#sqPessoa', '#form-dados-basicos').val()) ||
				(this.validated && tabTargetIndex === 0)
			) {
				form.off('submit.validate');
			}
			
			if(send) {
				if(!$('#sqPessoa').val() || !$('#sqPessoaSgdoce').val()) {
					PessoaJuridica.send(form);
				} else {
                    Endereco.init();
                    Email.init();
                    Telefone.init();
				}
			}
			
			return true;
		} else {
			$('#body-error').show();
		}
		
		return false;
	}	
};