$(document).ready(function(){

    $('#cancelar').click(function(){
        window.location = 'modelo-minuta/modelo-minuta';
    });
    $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchAssunto/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'class',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
    $('#sqTipoDocumento').simpleAutoComplete("/auxiliar/tipodoc/search-tipo-documento/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'class',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

//    if ($('#sqCabecalho').val() == ''){
//		   jQuery.each($('.tipoCabecalho'), function() {
//	   		   $(this).attr('disabled','disabled');
//	   		   });
//    }
//    $('#sqCabecalho').change(function(){
//	   	 if($('#sqCabecalho').val() == 0){
//	   		   jQuery.each($('.tipoCabecalho'), function() {
//	   		       $(this).removeAttr('checked');
//	   		       $(this).attr('disabled','disabled');
//	   		   });
//	   	  }else{
//	   		   jQuery.each($('.tipoCabecalho'), function() {
//	   			   $(this).removeAttr('disabled');
//	   		   });
//	   	  }                  
//    });
    if ($('#idModeloDocumento').val() == 0)
	{
    	$('.tab-content').removeClass('hidden');
  	
    	$('#sqGrauAcesso').val(1);
    }
    if ((!$('#tipoDocumentoSelecionado').val()) && ($('#idModeloDocumento').val() == 0)){
    	$('#formButtonsPrincipal').addClass('hidden');  
    }else{
    	$('#formButtonsPrincipal').removeClass('hidden');  
    }
    jQuery.each($('.tipoDocumento'), function() {
        if( ($(this).val() == 104) || ($(this).val() == 105) || ($(this).val() == 106 ) ){
        	if($(this).is(':checked')){        		
				$('input[id=sqPadraoModeloDocumentoCam][value=45]').parent().parent().show();
				$('input[id=sqPadraoModeloDocumentoCam][value=79]').parent().parent().show();
				$('input[id=sqPadraoModeloDocumentoCam][value=107]').parent().parent().show();
				$('input[id=sqPadraoModeloDocumentoCam][value=108]').parent().parent().show();
				$('input[id=sqPadraoModeloDocumentoCam][value=108]').attr('checked', true);
			    $('input[id=sqPadraoModeloDocumentoCam][value=79]').attr('checked', true);
			    $('input[id=sqPadraoModeloDocumentoCam][value=79]').attr('readonly', 'readonly');
			    $('input[id=sqPadraoModeloDocumentoCam][value=45]').attr('checked', true);
			    $('input[id=sqPadraoModeloDocumentoCam][value=45]').attr('readonly', 'readonly');
        	}else{
				$('input[id=sqPadraoModeloDocumentoCam][value=45]').parent().parent().hide();
				$('input[id=sqPadraoModeloDocumentoCam][value=79]').parent().parent().hide();
			    $('input[id=sqPadraoModeloDocumentoCam][value=107]').parent().parent().hide();
			    $('input[id=sqPadraoModeloDocumentoCam][value=107]').attr('checked', false);
			    
			    if(!$('input[id=sqPadraoModeloDocumentoCam][value=108]').is(':checked')) {
				    $('input[id=sqPadraoModeloDocumentoCam][value=108]').parent().parent().hide();
				    
				    $('input[id=sqPadraoModeloDocumentoCam][value=108]').attr('checked', false);
			    }
			    
			    $('input[id=sqPadraoModeloDocumentoCam][value=79]').attr('checked', false);
			    $('input[id=sqPadraoModeloDocumentoCam][value=45]').attr('checked', false);
        	}
        }
    });
    
    $('input[id=sqPadraoModeloDocumentoCam][value=103], input[id=sqPadraoModeloDocumentoCam][value=104], input[id=sqPadraoModeloDocumentoCam][value=105], input[id=sqPadraoModeloDocumentoCam][value=106]').click(function(){
    	if($(this).is(':checked')){
			$('input[id=sqPadraoModeloDocumentoCam][value=45]').parent().parent().show();
			$('input[id=sqPadraoModeloDocumentoCam][value=79]').parent().parent().show();
			$('input[id=sqPadraoModeloDocumentoCam][value=107]').parent().parent().show();
			$('input[id=sqPadraoModeloDocumentoCam][value=108]').parent().parent().show();
		    $('input[id=sqPadraoModeloDocumentoCam][value=79]').attr('checked', true);
		    $('input[id=sqPadraoModeloDocumentoCam][value=79]').attr('readonly', 'readonly');
		    $('input[id=sqPadraoModeloDocumentoCam][value=45]').attr('checked', true);
		    $('input[id=sqPadraoModeloDocumentoCam][value=45]').attr('readonly', 'readonly');
		}else{
			$('input[id=sqPadraoModeloDocumentoCam][value=45]').parent().parent().hide();
			$('input[id=sqPadraoModeloDocumentoCam][value=79]').parent().parent().hide();
		    $('input[id=sqPadraoModeloDocumentoCam][value=79]').attr('checked', false);
		    $('input[id=sqPadraoModeloDocumentoCam][value=45]').attr('checked', false);
		    $('input[id=sqPadraoModeloDocumentoCam][value=107]').parent().parent().hide();
		    $('input[id=sqPadraoModeloDocumentoCam][value=107]').attr('checked', false);
		    $('input[id=sqPadraoModeloDocumentoCam][value=108]').parent().parent().hide();
		    $('input[id=sqPadraoModeloDocumentoCam][value=108]').attr('checked', false);
		}
	}); 

    $('#btnProximo1').click(function(){ 
        if($('form').valid()){
            $('.campos-obrigatorios').addClass('hidden');
        }
    });
    $('#btnProximo1').click(function(){
        if ($('#sqPadraoModeloDocumento').val() != "" && $('#sqTipoDocumento').val() != "") {
      
            $.post('/modelo-minuta/modelo-minuta/valida-documento', {
            	sqPadraoModeloDocumento: $('#sqPadraoModeloDocumento').val(),
                sqTipoDocumento: $('#sqTipoDocumento_hidden').val(),
                sqAssunto: $('#sqAssunto_hidden').val()
            }, 
            function(data){
            	if (data['sqModeloDocumento'] != ''){
                    var callBack = function(){
                        window.location = '/modelo-minuta/modelo-minuta/edit/id/'+data['sqModeloDocumento']+'/sqPadraoModeloDocumento/'+data['sqPadraoModeloDocumento'];
                    }

                    Message.showConfirmation({
                        'body': 'Modelo de Minuta já cadastrado para esse tipo/assunto!. Deseja Alterar as informações ?',
                        'yesCallback': callBack
                    });
            	}else{
            		//redirecionar para a pagina correta
                	window.location = '/modelo-minuta/modelo-minuta/create/sqPadraoModeloDocumento/' 
                	+ $('#sqPadraoModeloDocumento').val() + '/sqTipoDocumento/' + $('#sqTipoDocumento_hidden').val() + '/sqAssunto/' 
                	+ $('#sqAssunto_hidden').val();  
            	}
            });
         
        }
    })

    $('.allCampo,.interno, .externo').hide();
	var all = $('.interno, .externo');
	var inputs = $('.destino input, .interno input, .externo input');
	
	$('#destNao').on('click', function(){
		$('.destino').hide();
		$('.allCampo').hide();
		$(all).hide();
		$('.allCampo input').attr('checked', false);
		$(inputs).attr('checked', false);
	    $('input[id=sqPadraoModeloDocumentoCam][value=75]').parent().parent().hide();	
	    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().hide();	
	    $('input[id=sqPadraoModeloDocumentoCam][value=75]').attr('checked', false);
	    $('input[id=sqPadraoModeloDocumentoCam][value=76]').attr('checked', false);
	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').parent().parent().hide();	
	    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().hide();	
	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').attr('checked', false);
	    $('input[id=sqPadraoModeloDocumentoCam][value=42]').attr('checked', false);	    
	});
	
	$('#destSim').on('click', function(){
		$('.destino').show();
		$(all).hide();
		$(inputs).attr('checked', false);
		$('#destInterno').click();
		$('#variasViasNao').click();
	});
    
	$('#destInterno').on('click', function(){
		$(all).hide();
		$('#variasViasNao').click();
		$('.interno').show();
		$('.allCampo').show();
		$('.allCampo input').attr('checked', false);
		$('.externo input').attr('checked', false);
		
	    CampoTratamento.Tratamento();
		
	    jQuery.each($('.tipoDocumento'), function() {
	    	//padrão Oficio
	        if($(this).val() == 95 
	           || $(this).val() == 101 || $(this).val() == 104
	           || $(this).val() == 68  || $(this).val() == 69
	           || $(this).val() == 93 || $(this).val() == 92 || $(this).val() == 91 || $(this).val() == 65 || $(this).val() == 66 
	           //padrão Geral
	           || $(this).val() == 102 || $(this).val() == 90 || $(this).val() == 88 
	           || $(this).val() == 89 || $(this).val() == 94 || $(this).val() == 103  || $(this).val() == 35 || $(this).val() == 36 
	           || $(this).val() == 32 || $(this).val() == 33 || $(this).val() == 66 || $(this).val() == 31 || $(this).val() == 64 || $(this).val() == 112 || $(this).val() == 113
	           ){
	            $(this).parent().parent().hide();      
	        }
	        //padrão Oficio e padrão Geral
	        if($(this).val() == 67 || $(this).val() == 111 || $(this).val() == 110){
	            $(this).parent().parent().show();         
	        }
	        if($(this).val() == 110 || $(this).val() == 111 || $(this).val() == 31 || $(this).val() == 64){
	        	$(this).parent().parent().show(); 
	            $('input[id=sqPadraoModeloDocumentoCam][value=110]').attr('readonly', 'readonly');
	        	$('input[id=sqPadraoModeloDocumentoCam][value=110]').attr('checked', true);       
	            $('input[id=sqPadraoModeloDocumentoCam][value=111]').attr('readonly', 'readonly');
	        	$('input[id=sqPadraoModeloDocumentoCam][value=111]').attr('checked', true);  
	        }
	        
    	});
	});
	
	$('#destExterno').on('click', function(){
		$(all).hide();
		$('#variosDestinosNao').click();
		$('.externo').show();
		$('.allCampo').show();
		$('.allCampo input').attr('checked', false);
		$('.interno input').attr('checked', false);
	    $('input[id=sqPadraoModeloDocumentoCam][value=68]').attr('checked', true);
	    $('input[id=sqPadraoModeloDocumentoCam][value=69]').attr('checked', true);
	    $('input[id=sqPadraoModeloDocumentoCam][value=35]').attr('checked', true);
	    $('input[id=sqPadraoModeloDocumentoCam][value=36]').attr('checked', true);  
	    
		if($('input[id=sqPadraoModeloDocumentoCam][value=31]').length) {
			$('input[id=sqPadraoModeloDocumentoCam][value=32]').parent().parent().after($('input[id=sqPadraoModeloDocumentoCam][value=112]').parent().parent());
		}
		
		if($('input[id=sqPadraoModeloDocumentoCam][value=64]').length) {
			$('input[id=sqPadraoModeloDocumentoCam][value=65]').parent().parent().after($('input[id=sqPadraoModeloDocumentoCam][value=113]').parent().parent());
		}
	    
	    jQuery.each($('.tipoDocumento'), function() {
	    	//padrão Oficio
	        if($(this).val() == 66 || $(this).val() == 95
	           || $(this).val() == 101 || $(this).val() == 104
	           || $(this).val() == 111 || $(this).val() == 93 || $(this).val() == 92 || $(this).val() == 91
	           //padrão Geral
	           || $(this).val() == 102 || $(this).val() == 90 || $(this).val() == 88 
	           || $(this).val() == 89 || $(this).val() == 94 || $(this).val() == 103 || $(this).val() == 110 || $(this).val() == 111
	           || $(this).val() == 33 || $(this).val() == 31 || $(this).val() == 64
	        	){
	            $(this).parent().parent().hide();         
	        }
	        //padrão Oficio e padrão Geral
	        if ($(this).val() == 69 || $(this).val() == 68 || $(this).val() == 35 || $(this).val() == 36 || $(this).val() == 65 
	        		|| $(this).val() == 32 || $(this).val() == 112   || $(this).val() == 113){
	            $(this).parent().parent().show();    	
	        }
	        if($(this).val() == 110 || $(this).val() == 111 || $(this).val() == 31 || $(this).val() == 64){
	        	 $(this).parent().parent().hide(); 
	            $('input[id=sqPadraoModeloDocumentoCam][value=110]').attr('readonly', 'readonly');
	        	$('input[id=sqPadraoModeloDocumentoCam][value=110]').attr('checked', false);
	            $('input[id=sqPadraoModeloDocumentoCam][value=111]').attr('readonly', 'readonly');
	        	$('input[id=sqPadraoModeloDocumentoCam][value=111]').attr('checked', false);
	        }
	    });		
    
	});
	if($('#idModeloDocumento').val() != '0'){
		switch($('#sqPadraoModeloDocumento').val()){
			case '1':
				$('#divTitulo').text('Alterar Padrão Atos');
			break;
			case '2':
				$('#divTitulo').text('Alterar Padrão Geral');
			break;
			case '3':
				$('#divTitulo').text('Alterar Padrão Ofício');
	     	    $('#sqPosicaoTipoDocumento').attr('disabled', 'disabled');
			break;	
		}
	}else{
		switch($('#sqPadraoModeloDocumento').val()){
			case '1':
				$('#divTitulo').text('Cadastrar Padrão Atos');
			break;
			case '2':
				$('#divTitulo').text('Cadastrar Padrão Geral');
			break;
			case '3':
				$('#divTitulo').text('Cadastrar Padrão Ofício');
	     	    $('#sqPosicaoTipoDocumento').attr('disabled', 'disabled');
			break;	
			default:
				$('#divTitulo').text('Cadastrar Modelo de Minuta');
			break;	
		}
	}

    if ($('#destExterno').is(':checked')){
		$('.externo').show();
		$('.allCampo').show();
	    jQuery.each($('.tipoDocumento'), function() {
	    	 //padrão Oficio
	        if($(this).val() == 66 || $(this).val() == 95
	           || $(this).val() == 101 || $(this).val() == 104
	           || $(this).val() == 111 || $(this).val() == 93 || $(this).val() == 92 || $(this).val() == 91
	         //padrão Geral
	           || $(this).val() == 31 || $(this).val() == 64 ||  $(this).val() == 102 || $(this).val() == 90 || $(this).val() == 88 
	           || $(this).val() == 89 || $(this).val() == 94 || $(this).val() == 103 || $(this).val() == 110 || $(this).val() == 111
	           || $(this).val() == 33 || $(this).val() == 66){
	            $(this).parent().parent().hide();         
	        }
	           //padrão Oficio e padrão Geral
	        if ($(this).val() == 69 || $(this).val() == 68 || $(this).val() == 35 || $(this).val() == 36 || $(this).val() == 112  || $(this).val() == 113){
	            $(this).parent().parent().show();   

	        }
		    });
    }

    $('input[id=sqPadraoModeloDocumentoCam][value=110],input[id=sqPadraoModeloDocumentoCam][value=111]').click(function(){
    	return false;
    });
    
    if ($('#destInterno').is(':checked')){
		$('.interno').show();
		$('.allCampo').show();
		
		if($('input[id=sqPadraoModeloDocumentoCam][value=31]').length) {
			$('input[id=sqPadraoModeloDocumentoCam][value=31]').parent().parent().after($('input[id=sqPadraoModeloDocumentoCam][value=112]').parent().parent());
		}
		
		if($('input[id=sqPadraoModeloDocumentoCam][value=64]').length) {
			$('input[id=sqPadraoModeloDocumentoCam][value=64]').parent().parent().after($('input[id=sqPadraoModeloDocumentoCam][value=113]').parent().parent());
		}
		
	    jQuery.each($('.tipoDocumento'), function() {
	           //padrão Oficio
	        if($(this).val() == 95 
	           || $(this).val() == 101 || $(this).val() == 104
	           || $(this).val() == 68  || $(this).val() == 69 || $(this).val() == 93 || $(this).val() == 92 || $(this).val() == 91
	           || $(this).val() == 65
	           //padrão Geral
	           || $(this).val() == 102 || $(this).val() == 90 || $(this).val() == 88 
	           || $(this).val() == 89 || $(this).val() == 94 || $(this).val() == 103  || $(this).val() == 35 || $(this).val() == 36 
	           || $(this).val() == 32 || $(this).val() == 33 || $(this).val() == 66 || $(this).val() == 31 || $(this).val() == 64 
	           ){
	            $(this).parent().parent().hide();
	        }
	           //padrão Oficio e padrão Geral
	        if($(this).val() == 67 || $(this).val() == 110 || $(this).val() == 111){
	            $(this).parent().parent().show();    
	        }
	        
	        if ($(this).val() == 110 || $(this).val() == 111){	        	
	            $('input[id=sqPadraoModeloDocumentoCam][value=110]').attr('readonly', 'readonly');
	        	$('input[id=sqPadraoModeloDocumentoCam][value=110]').attr('checked', true);
	            $('input[id=sqPadraoModeloDocumentoCam][value=111]').attr('readonly', 'readonly');
	        	$('input[id=sqPadraoModeloDocumentoCam][value=111]').attr('checked', true);
	    		if($(this).is(':checked')){	    			
	    			
	    			$('input[id=sqPadraoModeloDocumentoCam][value=64]').parent().parent().show();
	    			$('input[id=sqPadraoModeloDocumentoCam][value=31]').parent().parent().show();
	    		}else{
	    			$('input[id=sqPadraoModeloDocumentoCam][value=64]').parent().parent().hide();
	    			$('input[id=sqPadraoModeloDocumentoCam][value=31]').parent().parent().hide();
	    		}
	    	}
	        if ($(this).val() == 64 || $(this).val() == 31) {
	    		if($(this).is(':checked')){
	    			$('input[id=sqPadraoModeloDocumentoCam][value=113]').parent().parent().show();
	    			$('input[id=sqPadraoModeloDocumentoCam][value=112]').parent().parent().show();
	    		}else{
	    			$('input[id=sqPadraoModeloDocumentoCam][value=113]').parent().parent().hide();
	    			$('input[id=sqPadraoModeloDocumentoCam][value=112]').parent().parent().hide();
	    		}
	    	}
    	});
    }

     $('input[id=sqPadraoModeloDocumentoCam][value=110],input[id=sqPadraoModeloDocumentoCam][value=111]').click(function(){
    	if($(this).is(':checked') && $('#destInterno').is(':checked')){
    	    $('input[id=sqPadraoModeloDocumentoCam][value=31]').parent().parent().show();	
    	    $('input[id=sqPadraoModeloDocumentoCam][value=64]').parent().parent().show();	
    	}else{
//    	    $('input[id=sqPadraoModeloDocumentoCam][value=31]').parent().parent().hide();	
 			$('input[id=sqPadraoModeloDocumentoCam][value=112]').parent().parent().hide();
 		    $('input[id=sqPadraoModeloDocumentoCam][value=31]').attr('checked', false);
 		    $('input[id=sqPadraoModeloDocumentoCam][value=112]').attr('checked', false);
 		    
 		    //padrao oficio
//    	    $('input[id=sqPadraoModeloDocumentoCam][value=64]').parent().parent().hide();	
 			$('input[id=sqPadraoModeloDocumentoCam][value=113]').parent().parent().hide();
 		    $('input[id=sqPadraoModeloDocumentoCam][value=64]').attr('checked', false);
 		    $('input[id=sqPadraoModeloDocumentoCam][value=113]').attr('checked', false);
    	}
     });
     
     $('input[id=sqPadraoModeloDocumentoCam][value=31],input[id=sqPadraoModeloDocumentoCam][value=64]').click(function() {
		if($('#destInterno').is(':checked')) {
			if(this.value == '31') {
				$('input[id=sqPadraoModeloDocumentoCam][value=31]').parent().parent().after($('input[id=sqPadraoModeloDocumentoCam][value=112]').parent().parent());
			} else if(this.value == '64') {
				$('input[id=sqPadraoModeloDocumentoCam][value=64]').parent().parent().after($('input[id=sqPadraoModeloDocumentoCam][value=113]').parent().parent());
			}
			
		}
 			
     	if($(this).is(':checked') && $('#destInterno').is(':checked')) {
 			$('input[id=sqPadraoModeloDocumentoCam][value=112]' ).parent().parent().show();
 			$('input[id=sqPadraoModeloDocumentoCam][value=113]' ).parent().parent().show();
 		} else {
 			$('input[id=sqPadraoModeloDocumentoCam][value=112]').parent().parent().hide();
 		    $('input[id=sqPadraoModeloDocumentoCam][value=112]').attr('checked', false);
 		    
 			$('input[id=sqPadraoModeloDocumentoCam][value=113]').parent().parent().hide();
 		    $('input[id=sqPadraoModeloDocumentoCam][value=113]').attr('checked', false);
 		}
 	});
     

    if ($('input[id=sqPadraoModeloDocumentoCam][value=110],input[id=sqPadraoModeloDocumentoCam][value=111]').is(':checked') && $('#destInterno').is(':checked')) {
 	    $('input[id=sqPadraoModeloDocumentoCam][value=31]').parent().parent().show();	
 	    $('input[id=sqPadraoModeloDocumentoCam][value=64]').parent().parent().show();
    }    

    CampoTratamento.Tratamento();
    
    if($('#sqPadraoModeloDocumento').val() == 1){
        var ano = $('#sqPadraoModeloDocumentoCam[value="96"]').closest('.controls');
        $('.dados-ident-doc .controls:eq(0)').after(ano);
    }
    
    $('input[id=sqPadraoModeloDocumentoCam][value=61]').parent().parent().hide();
    $('input[id=sqPadraoModeloDocumentoCam][value=8]').parent().parent().hide();
    $('input[id=sqPadraoModeloDocumentoCam][value=28]').parent().parent().hide();
    $('input[id=sqPadraoModeloDocumentoCam][value=60],input[id=sqPadraoModeloDocumentoCam][value=7],input[id=sqPadraoModeloDocumentoCam][value=27]').click(function(){
    	if($(this).is(':checked')){
     	    $('input[id=sqPadraoModeloDocumentoCam][value=61]').parent().parent().show();
     	    $('input[id=sqPadraoModeloDocumentoCam][value=8]').parent().parent().show();
     	    $('input[id=sqPadraoModeloDocumentoCam][value=28]').parent().parent().show();
    	}else{
     	    $('input[id=sqPadraoModeloDocumentoCam][value=61]').parent().parent().hide();
     	    $('input[id=sqPadraoModeloDocumentoCam][value=8]').parent().parent().hide();
     	    $('input[id=sqPadraoModeloDocumentoCam][value=28]').parent().parent().hide();
    	}
    });
    
    if ( $('input[id=sqPadraoModeloDocumentoCam][value=60],input[id=sqPadraoModeloDocumentoCam][value=7],input[id=sqPadraoModeloDocumentoCam][value=27]').is(':checked') ){
 	    $('input[id=sqPadraoModeloDocumentoCam][value=61]').parent().parent().show();	
 	    $('input[id=sqPadraoModeloDocumentoCam][value=8]').parent().parent().show();
 	    $('input[id=sqPadraoModeloDocumentoCam][value=28]').parent().parent().show();
    }else{
 	    $('input[id=sqPadraoModeloDocumentoCam][value=61]').parent().parent().hide();	
 	    $('input[id=sqPadraoModeloDocumentoCam][value=8]').parent().parent().hide();
 	    $('input[id=sqPadraoModeloDocumentoCam][value=28]').parent().parent().hide();
    }
    
    $('input[id=sqPadraoModeloDocumentoCam][value=39]').parent().parent().hide();
    $('input[id=sqPadraoModeloDocumentoCam][value=72]').parent().parent().hide();
    $('input[id=sqPadraoModeloDocumentoCam][value=38],input[id=sqPadraoModeloDocumentoCam][value=71]').click(function(){
    	if($(this).is(':checked')){
     	    $('input[id=sqPadraoModeloDocumentoCam][value=39]').parent().parent().show();
     	    $('input[id=sqPadraoModeloDocumentoCam][value=72]').parent().parent().show();
    	}else{
    	    $('input[id=sqPadraoModeloDocumentoCam][value=39]').parent().parent().hide();
     	    $('input[id=sqPadraoModeloDocumentoCam][value=72]').parent().parent().hide();
            $('input[id=sqPadraoModeloDocumentoCam][value=39]').attr("checked",false);
            $('input[id=sqPadraoModeloDocumentoCam][value=72]').attr("checked",false);
            
    	}
    });
    
    if ( $('input[id=sqPadraoModeloDocumentoCam][value=38],input[id=sqPadraoModeloDocumentoCam][value=71]').is(':checked') ){
	    $('input[id=sqPadraoModeloDocumentoCam][value=39]').parent().parent().show();
 	    $('input[id=sqPadraoModeloDocumentoCam][value=72]').parent().parent().show();
    }else{
	    $('input[id=sqPadraoModeloDocumentoCam][value=39]').parent().parent().hide();
 	    $('input[id=sqPadraoModeloDocumentoCam][value=72]').parent().parent().hide();
    }
    

    
    
	if($('#destNao').is(':checked') ){
		$('.destino').hide();
	}
    $('#sqPadraoModeloDocumento').change(function(){
        if (!$(this).val() != "") {
            window.location = '/modelo-minuta/modelo-minuta/create';
        }
    });
    if ($('#sqPadraoModeloDocumento').val() != "") {
        $('#nav-tabs').removeClass('hidden');
        $('#tab-content').removeClass('hidden');
    }
    $('input[id=sqPadraoModeloDocumentoCam][value=66]').parent().hide();
    $('input[id=sqPadraoModeloDocumentoCam][value=68]').attr('checked', true);
    $('input[id=sqPadraoModeloDocumentoCam][value=69]').attr('checked', true);
    $('input[id=sqPadraoModeloDocumentoCam][value=68]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=69]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=77]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=77]').attr('checked', true);
    
    $('input[id=sqPadraoModeloDocumentoCam][value=77],input[id=sqPadraoModeloDocumentoCam][value=68]'
    +',input[id=sqPadraoModeloDocumentoCam][value=69],input[id=sqPadraoModeloDocumentoCam][value=35]'
    +',input[id=sqPadraoModeloDocumentoCam][value=36],input[id=sqPadraoModeloDocumentoCam][value=43]'
    +',input[id=sqPadraoModeloDocumentoCam][value=14],input[id=sqPadraoModeloDocumentoCam][value=115]'
    +',input[id=sqPadraoModeloDocumentoCam][value=116],input[id=sqPadraoModeloDocumentoCam][value=117]').click(function(){
    	return false;
    });
    
    //padrao geral    
//    $('input[id=sqPadraoModeloDocumentoCam][value=33]').parent().hide();
//    $('input[id=sqPadraoModeloDocumentoCam][value=33]').attr('checked', true);
    $('input[id=sqPadraoModeloDocumentoCam][value=35]').attr('checked', true);
    $('input[id=sqPadraoModeloDocumentoCam][value=36]').attr('checked', true);   
    $('input[id=sqPadraoModeloDocumentoCam][value=43]').attr('checked', true);   

    $('input[id=sqPadraoModeloDocumentoCam][value=33]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=35]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=36]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=43]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=115]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=116]').attr('readonly', 'readonly');
    $('input[id=sqPadraoModeloDocumentoCam][value=117]').attr('readonly', 'readonly');
    
    //padrao ato    
    $('input[id=sqPadraoModeloDocumentoCam][value=14]').attr('checked', true); 
    $('input[id=sqPadraoModeloDocumentoCam][value=14]').attr('readonly', 'readonly');
    
  //Aba Assinatura    
    if ( $('input[id=sqPadraoModeloDocumentoCam][value=100],input[id=sqPadraoModeloDocumentoCam][value=98],input[id=sqPadraoModeloDocumentoCam][value=99]').is(':checked') ){
		jQuery.each($('.tipoAssinaturaCamp'), function() {
	        if($(this).val() == 80 || $(this).val() == 81 || $(this).val() == 120 || $(this).val() == 83
	        //padrão atos
    		|| $(this).val() == 15 ||	$(this).val() == 16 || $(this).val() == 118 ||	$(this).val() == 18
	        //padrão geral
    		|| $(this).val() == 46 || $(this).val() == 47 || $(this).val() == 119 || $(this).val() == 49
	        ){
 	            $(this).parent().parent().show();
 	        }
	    });
    }else{
		jQuery.each($('.tipoAssinaturaCamp'), function() {
	        if($(this).val() == 80 || $(this).val() == 81 || $(this).val() == 120 || $(this).val() == 83
	        //padrão atos
    		|| $(this).val() == 15 ||	$(this).val() == 16 || $(this).val() == 118 ||	$(this).val() == 18
	        //padrão geral
    		|| $(this).val() == 46 || $(this).val() == 47 || $(this).val() == 119 || $(this).val() == 49
	        ){
 	            $(this).parent().parent().hide();
 	        }
	    });
    }
    
	    $('input[id=sqPadraoModeloDocumentoCam][value=100],input[id=sqPadraoModeloDocumentoCam][value=98],input[id=sqPadraoModeloDocumentoCam][value=99]').click(function(){
		    	if ($(this).is(':checked')){
		    		jQuery.each($('.tipoAssinaturaCamp'), function() {
		    	        if($(this).val() == 80 || $(this).val() == 81 || $(this).val() == 120 || $(this).val() == 83
		    	        //padrão atos
		    	        || $(this).val() == 15 ||	$(this).val() == 16 || $(this).val() == 118 ||	$(this).val() == 18	
		    	        //padrão geral
		        		|| $(this).val() == 46 || $(this).val() == 47 || $(this).val() == 119 || $(this).val() == 49
		    	        ){
		     	            $(this).parent().parent().show();
		     	        }
		    	    });	     	   
		    	}else{
		    		jQuery.each($('.tipoAssinaturaCamp'), function() {
		    	        if($(this).val() == 80 || $(this).val() == 81 || $(this).val() == 82 ||  $(this).val() == 117 || $(this).val() == 120 || $(this).val() == 83
	        			//padrão atos
		    	        || $(this).val() == 15 ||	$(this).val() == 16 || $(this).val() == 118 ||	$(this).val() == 18	 ||	$(this).val() == 17 ||	$(this).val() == 115
		    	        //padrão geral
		        		|| $(this).val() == 46 || $(this).val() == 47 || $(this).val() == 119 || $(this).val() == 49 ||	$(this).val() == 48 ||	$(this).val() == 116
		    	        ){
		     	            $(this).parent().parent().hide();
		     	            $(this).attr('checked', false); 
				    	    $('input[id=sqPadraoModeloDocumentoCam][value=120]').attr('checked', false);   
				    	    $('input[id=sqPadraoModeloDocumentoCam][value=83]').attr('checked', false);   
		     	        }
		    	    });
		    	}
	    });
	    
//	    18 asssin 115 text
	    //geral 49 e 116 text
    if ( $('input[id=sqPadraoModeloDocumentoCam][value=120],input[id=sqPadraoModeloDocumentoCam][value=83],input[id=sqPadraoModeloDocumentoCam][value=118],input[id=sqPadraoModeloDocumentoCam][value=18],input[id=sqPadraoModeloDocumentoCam][value=119],input[id=sqPadraoModeloDocumentoCam][value=49]').is(':checked') ){
		jQuery.each($('.tipoAssinaturaCamp'), function() {
	        if($(this).val() == 117 || $(this).val() == 82
	        //padrão atos
    		|| $(this).val() == 17	|| $(this).val() == 115	
	        //padrão geral
    		|| $(this).val() == 48	|| $(this).val() == 116	
	        ){
 	            $(this).parent().parent().show();
 	        }
	    });
    }else{
		jQuery.each($('.tipoAssinaturaCamp'), function() {
	        if($(this).val() == 117 || $(this).val() == 82
	        //padrão atos
    		|| $(this).val() == 17	|| $(this).val() == 115
	        //padrão geral
    		|| $(this).val() == 48	|| $(this).val() == 116	
	        ){
 	            $(this).parent().parent().hide();
 	        }
	    });
    }

    $('input[id=sqPadraoModeloDocumentoCam][value=120],input[id=sqPadraoModeloDocumentoCam][value=83],input[id=sqPadraoModeloDocumentoCam][value=118],input[id=sqPadraoModeloDocumentoCam][value=18],input[id=sqPadraoModeloDocumentoCam][value=119],input[id=sqPadraoModeloDocumentoCam][value=49]').click(function(){
	    	if ($(this).is(':checked')){
	    		jQuery.each($('.tipoAssinaturaCamp'), function() {
	    	        if($(this).val() == 82
	    	        //padrão atos
	        		|| $(this).val() == 17
	    	        //padrão geral
	        		|| $(this).val() == 48
	    	        ){
	     	            $(this).parent().parent().show();
	     	        }
	    	    });	     	   
	    	}else{
	    		jQuery.each($('.tipoAssinaturaCamp'), function() {
	    	        if($(this).val() == 117 || $(this).val() == 82
	    	        //padrão atos
	        		|| $(this).val() == 17	|| $(this).val() == 115
	    	        //padrão geral
	        		|| $(this).val() == 48	|| $(this).val() == 116	
	    	        ){
	     	            $(this).parent().parent().hide();
	     	        }
	    	    });
	    	}
    });
        
    //assinatura multipla
    $('input[id=sqPadraoModeloDocumentoCam][value=120],input[id=sqPadraoModeloDocumentoCam][value=118]'
	+',input[id=sqPadraoModeloDocumentoCam][value=119]').click(function(){
    	if ($(this).is(':checked')){
     	    $('input[id=sqPadraoModeloDocumentoCam][value=83]').attr('disabled', 'disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=18]').attr('disabled', 'disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=49]').attr('disabled', 'disabled');
     	   
    	}else{
     	    $('input[id=sqPadraoModeloDocumentoCam][value=83]').removeAttr('disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=18]').removeAttr('disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=49]').removeAttr('disabled');
            $('input[id=sqPadraoModeloDocumentoCam][value=82]').removeAttr('checked');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=17]').removeAttr('checked');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=81]').removeAttr('checked');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=48]').removeAttr('checked');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=115]').removeAttr('checked');
    	    $('input[id=sqPadraoModeloDocumentoCam][value=116]').removeAttr('checked');
    	    $('input[id=sqPadraoModeloDocumentoCam][value=117]').removeAttr('checked');

    	}
    });
    if ($('input[id=sqPadraoModeloDocumentoCam][value=120],input[id=sqPadraoModeloDocumentoCam][value=118]'
    		+',input[id=sqPadraoModeloDocumentoCam][value=119]').is(':checked') ){
 	    $('input[id=sqPadraoModeloDocumentoCam][value=83]').attr('disabled', 'disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=18]').attr('disabled', 'disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=49]').attr('disabled', 'disabled');    	     	   
	}else{
 	    $('input[id=sqPadraoModeloDocumentoCam][value=83]').removeAttr('disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=18]').removeAttr('disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=49]').removeAttr('disabled');
	}
     
    
    //assinatura hirerquica
    $('input[id=sqPadraoModeloDocumentoCam][value=83],input[id=sqPadraoModeloDocumentoCam][value=18]'
	+',input[id=sqPadraoModeloDocumentoCam][value=49]').click(function(){
    	if ($(this).is(':checked')){
     	    $('input[id=sqPadraoModeloDocumentoCam][value=120]').attr('disabled', 'disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=118]').attr('disabled', 'disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=119]').attr('disabled', 'disabled');
    	}else{
     	    $('input[id=sqPadraoModeloDocumentoCam][value=120]').removeAttr('disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=118]').removeAttr('disabled');
     	    $('input[id=sqPadraoModeloDocumentoCam][value=119]').removeAttr('disabled');
     	    
     	    $('input[id=sqPadraoModeloDocumentoCam][value=17]').removeAttr('checked');     	    
            $('input[id=sqPadraoModeloDocumentoCam][value=82]').removeAttr('checked');
            
     	    $('input[id=sqPadraoModeloDocumentoCam][value=115]').removeAttr('checked');
    	    $('input[id=sqPadraoModeloDocumentoCam][value=116]').removeAttr('checked');
    	    $('input[id=sqPadraoModeloDocumentoCam][value=117]').removeAttr('checked');
    	}
    });
    
	$('input[id=sqPadraoModeloDocumentoCam][value=116]').parent().parent().hide();
	$('input[id=sqPadraoModeloDocumentoCam][value=117]').parent().parent().hide();

    //motivação
    $('input[id=sqPadraoModeloDocumentoCam][value=17],input[id=sqPadraoModeloDocumentoCam][value=48],input[id=sqPadraoModeloDocumentoCam][value=82],input[id=sqPadraoModeloDocumentoCam][value=45]'
    		).click(function(){
    	    	if ($(this).is(':checked')){
    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=115]').attr('checked', 'checked');
    	    		$('input[id=sqPadraoModeloDocumentoCam][value=115]').parent().parent().show();
    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=116]').attr('checked', 'checked');
    	    		$('input[id=sqPadraoModeloDocumentoCam][value=116]').parent().parent().show();
    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=117]').attr('checked', 'checked');
    	    		$('input[id=sqPadraoModeloDocumentoCam][value=117]').parent().parent().show();
