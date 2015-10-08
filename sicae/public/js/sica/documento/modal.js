DocumentoModal = {
    actionChangeTipoDocumento: function() {
        $('#form-documento-modal div.control-group').not('#form-documento-modal div.control-group:first').hide();

        $('#sqTipoDocumento').change(function() {
            $('#form-documento-modal div.control-group').not('#form-documento-modal div.control-group:first').hide();
            $('#form-documento-modal input, #form-documento-modal select').not('#sqTipoDocumento').attr('disabled', true);

            if ($(this).val()) {
                $('.' + $(this).val()).show();
                $('input.' + $(this).val() + ', select.' + $(this).val()).removeAttr('disabled');
            }
        });

        if ($('#sqTipoDocumento').val()) {
            $('#form-documento-modal div.control-group').not('#form-documento-modal div.control-group:first').hide();
            $('#form-documento-modal input, #form-documento-modal select').not('#sqTipoDocumento').attr('disabled', true);

            $('.' + $('#sqTipoDocumento').val()).show();
            $('input.' + $('#sqTipoDocumento').attr('value') + ', select.' + $('#sqTipoDocumento').attr('value')).removeAttr('disabled');
        }
    },
    initMask: function() {
        $('.numeric').setMask({
            mask: '9',
            type: 'repeat'
        });

        $('.dateBR').setMask('date');
        $('select').removeAttr('multiple');
    },
    initDatePicker: function() {
        loadJs('/js/library/bootstrap-datepicker.js', function() {
		loadJs('/assets/js/components/datepicker.js', function() {
		    var options = {
		        format: 'dd/mm/yyyy',
		        language: 'br'
		    };

	 		$('.datepicker-icon').click(function() {
		            $(this).parents('div.controls').find('.input-small:last').focus();
		            return false;
		        });

		    $('.datepicker').datepicker(options);
		});
        });
    },
    concluir: function() {
        $('.btnAdicionarDocumento').click(function() {
            if ($('#form-documento-modal').valid()) {
                var config = {
                    url: '/principal/documento/save',
                    type: 'post',
                    data: $('#form-documento-modal').serializeArray(),
                    dataType: 'json',
                    success: function(data) {
                        Message.showMessage(data);

                        if (Message.isSuccess(data)) {
                            $('#form-documento').submit();
                        }
                    }
                };

                $.ajax(config);
            } else {
                return false;
            }
        });

        PessoaForm.validateType('#modal-documento', '#sqTipoDocumento', 'documento');
    },
    init: function() {
        DocumentoModal.actionChangeTipoDocumento();
        DocumentoModal.initMask();
        DocumentoModal.initDatePicker();
        DocumentoModal.concluir();
    }

}

$(document).ready(function() {
    DocumentoModal.init();
});