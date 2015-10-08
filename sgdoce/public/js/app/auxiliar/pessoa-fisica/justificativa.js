var Justificativa = {
	init : function(form,pedeCredenciais) {
        $('#txJustificativa').off('keyup').on('keyup', function(){
            if(!$('#txJustificativa').val().length){
                $(this).parent().parent().addClass('error');
                $(this).parent().find('p.help-block').remove();
                $(this).parent().append('<p for="group9" generated="true" class="help-block">Campo de preenchimento obrigatório.</p>');
            } else {
                $(this).parent().parent().removeClass('error');
                $(this).parent().find('p.help-block').remove();
            }
        });

        if (!pedeCredenciais){
            $('#divCredenciais').hide();
            $('#labelInformarSenha').hide();
        }
		$('#btn-concluir-justificativa').off('click').on('click', function() {
			if(Justificativa.validar(pedeCredenciais)) {
				if($('[name=txJustificativa]', form).length) {
					$('[name=txJustificativa]', form).remove();
				}
				
				form.append($('<input type="hidden" name="txJustificativa" value="' + $('#txJustificativa').val() + '">'));

				$.ajax({
				    data : {
                        isChefe:pedeCredenciais,
				        sqResponsavel : $('#sqResponsavel').val() ? $('#sqResponsavel').val() : $('#pessoa').val(),
				        nuCpfResponsavel : $('#nuCpfResponsavel').val(),
				        txSenhaResponsavel : $('#txSenhaResponsavel').val(),
				        txJustificativa : $('#txJustificativa').val()
				    },
				    method : 'post',
				    url : '/auxiliar/pessoa/autorizar-justificativa',
				    success : function(response) {
				        if(response && response.result === true) {
				            $('.btn-fechar-justificativa').trigger('click');
                            $('#form-dados-basicos #sqPessoaResponsavel').val(response.sqPessoaResponsavel);
				            $('#modalJustificativa').off('hidden').on('hidden', function() {
				                // removido conforme solicitação no ticket #15388
				                //Documento.adicionar();
				                //$('#btn-adicionar-documento').trigger('click');
				                
				                // Ao abrir modal.
				                // Evento customizado no arquivo documento/modal.js
                                //$(document).off('app.ready-submit').on('app.ready-submit', function() { // removido conforme solicitação no ticket #15388
                                    $.ajax({
                                        url : '/auxiliar/pessoa-fisica/save',
                                        type : 'POST',
                                        data : $('#form-dados-basicos').serialize()
                                    }).done(function(response) {
                                        if(response.sqPessoa) {
                                        	// removido conforme solicitação no ticket #15388
                                            //$('#form-documento-modal [name*="[sqPessoa]"]').val(response.sqPessoa);
                                            //$('#form-documento-modal #sqPessoa-modal').val(response.sqPessoa);
                                            //$('#form-documento-modal #sqPessoaSgdoce-modal').val(response.sqPessoaSgdoce);

                                            localStorage.setItem('campoPessoa',response.campoPessoa);
                                            localStorage.setItem('campoCpf'   ,response.campoCpf);
                                            localStorage.setItem('valorPessoa',response.sqPessoa);
                                            localStorage.setItem('valorCpf'   ,response.nuCpf);
                                            localStorage.setItem('form'       ,response.form);
                                            localStorage.setItem('noPessoa'   ,response.noPessoa);
                                            // removido conforme solicitação no ticket #15388
                                            //$('#form-documento-modal').off('submit');

                                            if($('#txImagem', '#form-documento-modal').val()) {
                                                $('#modalUpload').modal('show');
                                            }
                                            
                                            Message.show('Sucesso', '<div >Opera\u00e7\u00e3o realizada com sucesso.</div>');
                                            //redirecionamento feito após save (como era no modo antigo, após documento ter sido salvo)
                                            $('a[data-handler=0]').click(function(){
                                                var url = window.location.href.toString().split(window.location.host)[1];
                                                if (url.search('/auxiliar/pessoa-fisica/edit') === 0){
                                                    window.location.reload();
                                                } else {
                                                    window.location = '/auxiliar/pessoa-fisica/edit/id/'+response.sqPessoa+'/form/'+response.form+'/campoPessoa/'+response.campoPessoa+'/campoCpf/'+response.campoCpf+'/#documentos';
                                                }
                                            });
                                        }
                                    });
                                //}); // removido conforme solicitação no ticket #15388
                                
                                $('#modalJustificativa').off('hidden');
                                return false;
				            });
				        } else {
				            if($('#modalJustificativa .alert-error').length) {
				                $('#modalJustificativa').find('.alert-error').remove();
				            }
				            
				            $('#modalJustificativa').find('fieldset').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.mensagem + '</div>');
				        }
				    }
				});
			}
			
			return false;
		});
		
		$('#txJustificativa').on('keyup blur', function(e) {
		    var value = $(this).val();
		    
		    if(value.length > 200) {
		        value = value.substr(0,200);
		        
		        $(this).val(value);
		    }
		});
	},
	
	validar : function(pedeCredenciais) {
		
	    var camposObrigatorios = $('.obrigatorio', '#modalJustificativa');
	    var result = true;
        if (pedeCredenciais) {
            camposObrigatorios.each(function(index, element) {
                var el = $(element);

                el.parent().find('p.help-block').remove();

                if(!el.val()) {
                    el.parent().parent().addClass('error');
                    el.parent().append('<p for="group9" generated="true" class="help-block">Campo de preenchimento obrigatório.</p>');

                    result = false;
                } else {
                    el.parent().parent().removeClass('error');
                }
            });
        }

        if(!result) {
            return false;
        }

        if($.trim($('#txJustificativa').val()).split(' ').length <= 4){
            $('#txJustificativa').parent().parent().addClass('error');
            $('#txJustificativa').parent().find('p.help-block').remove();
            $('#txJustificativa').parent().append('<p for="group9" generated="true" class="help-block">A justificativa deve conter no mínimo 05 palavras.</p>');

            return false;
        }
        
        return true;
	}
};