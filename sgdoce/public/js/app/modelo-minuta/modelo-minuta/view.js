ModalMinuta = {   		

	initModalDestinatario: function(){
        $('#btnProximo, #btnSalvar').click(function(){
            if($('#form-pessoa').valid()){
                if(!$('#form-cadastro-modelo-minuta #nuCpf').val()){
                    $.get('/principal/pessoa-fisica/justificativa', function(data){
                        $('#modal-justificativa').html(data).modal();
                    });
                }else{
                    $('#form-cadastro-modelo-minuta').submit();
                }
            }
        });
    },
    
    init: function(){
    	MenuMinuta.initModalDestinatario();
    }
}

$(document).ready(function(){
	ModalMinuta.init();
});