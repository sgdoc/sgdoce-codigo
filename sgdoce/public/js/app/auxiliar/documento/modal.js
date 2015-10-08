DocumentoModal = {
	serializeObject : function(form) {
		var elementsList = $('select:not(:hidden), input:not(:hidden), textarea:not(:hidden), input[type=hidden]', form),
			strObj       = '{';

	    $.each(elementsList, function(index, element) {
	        var el  = $(element);

	        strObj += (index == 0 ? '' : ',') + '"' + el.attr('name') + '":"' + el.val() + '"';
	    });

	    strObj += '}';

		return JSON.parse(strObj);
	},

	salvar : function() {
        $('.btn-adicionar', '#modalDocumento').off('click').on('click', function() {
            $('.btn-adicionar', '#modalDocumento').attr('disabled', 'disabled');
            if(!$('[name=sqTipoPessoa]', '#form-documento-modal').length) {
                $('#form-documento-modal').append(
                    '<input type="hidden" name="sqTipoPessoa" value="' + $('[name=sqTipoPessoa]').val() + '" />'
                );
            }
            
            if($('#form-documento-modal').valid() && !$('.error', '#modalDocumento').length) {
                $(document).trigger('app.ready-submit');
                DocumentoModal.concluir();
            } else {
                $('.alert-error').addClass('hide');
                $('.alert-error', '#modalDocumento').removeClass('hide');
                $('.btn-adicionar', '#modalDocumento').removeAttr('disabled');
            }
            
            return false;
        });
	},

    concluir: function() {
    	var sqTipoDocumento = $('#sqTipoDocumento').val();
    	setTimeout(function(){
            $.ajax({
                url : '/auxiliar/documento/save',
                type : 'POST',
                data : $('#form-documento-modal').serialize(),
                success : function(response) {
                    $('#modalDocumento').modal('hide');
                    var form = localStorage.getItem('form');
                    var campoPessoa = localStorage.getItem('campoPessoa');
                    var campoCpf = localStorage.getItem('campoCpf');
                    $('#form-documento-modal').append($('<input type="hidden" name="form" value="' + form + '">'));
                    $('#form-documento-modal').append($('<input type="hidden" name="campoPessoa" value="' + campoPessoa + '">'));
                    $('#form-documento-modal').append($('<input type="hidden" name="campoCpf" value="' + campoCpf + '">'));
                    if(
                        $('#txImagem', '#form-documento-modal').val()
                            && response.content
                        ) {
                        if($('#new', '#form-documento').length && $('#new', '#form-documento').val()) {
                            $('#form-documento-modal').append('<input type="hidden" name="new" id="new" value="1" />');
                        }

                        $('#form-documento-modal').append('<input type="hidden" name="sqTipoDocumento" id="sqTipoDocumento" value="' + sqTipoDocumento + '" />');
//                    $('#form-documento-modal').append('<input type="hidden" name="sqTipoDocumento" id="sqTipoDocumento" value="' + response.content.documento.sqAtributoTipoDocumento.sqTipoDocumento.sqTipoDocumento + '" />');

                        $('#form-documento-modal').off('submit').on('submit', function() {
                            $('#modalUpload').modal('show');
                        });

                        $('#form-documento-modal').submit();
                        return true;
                    } else {
                        Message.showMessage(response);
                        $('a[data-handler=0]').click(function(){
                            var url = window.location.href.toString().split(window.location.host)[1];
                            if (url.search('/auxiliar/pessoa-fisica/edit') === 0){
                                window.location.reload();
                            } else {
                                window.location = '/auxiliar/pessoa-fisica/edit/id/'+ $('#form-documento-modal #sqPessoa-modal').val()+'/form/'+form+'/campoPessoa/'+campoPessoa+'/campoCpf/'+campoCpf+'/#documentos';
                            }
                        });
                    }

                }
            });
        },2000)

    },

    initMask: function(){
        $('.numeric').setMask({
            mask: '9',
            type: 'repeat'
        });

        $('.dateBR').setMask('date');
        $('select').removeAttr('multiple');
    },

    initDatePicker: function(){
        var options = {
            format: 'dd/mm/yyyy',
            language: 'br'
        };

        $('.datepicker').datepicker(options);
        
        $('.date .add-on').off('click').on('click', function() {
        	$(this).parent().find('input').trigger('focus');
        });
    },

    init: function(){
		$('[class*=tipo-documento-]').addClass('hide');
		$('#sqTipoDocumento').on('change', function(){
			var value = $(this).val();
			var all   = $('[class*=tipo-documento-]');

			all.addClass('hide');
			
			$('.error', '#modalDocumento').removeClass('error');
            $('.help-block').removeClass('show').addClass('hide');
            $('.alert-error').removeClass('show').addClass('hide');
            $('.img-upload-container').removeClass('show').addClass('hide');

			if(value) {
				$('.tipo-documento-' + value).removeClass('hide').addClass('show');
				$('.img-upload-container').removeClass('hide').addClass('show');
			}
			
			if(!$('#verificador', '#modalDocumento').length) {
    			$('#txImagem', '#modalDocumento').val('');
    			$('input[type=text], textarea', '#modalDocumento').val('');
    			$('select:not(#sqTipoDocumento)', '#modalDocumento').each(function(index, select) {
    			    select.selectedIndex = 0;
    			});
			}
		});
		
		
	    $('#sqTipoDocumento').trigger('change');
		
		$('.tipo-documento-4').each(function(index, element) {
		    if(!$('.controls', element).html()) {
		        $(element).remove();
		    }
		});

        DocumentoModal.salvar();
        DocumentoModal.initValidateFile();
        DocumentoModal.initMask();
        DocumentoModal.initDatePicker();
        
        $('.obrigatorio, .required', '#form-documento-modal').each(function(index, element) {
            var label = $($(element).parents('.control-group')[0]).find('label');
            
            label.html('<span title="Required field" class="required">*</span> ' + label.text());
        });
        
        $('.hide-parent', '#form-documento-modal').each(function(index, element) {
            $($(element).parents('.control-group')[0]).hide();
        });
        
        $('.controls', '#form-documento-modal').each(function(index, element) {
            if(!$(element).html().trim()) {
                $($(element).parents('.control-group')[0]).hide();
            }
        });
    },
    
    initValidateFile : function() {
        $('#txImagem', '#form-documento-modal').on('change', function() {
            if($(this).val()) {
                var input     = $(this);
                var value     = input.val();
                var extension = value.substring(value.lastIndexOf('.') + 1);
                var $return   = true;
                var msg       = '';

                if(!/(png)$/i.test(extension)) {
                    msg = 'Extensão do arquivo inválida. Selecione arquivos no formato .PNG.';
                    
                    $return = false;
                }
                
                if(this.files[0].size > (25 * 1000 * 1000)) {
                    msg = 'O tamanho do arquivo é superior ao permitido. O tamanho máximo permitido é 25Mb.';
                    
                    $return = false;
                }
                
                if(!$return) {
                    input.closest('.control-group').addClass('error');
                    input.closest('.control-group').find('.help-block')
                        .removeClass('hide')
                        .addClass('show')
                        .html(msg);
                } else {
                    input.closest('.control-group').removeClass('error');
                    input.closest('.control-group').find('.help-block')
                        .removeClass('show')
                        .addClass('hide');
                }
            }
        });
    }
};

(function() {
	DocumentoModal.init();
})();