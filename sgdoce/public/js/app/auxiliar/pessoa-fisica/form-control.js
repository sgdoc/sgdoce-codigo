var tab       = null,
	tabActive = null,
	tabIndex  = -1,
	tabTargetIndex = -1,
	iterator = 0;
	
$(document).off('ready');
$(document).on('ready', function() {
	tab = $('#tabPessoaFisica');
	
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
	
	$('#modalJustificativa .btn-fechar-justificativa').click(function() {
	    if(!$('#sqPessoa', '#form-dados-basicos').val()) {
	    	$('#modalJustificativa').find('textarea').val('');
	    	//$('#modalJustificativa').find('input, textarea').val('');
	        //$('#modalJustificativa').find('select')[0].selectedIndex = 0;
	        $('#modalJustificativa').find('.help-block').remove();
	        $('#modalJustificativa').find('.error').removeClass('error');
	    }
	});
	
	$('.btn-nav').off('click');
	$('.btn-nav').on('click', function(e) {
	    if(!$(this).is('.disabled')) {
    		tabActive = tab.find('.active');
    		tabIndex  = $(this).hasClass('btn-prev') ?
    			$('li', tab).index(tabActive) - 1 :
    			$('li', tab).index(tabActive) + 1;
    		
    		$('li a', tab).eq(tabIndex).tab('show');
	    }
	});
	
	$('#tabPessoaFisica a').off('show');
	$('#tabPessoaFisica a').on('show', function(event) {
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

		navigate();
		
		iterator++;
	});
	
	var originalValue = $('#nuCpf', '#form-dados-basicos').val();
	$('#nuCpf', '#form-dados-basicos').off('blur').on('blur', function() {
		var value = $(this).val();
		
		if(
			value 
			&& value.length === 14 
			&& value !== originalValue
			&& $(this).is(':not([readonly])')
		) {
			$.post('/auxiliar/pessoa-fisica/search-cpf', {
				nuCpf : value,
			}, function(response) {
				if(response && response.sqPessoa) {
					$('#modalCpf').modal('show');
					$('.btn-primary', '#modalCpf').attr('href', '/auxiliar/pessoa-fisica/edit/id/' + response.sqPessoa);
					
					$('#modalCpf').off('hide').on('hide', function() {
						$('#nuCpf', '#form-dados-basicos').val('');
					});
				} else {
					PessoaFisica.confirmUpdateInfoconv($('#nuCpf'));
				}
			});
		}
	});
	
	var originalValue = $('#noPessoaFisica', '#form-dados-basicos').val();
	$('#noPessoaFisica', '#form-dados-basicos').off('blur').on('blur', function() {
		var value = $(this).val();
		
		if(
			value 
			&& value !== originalValue
			&& $(this).is(':not([readonly])')
		) {
			$.post('/auxiliar/pessoa-fisica/search-nome-pessoa', {
				noPessoaFisica : value,
			}, function(response) {
				if(response && response.sqPessoa) {
					$('#modalNomePessoa').modal('show');
					$('.btn-primary', '#modalNomePessoa').attr('href', '/auxiliar/pessoa-fisica/edit/id/' + response.sqPessoa);
				}
			});
		}
	});

    $('#noPessoaFisica').simpleAutoComplete('/auxiliar/pessoa-fisica/search-pessoa-fisica', {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel',
        clearInput: true
    });

    $('li.sel').live('click', function(element) {
    	var value = $(element.currentTarget).text().replace(/^([0-9]{3})\.([0-9]{3})\.([0-9]{3})\-([0-9]{2})\s-\s/, '');
    	
    	$('#noPessoaFisica').val(value);
    });
	
	// Abre a aba correspondente à url de acordo com o hash 
	if(window.location.hash) {
		var hash = window.location.hash.replace('#', '');
		
		if($('#tabPessoaFisica a[href=#tab-' + hash + ']').length) {
			$('#tabPessoaFisica a[href=#tab-' + hash + ']').tab('show');
		}
	}
	
	function validarAba(index) {
		switch(index) {
			default:
				return ValidarFormPessoaFisica.dadosBasicos(true);
				
				break;
				
			case -1:
				break;
		}
	}
});

var ValidarFormPessoaFisica = {
	validated : false,
	
	dadosBasicos : function(send) {
		var tab    = $('#tab-dados-basicos'),
			form   = $('form', tab),
			inputs = $('.obrigatorio:not(:hidden)'),
			error  = false;
		
		if(!$('[name=aba]', form).length) {
			form.append($('<input type="hidden" name="aba" value="documentos">'));
		}
		
		$('[name=aba]', form).val('documentos');
        
        if(inputs.length) {
            if(!$('#form-dados-basicos').valid()) {
                error = true;
            }
        }
		
		if(!error) {
			if(
				(!$('#nuCpf', form).val() || $('#nuCpf', form).val().length !== 14) 
				&& !$('.btn-visualizar-justificativa', form).length 
			) {
                var pedeCredenciais = true;

                $('#sqResponsavel option').each(function(key,value){
                    if ($(value).val() == $('#pessoa').val()){
                        pedeCredenciais = false;
                    }
                });
                var modal = '#modalJustificativa';
                $('.btn-modal').attr('data-target', modal)
                    .attr('href', modal)
                    .trigger('click');

                Justificativa.init(form,pedeCredenciais);

				return false;
			} else {
				if(tabTargetIndex === 0 && !$('#sqPessoa', '#form-dados-basicos').val()) {
					form.off('submit.validate');
				}
			}
			
			if(send) {
				if(!$('#sqPessoa').val() || !$('#sqPessoaSgdoce').val()) {
					PessoaFisica.send(form);
				} else {
                    Endereco.init();
                    Email.init();
                    Telefone.init();
                    Documento.init();
				}
			}
			
			if($('#nuCpf', form).parents('.error').length) { 
                return false;
            }
			
			return true;
		} else {
			$('#body-error').show();
		}
		
		return false;
	}	
};