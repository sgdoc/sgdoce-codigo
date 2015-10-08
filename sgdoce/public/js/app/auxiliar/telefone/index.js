Telefone = {
    initGrid: function(){
    	if($('#sqPessoa', $('#form-telefone')).val()) {
    		Grid.loadNoPagination($('#form-telefone'), $('#table-telefone'));
    	}
    },

    adicionar: function() {
        $('#btn-add-telefone').off('click').on('click', function() {
            $.get('/auxiliar/telefone/create', {
                    sqPessoa: $('#sqPessoa').val()
                }, function(data){
                    $('#modalContatoTelefone').html(data).modal();
                }
            );
        });
    },
    
    alterar: function(id, sqPessoa){
        $.get('/auxiliar/telefone/edit', {
            id: id, 
            sqPessoa: sqPessoa
        }, function(data){
            $('#modalContatoTelefone').html(data).modal();
        });
    },

    deletar: function(codigo){
        var callBack = function(){
            PessoaForm.saveFormWebService('app:VwTelefone', 'libCorpDeleteTelefone',
                [{
                    name: 'sqTelefone',
                    value: codigo
                }],
                $('#form-telefone'));
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
        
    },

    init: function(){
        Telefone.initGrid();
        Telefone.adicionar();
    }

}

$(document).on('ready', function() {
    Telefone.init();
});