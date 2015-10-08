var tipo = [];
MinutaPasso = {

    deletar: function(codigo){
        var callBack = function(){
            window.location = '/artefato/minuta-eletronica/delete/id/' + codigo;
        }

        Message.showConfirmation({
            'body': 'Confirma a exclusão?',
            'yesCallback': callBack
        });
    },

    txTextoArea: function() {
    
        var element = $('.txTextoArtefato');
        var wysihtml5 = element.data('wysihtml5');
        var body = $(wysihtml5.editor.composer.iframe).contents().find('body');

        
        var toggle = (function() {

            var spellchecker = null;
            var funcao = "";
            
            function create() {
                funcao = 'create';
                word = $(wysihtml5.editor.composer.iframe).contents().find('body').text();
//            alert(word);
                $('.btSpellchecker').css('border', 'solid 1px red');
                body = $(wysihtml5.editor.composer.iframe).contents().find('body');

        		spellchecker = new $.SpellChecker(body, {
        				lang: 'pt_BR',
        				parser: 'html',
        				webservice: {
        				path: "/auxiliar/corretor-ortografico/index",
        				driver: 'pspell'
        			},
        	        local: {
        	            requestError: 'Houve um erro ao processar a requisição.',
        	            ignoreWord: 'Ignorar palavra',
        	            ignoreAll: 'Ignorar todas',
        	            ignoreForever: 'Adicionar ao dicionário',
        	            loading: 'Carregando...',
        	            noSuggestions: '(Não há sugestão)'
        	        },
        	        
        			suggestBox: {
        			      position: 'below'
        			}
        		});     
        		 
        		spellchecker.check();
            }
            
            function destroy() {
                funcao = 'destroy';
                $('.btSpellchecker').removeAttr('style');
        		spellchecker.destroy();
        		spellchecker = null;
            }
             
            function toggle() {
              var body2 = $(wysihtml5.editor.composer.iframe).contents().find('body');
                  
                  body2.on("paste", function(){
                      if(funcao == 'create'){
                          if(spellchecker){
                              var div = $(wysihtml5.editor.composer.iframe).contents().find('div');
                              if($(div).length > 0)
                              {
                                    $('.spellchecker-suggestbox').remove();
                                    $('.btSpellchecker').removeAttr('style');
                                    spellchecker.destroy();
                                    spellchecker = null;
                              }
                          }
                      }
                  });
        		(!spellchecker) ? create() : destroy();
            }
             
            return toggle;
         }
      )();
    
    wysihtml5.toolbar.find('[data-wysihtml5-command="spellcheck"]').click(toggle);
    
    },
    
    initCampo: function (){
    	
        //MinutaPasso.txTextoArea();
        
    	$('.current-font').parent().parent().click(function(){
    		$(this).removeClass('open');
    	});
    	
    	var interessado  =  false;
    	var rodape		  = false;
    	var tratamento	  = false;
    	var destinatario  = false;
    	var logo = false;
    	var params = {
    	        sqTipoDocumento : $('#sqTipoDocumento').val() ,
    	        sqAssunto : $('#sqAssunto').val(),
    	        sqModeloDocumento : $('#sqModeloDocumento').val()
    	    };
    	    var arrCampoModeloDocumento = MinutaPasso.getCampoModeloDocumento(params);
    	    var j = $.parseJSON(arrCampoModeloDocumento);
    	    var abaAssinaturaMultipla = false;
    	    var abaAssinaturaUnica = false;

    	    if(j.length > 0){
    	        $.each(j, function(i) {
    	        	if($('#sqTipoVisualizacao').val()){
        	    		$('#divTitulo').text('Alterar Minuta '+ j[i].noPadraoModeloDocumento);
        	    		$('#divEdit').val('1');
    	        	}else{
    	        		$('#divTitulo').text('Cadastrar Minuta '+ j[i].noPadraoModeloDocumento);
    	        		$('#divEdit').val('0');
    	        	}

    	    		if (j[i].noCampo == 'Tratamento'){
    	    			tratamento = true;
    	    		}

    	    		if (j[i].noCampo == 'Rodapé'){
    	    			rodape = true;
    	    			logo = false;
    	    		}
    	    		
    	    		if ((j[i].noCampo == 'Logo no Rodapé') && !rodape) {
    	    			rodape = true;
    	    			logo = true;
    	    		}	

    	    		if (j[i].noCampo == 'Destino Interno'){
    	    			destinatario = true;
    	    			$('.abaDestExterno').remove();
    	    		}
    	    		if (j[i].noCampo == 'Destino Externo'){
    	    			destinatario = true;
    	    			$('.abaDestInterno').remove();
    	    		}
    	    		if (j[i].noCampo == 'Múltiplas Assinaturas' || j[i].noCampo == 'Assinatura Hierárquica' ){
    	    			abaAssinaturaMultipla = true;
    	    			abaAssinaturaUnica = false;
    	    		}
    	    		if(j[i].noCampo == 'Assinatura'){
    	    			if(!abaAssinaturaMultipla){
    	    				abaAssinaturaMultipla = false;
    	    				abaAssinaturaUnica = true;
    	    			}
    	    		}
    	            $('.dv'+j[i].sqGrupoCampo+'-'+j[i].noColunaTabela).show();
    	            if (j[i].inObrigatorio) {
    	                $('#'+j[i].noColunaTabela).addClass('required');
    	            }
    	            if (j[i].noPadraoModeloDocumento != 'Padrão Atos'){
    	            	 //$('.cargoInternoAto').remove();
    	            	 interessado = true;
    	            }else{
    		    		$('.abaDestInterno').remove();
    		    		$('.abaDestExterno').remove();
    		    		$('.abaRodape').remove();
    		    		$('#botao_aba5').remove();
    	            	$('.dv2-txPosVocativo').show();
   	            	 	interessado = false;
    	            }
    	            tipo[j[i].sqGrupoCampo] = 1;
    	        });
    	    }

    	    if (!rodape){
	    		$('.abaRodape').remove();
	    		$('#botao_aba6').remove();
    	    }

    	    if(logo){
    	    	$('.dv5-coCep').remove();
    	    }
    	    
    	    if (!abaAssinaturaUnica && !abaAssinaturaMultipla){
	    		$('#botao_aba3').remove();
	    		$('.abaAssinatura').remove();
	    		$('.abaAssinaturaUnica').remove();
    	    }

    	    if (!abaAssinaturaUnica){
	    		$('.abaAssinaturaUnica').remove();
    	    }
    	    if (!abaAssinaturaMultipla){
	    		$('.abaAssinatura').remove();
    	    }

    	    if (!tratamento){
    	    	$('.tpDestinatario').show();
    	    }
    	    if (!interessado){
    	    	$('#botao_aba7').remove();
	    		$('.abaInteressado').remove();
    	    }  else{
    	    	$('#botao_aba7').show();
    	    }
    	    if (!destinatario){
	    		$('#botao_aba5').remove();
    	    	$('.abaDestInterno').remove();
    	    	$('.abaDestExterno').remove();
    	    }
    	    for (i in tipo) {
    	    	$('#botao_aba' + i).show();
    	    }

        $('.btn-concluir').click(function(){
            MinutaPasso.validaTexto();
        });
    },

    initButton: function (){
    	$('#cancelar').click(function() {
    	    if ($('#divEdit').val() == '0') {
	            $.post('/artefato/artefato/delete', {
	            	sqArtefato: $('#sqArtefato').val()
	            },
	            history.back());
    	    } else {
    	    	history.back()
    	    }
    	});
    },

    init: function () {
//    	$('.textarea').wysihtml5();
	    $('#sqPessoa').simpleAutoComplete("/artefato/pessoa/search-pessoa", {
	        extraParamFromInput: '#extra',
	        attrCallBack: 'rel',
	        autoCompleteClassName: 'autocomplete',
	        selectedClassName: 'sel'
	    });

	    $('#sqPessoaRodape').simpleAutoComplete("/artefato/pessoa/search-pessoa/extraParam/4", {
	        extraParamFromInput: '#extra',
            attrCallBack: 'rel',
	        autoCompleteClassName: 'autocomplete',
	        selectedClassName: 'sel'
	    });
	    
	    $('#sqEstado').change(function(){
	    	 $('#sqMunicipio_hidden,#sqMunicipio').val('');
	    });	
//        function callBack(par) {
//            $("#coCepRodape").val(par['coCep']);
//            $("#txEnderecoRodape").val(par['txEndereco']);
//            $("#txTelefoneRodape").val(par['txTelefone']);
//            $("#txEmailRodape").val(par['txEmail']);
//        }

	    $('#sqMunicipio').simpleAutoComplete("/auxiliar/municipio/search-municipio", {
	        extraParamFromInput: '#sqEstado',
	        attrCallBack: 'rel',
	        autoCompleteClassName: 'autocomplete',
	        selectedClassName: 'sel'
	    });
    },
    getCampoModeloDocumento: function(params) {
        return $.ajax({
            type: 'post',
            url: '/artefato/minuta-eletronica/get-campo-modelo-documento',
            data: params,
            async: false,
            global: false
        }).responseText;
    },
    initTelefone: function(){
    	

    	$('#txTelefoneRodape, #nuDdd').change(function(){
            var ddd = $('#nuDdd').val();

            if (ddd.toString().substr(-2) == '11') {
                $("#txTelefoneRodape").setMask("99999-9999");
            } else {
                $("#txTelefoneRodape").setMask("9999-9999");
            }
            if ($("#txTelefoneRodape").val().length < 8) {
            	$("#txTelefoneRodape").val('');
            }
        });
    },
//    getCep : function(cep){
//        $.ajax({
//            url  : '/artefato/minuta-eletronica/search-endereco-cep',
//            data : {
//                cep : cep
//            },
//            type : 'post',
//            dataType : 'json',
//            success : function(data){
//                $("#txEndereco").val(data.logradouro);
//            }
//        })
//    },
//    getCepRodape : function(cep){
//        $.ajax({
//            url  : '/artefato/minuta-eletronica/search-endereco-cep',
//            data : {
//                cep : cep
//            },
//            type : 'post',
//            dataType : 'json',
//            success : function(data){
//                $("#txEnderecoRodape").val(data.logradouro);
//            }
//        })
//    },

    initResponsavelRodape: function(){
        $('#sqPessoaRodape').blur(function(){
            if ($('#sqPessoaRodape_hidden').val()){
                if($('#sqPessoaRodape').valid()){
                    var arrDados = MinutaPasso.getDados($('#sqPessoaRodape_hidden').val(),4);
                    MinutaPasso.populatePessoaRodape(arrDados);
                }
            }
        });
    },

    getDados: function(sqPessoa,sqTipoPessoa){
        var result = $.ajax({
            type: 'post',
            url: '/artefato/pessoa/get-pessoa-dados-rodape',
            data: 'sqPessoa='+sqPessoa+'&sqTipoPessoa='+sqTipoPessoa,
            async: false,
            global: false
            }).responseText;
        return result;
    },

    populatePessoaRodape: function(data){
    	var j = $.parseJSON(data);
        if(j.length > 0){
          $.each(j, function(i) {
          	$('#coCepRodape').val(j[i].coCep);
        	$('#txEnderecoRodape').val(j[i].txEndereco);
        	$('#txTelefoneRodape').val(j[i].txTelefone);
        	$('#txEmailRodape').val(j[i].txEmail);
        	$('#nuDdd').val(j[i].nuDdd);
          })
      } else {
		$('#coCepRodape').val('');
		$('#txEnderecoRodape').val('');
		$('#txTelefoneRodape').val('');
		$('#txEmailRodape').val('');
		$('#nuDdd').val('');
      }
    },
    validaTexto: function(){
        if($('#txTextoArtefato').val()){
            $('#txTextoArtefatoHidden').attr('checked',true);
            $('#txTextoArtefatoHidden').removeClass('required');
            $('.dv2-txTextoArtefato').removeClass('error');
        }else{
            $('#txTextoArtefatoHidden').attr('checked',false);
            $('#txTextoArtefatoHidden').addClass('required');
        }
    }
}

