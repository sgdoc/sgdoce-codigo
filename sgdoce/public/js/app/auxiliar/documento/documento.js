Documento = {
    initGrid: function() {
    	// Verifica se o objeto Grid já foi executado...
//    	window.Grid = !window.Grid ? null : window.Grid;
        if($('#sqPessoa-documento', $('#form-documento')).val()) {
            Grid.loadNoPagination($('#form-documento'), $('#table-documento'));
//        	var timer = window.setInterval(function() {
//        		if(Grid) {
        			
//        			window.clearInterval(timer);
//        		}
//        	}, 50);
        }
    },

    adicionar: function(){
        $('#btn-adicionar-documento').off('click').on('click', function(){
            $.get(
        		'/auxiliar/documento/create',
        		{
                    sqPessoa       : $('#sqPessoa-documento').val(),
                    sqPessoaSgdoce : $('#sqPessoaSgdoce-documento').val()
        		},
        		function(data) {
        			$('#modalDocumento').html(data).modal('show');
        		}
    		);
        });
    },

    alterar: function(sqDocumento, sqTipoDocumento, sqPessoa) {
        $.get('/auxiliar/documento/edit', {
            id: sqTipoDocumento,
            sqPessoa: sqPessoa,
            sqDocumento : sqDocumento,
            sqTipoDocumento: sqTipoDocumento
        }, function(data){
            $('#modalDocumento').html(data).modal();
        });
    },
    
    visualizar : function(sqAnexoComprovanteDocumento, sqPessoa) {
        $.get('/auxiliar/documento/view-image', {
            sqPessoaSgdoce: sqPessoa,
            sqAnexoComprovanteDocumento : sqAnexoComprovanteDocumento
        }, function(data){
            $('#modalDocumento').html(data).modal();
        });
    },

    deletar: function(sqDocumento, sqTipoDocumento, sqPessoa, sqPessoaSgdoce) {
        var callBack = function(){
            var config = {
                url: '/auxiliar/documento/delete',
                type: 'post',
                data: {
                    sqPessoaSgdoce: sqPessoaSgdoce, 
                    sqPessoa: sqPessoa,
                    sqDocumento : sqDocumento,
                    sqTipoDocumento: sqTipoDocumento
                },
                dataType: 'json',
                success: function(response){
					$('#form-documento').submit();
					$('#modalDocumento').modal('hide');
					
					if(response) {
						Message.showMessage(response);
					}
                }
            };

            $.ajax(config);
        }

        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },
    
    deletarImagem : function(sqDocumento, sqTipoDocumento, sqPessoaSgdoce) {
        var callBack = function(){
            var config = {
                url: '/auxiliar/anexo-comprovante-documento/delete',
                type: 'post',
                data: {
                    sqPessoaSgdoce: sqPessoaSgdoce,
                    sqDocumento : sqDocumento,
                    sqTipoDocumento: sqTipoDocumento
                },
                dataType: 'json',
                success: function(response){
                    $('#form-documento').submit();
                    $('#modalDocumento').modal('hide');
                    
                    if(response) {
                        Message.showMessage(response);
                    }
                }
            };

            $.ajax(config);
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },

    init: function(){
        Documento.initGrid();
        Documento.adicionar();
    }
};

$(document).on('ready', function() {
	Documento.init();
});