//    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=115]').removeAttr('disabled');
//    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=116]').removeAttr('disabled');
//    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=117]').removeAttr('disabled');
    	    	}else{
    	    		$('input[id=sqPadraoModeloDocumentoCam][value=115]').parent().parent().hide();
    	    		$('input[id=sqPadraoModeloDocumentoCam][value=116]').parent().parent().hide();
    	    		$('input[id=sqPadraoModeloDocumentoCam][value=117]').parent().parent().hide();
    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=115]').removeAttr('checked');
    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=116]').removeAttr('checked');
    	    	    $('input[id=sqPadraoModeloDocumentoCam][value=117]').removeAttr('checked');
    	    	}
    });
    
    if ( $('input[id=sqPadraoModeloDocumentoCam][value=17],input[id=sqPadraoModeloDocumentoCam][value=48],input[id=sqPadraoModeloDocumentoCam][value=82]').is(':checked') ){
		jQuery.each($('.tipoAssinaturaCamp'), function() {
	        if($(this).val() == 115
	        //padrão atos
    		|| $(this).val() == 116 	
	        //padrão geral
    		|| $(this).val() == 117
	        ){
	        	$(this).attr('checked','checked');
 	            $(this).parent().parent().show();
 	        }
	    });
    }else{
		jQuery.each($('.tipoAssinaturaCamp'), function() {
	        if($(this).val() == 115
	        //padrão atos
    		|| $(this).val() == 116
	        //padrão geral
    		|| $(this).val() == 117
	        ){
	        	$(this).removeAttr('checked');
 	            $(this).parent().parent().hide();
 	        }
	    });
    }
	if ($('input[id=sqPadraoModeloDocumentoCam][value=83],input[id=sqPadraoModeloDocumentoCam][value=18]'
    		+',input[id=sqPadraoModeloDocumentoCam][value=49]').is(':checked') ){
 	    $('input[id=sqPadraoModeloDocumentoCam][value=120]').attr('disabled', 'disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=117]').attr('disabled', 'disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=118]').attr('disabled', 'disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=119]').attr('disabled', 'disabled');
    }else{
 	    $('input[id=sqPadraoModeloDocumentoCam][value=120]').removeAttr('disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=118]').removeAttr('disabled');
 	    $('input[id=sqPadraoModeloDocumentoCam][value=119]').removeAttr('disabled');
    }
	$('.legendHelp37,.legendHelp39,.legendHelp70,.legendHelp72').show();
   	$('.legendHelp37').prepend('"Esta informação será exibida no documento."');
   	$('.legendHelp39').prepend('"Esta informação será exibida no campo Assunto do documento."');
   	$('.legendHelp70').prepend('"Esta informação será exibida no documento."');
   	$('.legendHelp72').prepend('"Esta informação será exibida no campo Assunto do documento."');
   	

	$('input[id=sqPadraoModeloDocumentoCam][value=79],input[id=sqPadraoModeloDocumentoCam][value=45]').click(function(){
		return false;
	});
	
});

