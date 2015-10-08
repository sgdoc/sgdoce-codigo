ListaPeca = {
	
    gridPeca: function(){
        Grid.load('/artefato/autuar-processo/list-peca-processo/sqArtefato/'+$('#sqArtefato').val(), $('#table-peca-processo'));
    },
	    
	reloadGridPeca: function() {
        $('#table-peca-processo').dataTable().fnDraw(false);
    },

    ListGridImagens: function(){
        $.get('artefato/imagem/list',{
                id: $('#sqArtefato').val(),
                obrigatoriedade: false
            },
            function(data){
                $('#dadosImagem').html(data);
                $('.thumbnail').css('height', 276);
            });
    },

	deletar:function(sqArtefatoVinculo,nuDigital,original){
	        var callBack = function() {
	            $.get('artefato/processo-eletronico/delete-peca/id/' + sqArtefatoVinculo +'/sqArtefato/'+$('#sqArtefato').val() + '/inOriginal/'+original 
	            , function(data){
	            	if(data == 'true'){
		            	ListaPeca.reloadGridPeca();
                        ListaPeca.ListGridImagens();
		                Message.showSuccess(UI_MSG['MN013']);
	            	}else if (data == 'false'){
	            		var msg = 'Não é permitido a exclusão de todas as digitais do processo. Ao menos uma digital deve está inserida no processo.';
		                Message.showSuccess(msg);
	            	}
	            });
	        }
	        var msg;
	        switch (original) {
			case '':
		        msg = 'A digital: '+ nuDigital +' é cópia e será excluída. Deseja excluir a cópia?';
				break;
			case '1':
		        msg = 'Tem certeza que deseja retirar a digital '+ nuDigital +'?';
				break;
			}
	        Message.showConfirmation({	        	
	            'body': msg,
	            'yesCallback': callBack
	        });
	}
}

TemaTratado = {
       
	grid : function()
    {
        Grid.load(
            '/artefato/autuar-processo/list-tema-tratado/sqArtefato/'+$('#sqArtefato').val(), $('#table-tema-tratado')
            );
    },

    deletar: function(sqArtefato,codigo,sqTemaVinculado){    	
        var callBack = function(){
            $.post('/artefato/autuar-processo/delete', {
            	sqArtefato: sqArtefato,
            	sqNomeEspecifico: codigo,
            	sqTemaVinculado:  sqTemaVinculado
            });
            TemaTratado.reloadGrid();
            Message.showSuccess(UI_MSG['MN013']);
        }

        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },
    reloadGrid: function() {
        $('#table-tema-tratado').dataTable().fnDraw(false);
        
    }
}

