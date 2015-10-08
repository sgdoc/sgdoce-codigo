$(document).ready(function(){
	//inicia tema
    $(".btnConcluirTema").click(function(){
        if($('#form-tema-tratado-modal').valid()) {
            $.post('/artefato/processo-eletronico/add-tema-tratado', {
                sqArtefato: $('#sqArtefato').val(),
                sqTemaVinculado: $('#sqTemaVinculado').val(),
                noNomeEspecifico: $('#sqNomeEspecifico').val(),
                sqNomeEspecifico: $('#sqNomeEspecifico_hidden').val()
            },
            function(data){
            	if(data.sucess == 'true'){
                    Message.showAlert('Item já incluído na lista.');
                    $(".bootbox .btn").click(function(){
                    	$('#modal-tema-tratado').modal();
                    });
                    return false;
            	}else if(data.sucess == 'false'){
                    $('#sqTemaVinculado').val('');
                    $('#sqNomeEspecifico').val('');
                    $('#sqNomeEspecifico_hidden').val('');
                    $(".nome-especifico").addClass('hidden');
                    $('.div-tema').addClass('hidden');
	                Message.showSuccess(UI_MSG['MN013']);
                    TemaTratado.reloadGrid();
            	}
            });
        }else{
            $('.campos-obrigatorios').remove();
            return false;
        }        
   });
    
	$("#sqTemaVinculado").change(function(){
        if ($(this).val() != '') {
            $(".nome-especifico").removeClass('hidden');
            
            switch ($(this).val()) {
                case '0' : 
                    $('.div-tema').addClass('hidden');
                    $('.tema').removeClass('hidden');
                    break;
                case '1' :
                    $('.div-tema').addClass('hidden');
                    $('.tema').removeClass('hidden');
                    break;
                case '2' : 
                    $('.div-tema').addClass('hidden');
                    $('.tema').removeClass('hidden');
                    break;
                case '3' : 
                    $('.div-tema').addClass('hidden');
                    $('.tema').removeClass('hidden');
                    break;
            }
            return true;
        }
        $(".nome-especifico").addClass('hidden');
        
    });
    
    // autocomplete para o tema caverna selecionado
    $('#sqNomeEspecifico').simpleAutoComplete("/artefato/processo-eletronico/canie-caverna-auto-complete", {
        extraParamFromInput: '#sqTemaVinculado',
    	autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
});