DestinatarioExterno = {
	deletar : function(sqArtefato,sqPessoaSgdoce,sqPessoaFuncao) {
		$.post('/artefato/pessoa/delete-destinatario', {
	        sqArtefato: sqArtefato,
	        sqPessoaSgdoce: sqPessoaSgdoce,
	        sqPessoaFuncao: sqPessoaFuncao
	    }).done(function() {
	        DestinatarioExterno.reloadGrid()
        });
    },
    reloadGrid: function() {
        $('#table-destinatario-externo').dataTable().fnDraw(false);
        ModalMinuta.fnDrawCallback();
    }
}
Assinatura = {
    deletar: function(codigo){
        $.post('/artefato/pessoa/delete-assinatura', {
            sqPessoaUnidadeOrg: codigo
            ,sqArtefato: $('#sqArtefato').val()
        }).done(function(){
            Assinatura.reloadGrid();
        });
    },
    reloadGrid: function() {
        $('#table-assinatura').dataTable().fnDraw(false);
    }
}
//getCepRodape : function(cep){
//$.ajax({
//  url  : '/artefato/minuta-eletronica/search-endereco-cep',
//  data : {
//      cep : cep
//  },
//  type : 'post',
//  dataType : 'json',
//  success : function(data){
//      $("#txEnderecoRodape").val(data.logradouro);
//  }
//})
//},
var iterator = 0;
$(document).ready(function(){
	if(!iterator) {
		MinutaPasso.initCampo();
		if(!$('#sqTipoVisualizacao').val()){
		    	$(document).ajaxStop(function() {
		    		if(!$('#sqResponsavel').val()){
		    			if (!$('#pessoaLogada').is(':disabled')) {
                            $('#pessoaLogada').trigger('click');
                        }else if (!$('#chefeSetor').is(':disabled')){
                            $('#chefeSetor').trigger('click');
                        }else if (!$('#chefeSubstituto').is(':disabled')){
                            $('#chefeSubstituto').trigger('click');
                        }
		    		}
		    	});
		}	
		MinutaPasso.init();
		MinutaPasso.initButton();
		MinutaPasso.initResponsavelRodape();
		MinutaPasso.initTelefone();
		
		iterator++;
	}
});
