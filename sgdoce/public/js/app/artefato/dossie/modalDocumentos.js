$(document).ready(function(){
    $('#inOriginal').change(function() {
        $('#nuDigitalDocumento').val('');
        $('#nuDigitalDocumento').removeAttr('name');
        $('#nuDigitalDocumento').removeAttr('autocomplete');
        $('#nuDigitalDocumento').attr('name', 'nuDigitalDocumento');
        $('#nuDigitalDocumento_hidden').remove();
        
        var url = "/artefato/dossie/find-numero-digital/inOriginal/"+ $('#inOriginal').val() +"/extraParam/"
        $('#nuDigitalDocumento').simpleAutoComplete(url, {
            extraParamFromInput: '#sqTipoArtefato',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    });
    $('#btn-documento').click(function() {
        if( !$('form').valid() ){        	
            $('.campos-obrigatorios').remove();
            return false;
        }
        $.post('/artefato/dossie/documentos', {
            nuDigital:                   $('#nuDigitalDocumento').val(),
            sqTipoVinculoArtefato:       $('#sqTipoVinculoArtefato').val(),
            sqArtefato:                  $('#sqArtefato').val(),
            inOriginal:                  $('#inOriginal').val()
        },function(data){
        	if(data.sucess == 'true'){
                Message.showSuccess(UI_MSG['MN013']);
                $('#table-dados-documentos').dataTable().fnDraw(false);
        	}else if(data.sucess == 'false'){
                Message.showAlert('Item já incluído na lista.');
                $(".bootbox .btn").click(function(){
                	$("#modalDocumentos").modal();
                });
                return false;
        	}
    	});    
    });
});