$(document).off('ready');
$(document).on('ready', function() {
	
	$('#btnModalMatrizFilial').on('click', function(e) {
		var self = this;
		
		e.stopPropagation();
		
        $.get(
    		'/auxiliar/pessoa-juridica/visualizar-matriz-filial', 
    		{
    			sqPessoa: $('#sqPessoa', '#form-dados-basicos').val(),
    			nuCnpj: $('#nuCnpj', '#form-dados-basicos').val()
    		}, 
    		function(response){
    			if(!$('#sqPessoa', '#form-dados-basicos').val()) {
//					$('#modalMatrizFilial .btn-print').addClass('hide');
    			}
    			
    			$($(self).attr('href')).find('.modal-body').html(response);
    			$($(self).attr('href')).modal('show');
    		}
		);
		
		return false;
	});
});