CampoTratamento = {
		Tratamento: function(){
		    $('input[id=sqPadraoModeloDocumentoCam][value=75]').parent().parent().hide();	
		    $('input[id=sqPadraoModeloDocumentoCam][value=41]').parent().parent().hide();
		    $('input[id=sqPadraoModeloDocumentoCam][value=32],input[id=sqPadraoModeloDocumentoCam][value=65]').click(function(){
		    	if($(this).is(':checked')){
			    	$('input[id=sqPadraoModeloDocumentoCam][value=75]').parent().parent().show();	
			 	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').parent().parent().show();
		    	}else{
		    	    $('input[id=sqPadraoModeloDocumentoCam][value=75]').attr('checked', false);   
		    	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').attr('checked', false);   
			    	$('input[id=sqPadraoModeloDocumentoCam][value=75]').parent().parent().hide();	
			 	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').parent().parent().hide(); 
				    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().hide();
				    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().hide();   
				    $('input[id=sqPadraoModeloDocumentoCam][value=42]').attr('checked', false);
				    $('input[id=sqPadraoModeloDocumentoCam][value=76]').attr('checked', false);
		    	}
		    });
		    
		    if ( $('input[id=sqPadraoModeloDocumentoCam][value=32],input[id=sqPadraoModeloDocumentoCam][value=65]').is(':checked') ){
		 	    $('input[id=sqPadraoModeloDocumentoCam][value=75]').parent().parent().show();	
		 	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').parent().parent().show();
		    }else{
	    	    $('input[id=sqPadraoModeloDocumentoCam][value=75]').attr('checked', false);   
	    	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').attr('checked', false);   
		    	$('input[id=sqPadraoModeloDocumentoCam][value=75]').parent().parent().hide();	
		 	    $('input[id=sqPadraoModeloDocumentoCam][value=41]').parent().parent().hide(); 
			    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().hide();
			    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().hide();   
			    $('input[id=sqPadraoModeloDocumentoCam][value=42]').attr('checked', false);
			    $('input[id=sqPadraoModeloDocumentoCam][value=76]').attr('checked', false);
		    }
		    
		    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().hide();
		    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().hide();    
		    $('input[id=sqPadraoModeloDocumentoCam][value=41],input[id=sqPadraoModeloDocumentoCam][value=75]').click(function(){
		    	if($(this).is(':checked')){
		     	    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().show();
		     	    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().show();   
		    	}else{
		    	    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().hide();
		    	    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().hide();   
		    	    $('input[id=sqPadraoModeloDocumentoCam][value=42]').attr('checked', false);
		    	    $('input[id=sqPadraoModeloDocumentoCam][value=76]').attr('checked', false);  
		    	}
		    });
		    
		    if ( $('input[id=sqPadraoModeloDocumentoCam][value=41],input[id=sqPadraoModeloDocumentoCam][value=75]').is(':checked') ){
			    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().show();
			    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().show();   
		    }else{
			    $('input[id=sqPadraoModeloDocumentoCam][value=42]').parent().parent().hide();
			    $('input[id=sqPadraoModeloDocumentoCam][value=76]').parent().parent().hide();   
			    $('input[id=sqPadraoModeloDocumentoCam][value=42]').attr('checked', false);
			    $('input[id=sqPadraoModeloDocumentoCam][value=76]').attr('checked', false);
		    }
		}
}


