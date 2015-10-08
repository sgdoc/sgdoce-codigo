$(document).ready(function(){
    $('.btnConcluirCapa').click(function() {
    	var update = $('#divEdit').val();
		switch ($('#redirect').val()) {
		case '2':
		    window.location = '/artefato/consultar-artefato/consultar-artefato-padrao/update/'+update+'/view/1';	
			break;
		case '3':
		    window.location = '/artefato/visualizar-artefato/index/sqArtefato/' + $('#sqArtefato').val() +'/update/'+update+'/view/1';	
			break;
		}
    });
});