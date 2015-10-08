formValidacao = {

		init: function(){
	    	if($("#deImagemRodapeHidden").val()){
            	$('.deImagemRodape').removeClass('required');  
	    	}
	        $('#coCepRodape').setMask('99.999-999'); 
	        $('#txTelefoneRodape').setMask('9999-9999'); 
			if ($('#dtPrazo').val()){
		        $('#sqPrazo').val('1');
			}
			if ($('#nuDiasPrazo').val()){
		        $('#sqPrazo').val('2');
			}
			formValidacao.tipoData();
	        $('#sqPrazo').change(function(){
	        	formValidacao.tipoData();
	        });        
  
			formValidacao.diaCorrido();
	        $('#inDias').change(function(){
	        	formValidacao.diaCorrido();
	        });
	        
	        formValidacao.validaCampos();        
	        
	        formValidacao.initTable();
	        
		},
	    
	    diaCorrido: function(){	    	
			switch ($('#inDias').val()) {
			case '1':

		        $('#inDiasCorridos').val(false);
		        $('#sqPrazo').val('2');
		        $('.dvDiasPrazo').removeClass('hidden');
		        $('.dv2-dtPrazo').removeClass('hidden');
				break;
			case '2':
		        $('#sqPrazo').val('2');
		        $('#inDiasCorridos').val(true);
		        $('.dvDiasPrazo').removeClass('hidden');
		        $('.dv2-dtPrazo').removeClass('hidden');
				break;
			}
	    },
		
		tipoData: function(){
			switch ($('#sqPrazo').val()) {			
			case '1':
				$('.dvDataPrazo').show();
				$('.dvDiasPrazo').hide();
		        $('#inDiasCorridos').val(null);
		        $('#nuDiasPrazo').val(null);
				break;
			case '2':		
				$('.dvDataPrazo').hide();
				$('.dvDiasPrazo').show();
		        $('#dtPrazo').val('');
				break;
			default:
				$('.dvDataPrazo').hide();
				$('.dvDiasPrazo').hide();
		        $('#inDiasCorridos').val(null);
				break;
			} 
		},
		

				
		initTable: function(){
		    Grid.load('/artefato/minuta-eletronica/list-destinatario-externo/sqArtefato/'+ $('#sqArtefato').val() , $('#table-destinatario-externo'));		    
		    Grid.load('/artefato/minuta-eletronica/list-destinatario-interno/sqArtefato/'+ $('#sqArtefato').val() , $('#table-destinatario-interno'));
		    Grid.load('/artefato/minuta-eletronica/list-assinatura/sqArtefato/'+ $('#sqArtefato').val()+'/sqPessoaFuncao/6' , $('#table-assinatura'));
		    Grid.load('/artefato/pessoa/list-interessado/sqArtefato/'+$('#sqArtefato').val()+'/sqPessoaFuncao/4', $('#table-interessado'));
		},
		
		validaCampos: function(){


		},
			
}

Modal = {
	    init: function(){
	    	$('#btnAdicionarDestin').click(function(){
	    	    if($('#form-cadastrar-artefato').valid()){
	    	        if(!$('#form-cadastrar-artefato').val()){
	    	            $.get('/artefato/minuta-eletronica/destinatario-externo-modal/sqTipoDocumento/'+$('#sqTipoDocumento').val() + '/sqAssunto/'+$('#sqAssunto').val(), function(data){
	    	                $('#modal-destinatario').html(data).modal();
	    	            });
	    	        }else{
	    	            $('#form-cadastrar-artefato').submit();
	    	        }
	    	    }
	    	});
	    	
	    	$('#btnAdicionarDestinatarioInterno').click(function(){
	    	    if($('#form-cadastrar-artefato').valid()){
	    	        if(!$('#form-cadastrar-artefato').val()){
	    	            $.get('/artefato/minuta-eletronica/destinatario-interno-modal/sqTipoDocumento/'+$('#sqTipoDocumento').val() + '/sqAssunto/'+$('#sqAssunto').val(), function(data){
	    	                $('#modal-destinatario-interno').html(data).modal();
	    	            });
	    	        }else{
	    	            $('#form-cadastrar-artefato').submit();
	    	        }
	    	    }
	    	});
	    	
	        $('#btnAdicionarInteressado').click(function(){
	        	if($('#form-cadastrar-artefato').valid()){
	    	        if(!$('#form-cadastrar-artefato').val()){
	    	        	$.get('/artefato/minuta-eletronica/interessado-modal/sqTipoDocumento/'+$('#sqTipoDocumento').val() + '/sqAssunto/'+$('#sqAssunto').val(), function(data){
	    	                $('#modal-interessado').html(data).modal();
	    	            });
	    	        }else{
	    	            $('#form-cadastrar-artefato').submit();
	    	        }
	    	    }
	         });    
	        
	        $('#btnAdicionarAssinatura').click(function(){
	        	if($('#form-cadastrar-artefato').valid()){
	    	        if(!$('#form-cadastrar-artefato').val()){
	    	            $.get('/artefato/minuta-eletronica/assinatura-modal/sqTipoDocumento/'+$('#sqTipoDocumento').val() + '/sqAssunto/'+$('#sqAssunto').val(), function(data){
	    	                $('#modal-assinatura').html(data).modal();
	    	            });
	    	        }else{
	    	            $('#form-cadastrar-artefato').submit();
	    	        }
	    	    }
	         });   
	    }
}
$(document).ready(function(){
	formValidacao.init();
	Modal.init();
});
