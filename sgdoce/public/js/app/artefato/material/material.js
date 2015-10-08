var Material = {

    materialAutoComplete: function(){
        $('#nuDigitalMaterial').simpleAutoComplete("/artefato/artefato/find-numero-digital", {
            extraParamFromInput: '#sqTipoArtefatoMaterial',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#nuDigitalMaterial').focus(function(){
            if($('#nuDigitalMaterial').val() == '') return false;
            $.ajax({
                type: "POST",
                url: "/artefato/artefato/search-texto-complementar",
                data: {
                    nuDigitalMaterial: $('#nuDigitalMaterial').val()
                }
            }).done(function( result ) {
                $("#txAssuntoComplementarMaterial").html(result);
            });
        });
    },

    materialModal: function(){
        $('#btnAdicionarMaterial').click(function(){
            $("#modal-material").load('/artefato/documento/modal-material/sqArtefato/'+$('#sqArtefato').val()).modal();
        });
    },

	materialFuncoes: function(){
	    $('#concluirUpload').click(function(){
            if (Material.validaTamanhoArquivo()){
                if($('#formModalMaterial').valid()) {
                    $('#formModalMaterial').ajaxForm({
                        success: function(data) {
                            if (data.success) {
                                Material.hideModal();
                                Material.reloadGrid();
                                Message.showSuccess(UI_MSG['MN013']);
                            } else {
                                var erros = "";

                                for (var erro in data.errors) {
                                    erros += data.errors[erro] + '\n';
                                }
//		                    Message.showAlert(erros);
                                $('#error-material').html('<div class="alert alert-error campos-obrigatorios">'+
                                    '<button class="close" data-dismiss="alert">×</button>'+erros+'</div>');
                                return false;
                            }
                        },
//				    error : function(){
//		                $('#error-material').html('<div class="alert alert-error campos-obrigatorios">'+
//	                    '<button class="close" data-dismiss="alert">×</button>O tamanho do arquivo é superior ao permitido. O tamanho máximo permitido é 100Mb.</div>');
//		                return false;
//				    },
                        dataType: 'json',
                        resetForm: true
                    }).submit();
                }else{
                    $('.campos-obrigatorios').remove();
                    return false;
                }
            }
	    });
	},

	hideModal : function() {
        $('#modal-material').modal('hide');
    },

    reloadGrid: function() {
        $('#table-dados-material').dataTable().fnDraw(false);
    },

    grid: function(){
        Grid.load('/artefato/documento/list-material/sqArtefato/'+$('#sqArtefato').val(), $('#table-dados-material'));
    },

    deletar:function(sqAnexoArtefatoVinculo){
        var callBack = function() {
            $.get('artefato/documento/delete-anexo-artefato/id/' + sqAnexoArtefatoVinculo , function(){
            	$('#table-dados-material').dataTable().fnDraw(false);
            });
        }
        Message.showConfirmation({
            'body': UI_MSG['MN018'],
            'yesCallback': callBack
        });
    },

    OutroTipo: function(){
    	$('#sqTipoAnexo').change(function(){
    		if($('#sqTipoAnexo').val() == 11){
    			$('#outroTipo').removeClass("hidden");
    			$('#txOutroTipo').addClass("required");
    		}else{
    			$('#outroTipo').addClass("hidden");
    			$('#txOutroTipo').removeClass("required");
    		}
    	});
    },

    validaTamanhoArquivo:function(){
        var tamanho = $('#arquivoFile')[0].files[0].size;
        if (tamanho > (25 * 1000 * 1000)) {
            Message.showError('O tamanho do arquivo é superior ao permitido. O tamanho máximo permitido é 25Mb.')
            return false;
        } else {
            Message.showSuccess(UI_MSG['MN014']);
            return true;
        }
    }
}