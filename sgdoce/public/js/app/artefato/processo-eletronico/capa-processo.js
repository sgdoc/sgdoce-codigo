$(document).ready(function(){
    $('.btnConcluirCapa').click(function() {
    	if( $('#divEdit').val() == '1'){
    		switch ($('#redirect').val()) {
			case '2':
			    window.location = '/artefato/consultar-artefato/consultar-artefato-padrao/update/1/view/1';	
				break;
			case '3':
			    window.location = '/artefato/visualizar-artefato/index/sqArtefato/' + $('#sqArtefato').val() +'/update/1/view/1';	
				break;
			}
    	}else{
        	$("#modal-termo-renumerado").load('/artefato/processo-eletronico/termo-renumeracao/sqArtefato/'+$('#sqArtefato').val()).modal();
    	}
    });
});
