EnderecoModal = {

    initCep: function(){
        $('#form-endereco-modal #sqCep').setMask('cep');
        $('#form-endereco-modal #nuEndereco').setMask({
            mask: '9',
            type: 'repeat'
        });

        $('#form-endereco-modal #sqEstadoEndereco').off('change').on('change', function(){
            Address.config.municipio = $('#form-endereco-modal #sqMunicipioEndereco');
            Address.populateMunicipioFromEstado($(this).val());
        });

        $('#btnCep').click(function(){
            Address.config.cep = $('#form-endereco-modal #sqCep');
            Address.config.pais = $('#form-endereco-modal #sqPai');
            Address.config.estado = $('#form-endereco-modal #sqEstadoEndereco');
            Address.config.municipio = $('#form-endereco-modal #sqMunicipioEndereco');
            Address.config.bairro = $('#form-endereco-modal #noBairro');
            Address.config.endereco = $('#form-endereco-modal #txEndereco');
            Address.config.numero = $('#form-endereco-modal #nuEndereco');
            Address.config.complemento = $('#form-endereco-modal #txComplemento');

            Address.populateFromCep($('#form-endereco-modal #sqCep').val());
        });
    },

    concluir: function(){
    	$('.btnAdicionarEndereco').off('click').on('click', function() {
    	    if(!$('[name=sqTipoPessoa]', '#form-endereco-modal').length) {
	            $('#form-endereco-modal').append(
                    '<input type="hidden" name="sqTipoPessoa" value="' + $('[name=sqTipoPessoa]').val() + '" />'
                );
    	    }

            if($('#form-endereco-modal').valid()) {
                $.ajax({
                    url : '/auxiliar/endereco/save',
                    type : 'POST',
                    data : $('#form-endereco-modal').serialize(),
                    success : function(response) {
                        if(
                            $('#txImagem', '#form-endereco-modal').val()
                            && response.content.sqEndereco
                        ) {
                            if($('#new', '#form-endereco').length && $('#new', '#form-endereco').val()) {
                                $('#form-endereco-modal').append('<input type="hidden" name="new" id="new" value="1" />');
                            }

//                            if(!$('#sqPessoaSgdoce', '#form-endereco-modal').length) {
//                                $('#form-endereco-modal').append('<input type="hidden" name="sqEnderecoSgdoce" id="sqEnderecoSgdoce" value="' + response.content.sqEnderecoSgdoce + '" />');
//                            }

                            $('#form-endereco-modal').off('submit').on('submit', function() {
                            	$('#modalUpload').modal('show');
                            });

                            $('#form-endereco-modal').submit();

                            return true;
                        }

                        Message.showMessage(response);
                        $('#form-endereco').submit();
                    }
                });
            } else {
                $('.alert-error').addClass('hide');
                $('.alert-error', '#modalEndereco').removeClass('hide');
                
                return false;
            }
        });
    },

    init: function(){
        EnderecoModal.initCep();
        EnderecoModal.concluir();
        EnderecoModal.initValidateFile();

        $('#sqTipoEndereco').off('change').on('change', function() {
            $('.error', '#modalEndereco').removeClass('error');
            $('.help-block').removeClass('show').addClass('hide');
            $('.alert-error .close').trigger('click');

        	($(this).val() == 3)
	    		? $('#field-noContato').addClass('show').removeClass('hide')
				: $('#field-noContato').addClass('hide').removeClass('show');
        }).trigger('change');

        if ($('#sqTipoEndereco').val() == 3) {
            $('#field-noContato').addClass('show').removeClass('hide');
        }
    },

    initValidateFile : function() {
        $('#txImagem', '#form-endereco-modal').on('change', function() {
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
                        $('#txImagem').val('');
                } else {
                    input.closest('.control-group').removeClass('error');
                    input.closest('.control-group').find('.help-block')
                        .removeClass('show')
                        .addClass('hide');
                }
            }
        });
    }
}

$(document).ready(function(){
    $.mask.masks.cep.mask = '99.999-999';

    jQuery.validator.addMethod("cep", function(value, element) {
        // Caso o CEP não esteja nesse formato ele é inválido!
        var expr = /^[0-9]{2}\.[0-9]{3}-[0-9]{3}$/;

        if(value.length > 0){
            if(expr.test(value))
                return true;
            else
                return false;
        }else{
            return true;
        }

    }, "CEP inválido.")

    EnderecoModal.init();
});