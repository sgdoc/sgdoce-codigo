Dossie = {
    exibirVinculacao: function(sqArtefato){
        $("#modalVinculacao").load('/artefato/dossie/modal-vinculacao/sqArtefato/' + sqArtefato ).modal();
    },

    exibirMaterial: function(sqArtefato){
        $("#modalMaterial").load('/artefato/dossie/modal-material/sqArtefato/' + sqArtefato ).modal();
    },

    exibirImagens: function(sqArtefato){
        $("#modalImagens").load('/artefato/dossie/modal-imagens/sqArtefato/' + sqArtefato ).modal();
    },

    exibirDocumentos: function(sqArtefato){
        $("#modalDocumentos").load('/artefato/dossie/modal-documentos/sqArtefato/' + sqArtefato ).modal();
    },
    validaNuDigital: function(){
        if(!$('#nuDigital').val()){
            $(location).attr('href','artefato/dossie/create/nuDigital/0');
            
            return false;
        }
        
        $.ajax({
            data :{'nuDigital' : $('#nuDigital').val()},
            type: "POST",
            dataType : "json",
            url : '/artefato/dossie/validar-digital',
            success : function(data) {
                if(data == 1){
                    $('#mensagemSucesso').modal();
                }
            }
        });
    },
    
    deletarVinuloDocumento:function(sqArtefatoVinculo){
        var callBack = function() {
            $.get('artefato/dossie/delete/id/' + sqArtefatoVinculo , function(data){
                Message.showSuccess(UI_MSG['MN013']);
                $('#table-dados-documentos').dataTable().fnDraw(false);
            })
        }
        Message.showConfirmation({
            'body': UI_MSG['MN018'],
            'yesCallback': callBack
        });
    },
    
    grid : function() {
        Grid.load('/artefato/dossie/list-documentos/sqArtefato/'+$('#sqArtefato').val(), $('#table-dados-documentos'));
    },
    
    reloadDivImagem : function() {
        $('#dadosImagem').html('');
        ProcessoDoc.assingContentImage();
    },

    assingContentImage : function() {
        $.get('artefato/imagem/list',{
            id: $('#sqArtefato').val(),
            obrigatoriedade: false
            },
        function(data){
            $('#dadosImagem').html(data);
            $('.thumbnail').css('height', 276);
        });
    },

    verificaDuplicidade:function() {
        var params = {
            tipo: $('#sqTipoDocumento_hidden').val(),
            numero: $('#nuArtefato').val(),
            origem: $('#sqPessoaIcmbio_hidden').val()
        };
        if ($('#nuArtefato').val()) {
            $.post('artefato/dossie/verifica-duplicidade',params,function(data){
                if (data.success) {
                    $('#tipo').val($('#sqTipoDocumento').val());
                    $('#origem').val($('#sqPessoaIcmbio').val());
                    $('#numero').val($('#nuArtefato').val());
                    $('#modalDuplicidade').modal();
                    $('#btnProximo,.btn-concluir').attr('disabled',true);
                } else {
                    $('#btnProximo,.btn-concluir').removeAttr('disabled');
                }
            });
        }
    }
}

$(document).ready(function(){
    $('#nuArtefato').blur(function(){
       Dossie.verificaDuplicidade();
    });
    $('#btnFecharDuplicidade').click(function(){
        $('#nuArtefato').val('');
        $('#modalDuplicidade').modal('hide');
    });
    $('#sqGrauAcesso').val(1);
    sqArtefato = $('#sqArtefato').val();
    Dossie.grid();
    Dossie.assingContentImage();
    //Auto Complet's
    $('#sqPessoaIcmbio').simpleAutoComplete("/artefato/dossie/search-unidade");
    $('#assinatura').simpleAutoComplete("/artefato/dossie/find-assinatura");
    $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto", {
        extraParamFromInput: '#sqAssunto',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

    $('#assinatura').blur(function(){
        if($('#assinatura').val() == '') {
            $('#noCargoAssinante').val('');
            return false;
        }

        $.ajax({
            async : false,
            data : {
                'sqPessoaSgdoce' : $('#assinatura_hidden').val()
            },
            type: "POST",
            dataType : "json",
            url : '/artefato/pessoa/recupera-cargo-pessoa',
            success : function(response){
                $('#noCargoAssinante').val(response.noCargo);
            }
        });
    });

    //Modal
    $('#btnModalVinculacao').click(function() {
        Dossie.exibirVinculacao(sqArtefato);
    }),

    $('#btnModalMaterial').click(function() {
        Dossie.exibirMaterial(sqArtefato);
    }),

    $('#btnModalImagens').click(function() {
        Dossie.exibirImagens(sqArtefato);
    }),

    $('#btnModalDocumentos').click(function() {
        Dossie.exibirDocumentos(sqArtefato);
    }),
    
    $('#cancelar').click(function() {
	    if ($('#divEdit').val() == '0') {
            $.post('/artefato/documento/delete', {
                sqArtefato: $('#sqArtefato').val()
            },
		    window.location = '/artefato/dossie/index');
	    }else {
	    	switch ($('#redirect').val()) {
			case '2':
			    window.location = '/artefato/consultar-artefato/consultar-artefato-padrao';
				break;
			case '3':
			    window.location = '/artefato/visualizar-artefato/index/sqArtefato/' + $('#sqArtefato').val();
				break;
			}
	    }
	});
    
    $('.btn-concluir').click(function() {    	
	    $('.tab').each(function(){
	        $(this).click();
	        if($('form').valid()){
	            valid = true;
	        }else{
	            valid = false;
	        }
	    });

		if(valid) {
			$.post('/artefato/dossie/save/',
			$('#formDossie').serialize(),
			function(){
				if($('#divEdit').val() == 0){
                    window.location = '/artefato/visualizar-artefato/index/sqArtefato/'+$('#sqArtefato').val() +'/update/0/view/1';
//                    window.location = '/artefato/area-trabalho/index/tipoArtefato/3';
                }else{
			    	switch ($('#redirect').val()) {
					case '2':
					    window.location = '/artefato/consultar-artefato/consultar-artefato-padrao/update/1/view/1';
						break;
					case '3':
					    window.location = '/artefato/visualizar-artefato/index/sqArtefato/' + $('#sqArtefato').val() +'/update/1/view/1';
//					    window.location = '/artefato/area-trabalho/index/tipoArtefato/3';
						break;
					}
			    }
			});
		};     
		return false; 
	});
    
    $('#btValidar').click(function(){
        if($('#nuDigital').val() != ''){
            $('#nuDigital').val('');
            return false;
        }
    	Dossie.validaNuDigital();
    	return false;
    });
});

