Endereco = {
    initGrid: function(){
    	if($('#sqPessoa', $('#form-endereco')).val()) {
    		Grid.loadNoPagination($('#form-endereco'), $('#table-endereco'));
    	}
    },
    
    adicionar: function(){
    	$('#btn-add-endereco').off('click');
        $('#btn-add-endereco').on('click', function() {
            $.get('/auxiliar/endereco/create', {
                sqPessoa: $('#sqPessoa').val()
            }, function(data){
                $('#modalEndereco').html(data).modal();
            });
        });
    },

    alterar: function(id, sqPessoa){
        $.get('/auxiliar/endereco/edit', {
            id: id, 
            sqPessoa: sqPessoa,
            sqPessoaSgdoce: $('#sqPessoaSgdoce').val()
        }, function(data){
            $('#modalEndereco').html(data).modal();
        });
    },
    
    visualizar : function(sqAnexoComprovante, sqPessoaSgdoce) {
        $.get('/auxiliar/endereco/view-image', {
            sqPessoaSgdoce: sqPessoaSgdoce,
            sqAnexoComprovante : sqAnexoComprovante
        }, function(data){
            $('#modalEndereco').html(data).modal();
        });
    },

    deletar: function(sqEndereco, sqEnderecoSgdoce) {
        var callBack = function() {
            $.post('/auxiliar/endereco/delete-endereco', {
                sqEndereco : sqEndereco,
                sqEnderecoSgdoce : sqEnderecoSgdoce
            }, function(response) {
                $('#form-endereco').submit();
                $('#modalEndereco').modal('hide');
                
                if(response) {
                    Message.showMessage(response);
                }
            });
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },
    
    deletarImagem : function(sqAnexoComprovante) {
        var callBack = function(){
            var config = {
                url: '/auxiliar/anexo-comprovante/delete',
                type: 'post',
                data: {
                    sqAnexoComprovante : sqAnexoComprovante
                },
                dataType: 'json',
                success: function(response) {
                    $('#form-endereco').submit();
                    $('#modalEndereco').modal('hide');
                    
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
        Endereco.initGrid();
        Endereco.adicionar();
    }
}

$(document).on('ready', function(){
    Endereco.init();
});