var ProcessoEletronico = {
    init: function(){
        sessionStorage.setItem('origemExterna', $('#chekProcedenciaExterno').is(':checked'));
        if (sessionStorage.getItem('origemExterna') == 'true') {
            $('#dtPrazo').removeClass('required');
        } else {
            $('#dtPrazo').addClass('required');
        }

        $('input[name=procedenciaInterno]').click(function () {
            if ($('#chekProcedenciaExterno').is(':checked')) {
                sessionStorage.setItem('origemExterna', 'true');
                $('#dtPrazo').removeClass('required');
            } else {
                sessionStorage.setItem('origemExterna', 'false');
                $('#dtPrazo').addClass('required');
            }
        });

    	if($("#nuArtefato").val()){
    		$("#nuArtefato").attr('maxlength','20').unsetMask().setMask('99999.999999/9999-99');
    	}else{
    		$("#nuArtefato").attr('maxlength','17').unsetMask().setMask('99999.999999/9999');
    	}
    	
    	$('.btn-concluir').click(function() {
            $('.tab').each(function(){
                $(this).click();
                if ($('#sqPrazo').val() == '2' && $('#inDias').val() == '2' && $('#nuDiasPrazo').val() == '') {
                    $('#nuDiasPrazo').val('30');
                }
                if($('form').valid()){
                    valid = true;
                    sessionStorage.clear();
                }else{
                    valid = false;
                }
            });	            

        	if(valid) {
            	$('#CoAmbitoFederal').removeAttr('disabled');
            	var nuArtefato  = $("#nuArtefato").val();
            	nuArtefato = nuArtefato.replace(/(\.|\/|-|,)/g, '');
            	
                $.post('/artefato/autuar-processo/save-capa/nuArtefatoMascara/'+ nuArtefato,
                		$('.form-processo-eletronico-principal').serialize(),
                		function(data){
                	        Message.showConfirmation({
                	        	'body': 'Gerar Capa do Processo',
                	        	'yesCallback' : function() {
                	        		$("#modal-capa-processo").load('/artefato/autuar-processo/capa-processo/sqArtefato/'+$('#sqArtefato').val()+'/autuar/true/artefatoPai/'+$('#artefatoPai').val()).modal();
                	        	},
                	        	'noCallback' : function()  {
                	        		
                	        	}
        	        		});
                });     
            	return false;        		
        	}
    	});
        
		$('#cancelarForm').click(function() {
		    if ($('#divEdit').val() == '0') {
	            $.post('/artefato/artefato/delete', {
	                sqArtefato: $('#sqArtefato').val()
	            },function(data){
		    		switch ($('#redirect').val()) {
					case '2':
					    window.location = '/artefato/consultar-artefato/consultar-artefato-padrao/update/3/view/1';
						break;
					case '3':
//					    window.location = '/artefato/visualizar-artefato/index/sqArtefato/' + $('#sqArtefato').val() +'/update/3/view/1';
						history.back();
						break;
					}
	            })
		    }
		});
    	
    	TemaTratado.grid();
    	ListaPeca.gridPeca();
		switch ($('#CoAmbitoFederal').val()) {
		case 'F':
			$('.divEstado').hide();
			break;
        case 'E' :
        case 'M' :
			$('.divEstado').show();
			break;
		default:
			break;
		}
    	$('#CoAmbitoFederal').change(function() {
    		switch ($('#CoAmbitoFederal').val()) {
			case 'F':
				$('.divEstado').hide();
				break;
            case 'E' :
            case 'M' :
				$('.divEstado').show();
				break;
			default:
				break;
			}
    	});
    	
//        $('#btn-concluir').click(function(){
//            var callBack = function(){
//                $.get('/artefato/autuar-processo/visualizar-capa', {
//                	sqArtefato: sqArtefato
//                });
//            }
//
//            Message.showConfirmation({
//                'body': 'Deseja Visualizar a Capa do Processo ? ',
//                'yesCallback': callBack
//            });
//            return false;
//        });
        
        $('#btnAdicionarTema').click(function(){
//            $("#modal-tema-tratado").show();
            $("#modal-tema-tratado").load('/artefato/autuar-processo/modal-tema-tratado/sqArtefato/'+$('#sqArtefato').val()).modal();
        });
        
        $('#btnAdicionarPeca').click(function(){          	
        	$("#sqTipoDocumento").val('');
        	$("#nuArtefatoVinculacaoPeca").val('');
        	$("#nuDigital").val('');
        	$("#modal-peca-processo").load('/artefato/autuar-processo/modal-peca-processo/sqArtefato/'+$('#sqArtefato').val()).modal();
        });
      
        
    	$('.TipoPrioridade').hide();
        $('#sqPrioridade').change(        		
            function() {
                $('.TipoPrioridade').show();
                if ($('#sqPrioridade').val() != '') {
                    $('#divTipoPrioridade').load(
                            '/auxiliar/tipo-prioridade/combo-tipo-prioridade/sqPrioridade/'
                                    + $('#sqPrioridade').val());
                }else{
                	$('#sqTipoPrioridade').val('');
                	 $('.TipoPrioridade').hide();
                }
        });
        
        if($('#sqPrioridade').val()){
        	 $('.TipoPrioridade').show();
        }
        
        if ($('#dtPrazo').val()){
            $('#sqPrazo').val('1');
        }
        if ($('#nuDiasPrazo').val()){
            $('#sqPrazo').val('2');
        }
        ProcessoEletronico.tipoData();
        $('#sqPrazo').change(function(){
            ProcessoEletronico.tipoData();
        });        

        ProcessoEletronico.diaCorrido();
        $('#inDias').change(function(){
            ProcessoEletronico.diaCorrido();
        });
        
    },

    diaCorrido: function(){	    	
        switch ($('#inDias').val()) {
        case '1':
            $('#nuDiasPrazo').addClass('required');
            $('#inDiasCorridos').val(false);
            $('#sqPrazo').val('2');
            $('.dvDiasPrazo').removeClass('hidden');
            $('.dv2-dtPrazo').removeClass('hidden');
            break;
        case '2':
            if (sessionStorage.getItem('origemExterna') == 'true'){
                $('#nuDiasPrazo').removeClass('required');
            }
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
            if (sessionStorage.getItem('origemExterna') == 'true') {
                $('#dtPrazo').removeClass('required');
            } else {
                $('#dtPrazo').addClass('required');
            }

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
    
    reloadDivImagem : function() {
        $('#dadosImagem').html('');
        ProcessoEletronico.assingContentImage();
    },

    assingContentImage : function() {
    	if($('#sqArtefato').val()){
            $.get('artefato/imagem/list',{
                id: $('#sqArtefato').val(),
                obrigatoriedade: true
                },
            function(data){
                $('#dadosImagem').html(data);
                $('.thumbnail').css('height', 276);
            });
    	}
    }

};
$(document).ready(function(){
	ProcessoEletronico.init();
	ProcessoEletronico.assingContentImage();
    
 	$('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
 	});
});